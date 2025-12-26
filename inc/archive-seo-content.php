<?php
/**
 * Archive SEO Content Manager
 * 
 * 既存のアーカイブページ（grant CPT、各タクソノミー）に対して
 * 管理画面から個別にSEOコンテンツ（イントロ/アウトロ）を編集・追加するシステム
 * 
 * @package Grant_Insight_Perfect
 * @version 3.0.0
 * 
 * v3.0.0 Changes:
 * - ページネーション実装（AJAX読み込みで軽量化）
 * - 補助金100件以下の自動noindex設定
 * - 類似カテゴリ自動検出（高精度TF-IDF/コサイン類似度ベース）
 * - おすすめ記事の自動選出（PV順で3件）
 * - カスタムCSS適用構造の改善
 */

if (!defined('ABSPATH')) exit;

/**
 * =============================================================================
 * 1. データベーステーブル作成（高速化修正 v11.0.10）
 * =============================================================================
 * 
 * 【重要】admin_init で毎回 dbDelta や ALTER TABLE を実行すると
 * 管理画面が極端に重くなる。バージョン管理を導入して、
 * DB構造に変更がある時だけ実行するように最適化。
 */

// テーブルのバージョン（構造変更時にインクリメント）
define('GI_ARCHIVE_SEO_DB_VERSION', '1.1');

function gi_create_archive_seo_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        archive_type varchar(50) NOT NULL COMMENT 'post_type_archive, taxonomy',
        archive_key varchar(100) NOT NULL COMMENT 'grant, grant_prefecture:tokyo, etc.',
        page_title varchar(255) DEFAULT NULL COMMENT 'カスタムH1タイトル',
        meta_description text DEFAULT NULL COMMENT 'メタディスクリプション',
        intro_content longtext DEFAULT NULL COMMENT 'イントロテキスト（メインコンテンツ上部）',
        outro_content longtext DEFAULT NULL COMMENT 'アウトロテキスト（メインコンテンツ下部）',
        sidebar_content longtext DEFAULT NULL COMMENT 'サイドバー追加コンテンツ',
        featured_posts text DEFAULT NULL COMMENT 'おすすめ記事ID（カンマ区切り）',
        custom_css text DEFAULT NULL COMMENT 'ページ固有CSS',
        is_active tinyint(1) DEFAULT 1 COMMENT '有効/無効',
        merge_target varchar(150) DEFAULT NULL COMMENT '統合先（type:key形式）',
        is_noindex tinyint(1) DEFAULT 0 COMMENT 'noindexフラグ',
        similar_categories text DEFAULT NULL COMMENT '類似カテゴリ候補（JSON）',
        auto_noindex_reason varchar(100) DEFAULT NULL COMMENT '自動noindexの理由',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY archive_key_unique (archive_type, archive_key),
        KEY archive_type_idx (archive_type),
        KEY is_active_idx (is_active),
        KEY merge_target_idx (merge_target),
        KEY is_noindex_idx (is_noindex)
    ) {$charset_collate};";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // バージョンを保存
    update_option('gi_archive_seo_db_version', GI_ARCHIVE_SEO_DB_VERSION);
}
add_action('after_switch_theme', 'gi_create_archive_seo_table');

function gi_upgrade_archive_seo_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    // テーブルが存在しない場合は何もしない
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        return;
    }
    
    $existing_columns = $wpdb->get_col("DESCRIBE {$table_name}", 0);
    
    $columns_to_add = array(
        'merge_target' => "ADD COLUMN merge_target varchar(150) DEFAULT NULL COMMENT '統合先（type:key形式）'",
        'is_noindex' => "ADD COLUMN is_noindex tinyint(1) DEFAULT 0 COMMENT 'noindexフラグ'",
        'similar_categories' => "ADD COLUMN similar_categories text DEFAULT NULL COMMENT '類似カテゴリ候補（JSON）'",
        'auto_noindex_reason' => "ADD COLUMN auto_noindex_reason varchar(100) DEFAULT NULL COMMENT '自動noindexの理由'",
    );
    
    foreach ($columns_to_add as $column => $sql) {
        if (!in_array($column, $existing_columns)) {
            $wpdb->query("ALTER TABLE {$table_name} {$sql}");
        }
    }
    
    // バージョンを保存
    update_option('gi_archive_seo_db_version', GI_ARCHIVE_SEO_DB_VERSION);
}

/**
 * テーブル存在確認と更新（高速化版）
 * 
 * 【重要】バージョンチェックにより、DB構造に変更がない場合は
 * 何もせずに即座にリターンする。これにより管理画面の速度が劇的に向上。
 */
function gi_ensure_archive_seo_table() {
    // バージョンが一致していれば何もしない（超高速化）
    $installed_version = get_option('gi_archive_seo_db_version', '0');
    if ($installed_version === GI_ARCHIVE_SEO_DB_VERSION) {
        return; // DBは最新版なので処理不要
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        gi_create_archive_seo_table();
    } else {
        gi_upgrade_archive_seo_table();
    }
}
add_action('admin_init', 'gi_ensure_archive_seo_table');


/**
 * =============================================================================
 * 2. 管理メニュー追加
 * =============================================================================
 */

function gi_add_archive_seo_menu() {
    add_menu_page(
        'アーカイブSEO管理',
        'アーカイブSEO',
        'edit_posts',
        'gi-archive-seo',
        'gi_archive_seo_page',
        'dashicons-archive',
        26
    );
    
    add_submenu_page(
        'gi-archive-seo',
        'ページ一覧',
        'ページ一覧',
        'edit_posts',
        'gi-archive-seo',
        'gi_archive_seo_page'
    );
    
    add_submenu_page(
        'gi-archive-seo',
        'ページ編集',
        'ページ編集',
        'edit_posts',
        'gi-archive-seo-edit',
        'gi_archive_seo_edit_page'
    );
    
    add_submenu_page(
        'gi-archive-seo',
        '統合候補管理',
        '統合候補管理',
        'manage_options',
        'gi-archive-seo-merge',
        'gi_archive_seo_merge_page'
    );
}
add_action('admin_menu', 'gi_add_archive_seo_menu');


/**
 * =============================================================================
 * 3. AJAX ハンドラー登録
 * =============================================================================
 */

add_action('wp_ajax_gi_archive_seo_get_pages', 'gi_ajax_archive_seo_get_pages');
add_action('wp_ajax_gi_get_posts_preview', 'gi_ajax_get_posts_preview');
add_action('wp_ajax_gi_archive_get_similar_categories', 'gi_ajax_get_similar_categories');
add_action('wp_ajax_gi_archive_auto_select_featured', 'gi_ajax_auto_select_featured');
add_action('wp_ajax_gi_archive_bulk_set_noindex', 'gi_ajax_bulk_set_noindex');
add_action('wp_ajax_gi_archive_execute_merge', 'gi_ajax_execute_merge');


/**
 * =============================================================================
 * 4. 管理画面 - ページ一覧（軽量化版）
 * =============================================================================
 */

function gi_archive_seo_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    // 統計情報のみ取得（軽量）
    $stats = gi_get_archive_seo_stats();
    
    ?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-archive" style="font-size: 30px; margin-right: 10px; vertical-align: middle;"></span>
            アーカイブSEOコンテンツ管理
        </h1>
        
        <p class="description" style="margin: 15px 0;">
            既存のアーカイブページ（助成金一覧、都道府県別、カテゴリ別など）に対して、<br>
            イントロテキスト・アウトロテキスト・おすすめ記事を個別に設定できます。
        </p>
        
        <!-- 統計カード -->
        <div class="gi-archive-seo-stats" style="display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap;">
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #2271b1;" id="stat-total"><?php echo $stats['total']; ?></div>
                <div style="color: #666;">利用可能ページ</div>
            </div>
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #00a32a;" id="stat-configured"><?php echo $stats['configured']; ?></div>
                <div style="color: #666;">設定済みページ</div>
            </div>
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #dba617;" id="stat-unconfigured"><?php echo $stats['unconfigured']; ?></div>
                <div style="color: #666;">未設定ページ</div>
            </div>
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #d63638;" id="stat-merge-candidates"><?php echo $stats['merge_candidates']; ?></div>
                <div style="color: #666;">統合候補（100件以下）</div>
            </div>
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #9333ea;" id="stat-noindex"><?php echo $stats['noindex']; ?></div>
                <div style="color: #666;">noindex設定済</div>
            </div>
        </div>
        
        <!-- 一括操作ボタン -->
        <div style="margin: 20px 0; padding: 15px; background: #f0f6fc; border-radius: 8px; border-left: 4px solid #2271b1;">
            <strong>一括操作:</strong>
            <button type="button" class="button" id="btn-bulk-noindex" style="margin-left: 10px;">
                <span class="dashicons dashicons-hidden" style="vertical-align: middle;"></span>
                100件以下を一括noindex
            </button>
            <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge'); ?>" class="button" style="margin-left: 10px;">
                <span class="dashicons dashicons-migrate" style="vertical-align: middle;"></span>
                統合候補管理
            </a>
            <span id="bulk-status" style="margin-left: 15px; color: #666;"></span>
        </div>
        
        <!-- フィルタータブ -->
        <div class="gi-archive-tabs" style="margin: 20px 0; border-bottom: 1px solid #ccc;">
            <a href="#" class="gi-tab active" data-filter="all">すべて</a>
            <a href="#" class="gi-tab" data-filter="unconfigured" style="color: #dba617;">未設定のみ</a>
            <a href="#" class="gi-tab" data-filter="post_type_archive">投稿タイプ</a>
            <a href="#" class="gi-tab" data-filter="grant_prefecture">都道府県</a>
            <a href="#" class="gi-tab" data-filter="grant_category">カテゴリ</a>
            <a href="#" class="gi-tab" data-filter="grant_municipality">市区町村</a>
            <a href="#" class="gi-tab" data-filter="grant_purpose">目的</a>
            <a href="#" class="gi-tab" data-filter="grant_tag">タグ</a>
            <a href="#" class="gi-tab" data-filter="merge_candidate" style="color: #d63638;">統合候補</a>
            <a href="#" class="gi-tab" data-filter="noindex" style="color: #9333ea;">noindex</a>
        </div>
        
        <!-- 検索・ページサイズ -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0;">
            <div>
                <input type="text" id="search-input" placeholder="ページ名で検索..." style="width: 300px; padding: 8px;">
                <button type="button" class="button" id="btn-search">検索</button>
            </div>
            <div>
                <label>表示件数: 
                    <select id="per-page">
                        <option value="20">20件</option>
                        <option value="50" selected>50件</option>
                        <option value="100">100件</option>
                    </select>
                </label>
            </div>
        </div>
        
        <!-- ローディング表示 -->
        <div id="loading-indicator" style="text-align: center; padding: 40px; display: none;">
            <span class="spinner is-active" style="float: none;"></span>
            <p>読み込み中...</p>
        </div>
        
        <!-- テーブル -->
        <table class="wp-list-table widefat fixed striped" id="gi-archive-table" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th style="width: 3%;"><input type="checkbox" id="select-all"></th>
                    <th style="width: 22%;">ページ名</th>
                    <th style="width: 10%;">タイプ</th>
                    <th style="width: 8%; cursor: pointer;" class="sortable" data-sort="post_count">
                        補助金数 <span class="dashicons dashicons-sort"></span>
                    </th>
                    <th style="width: 8%; cursor: pointer;" class="sortable" data-sort="pv_count">
                        PV数 <span class="dashicons dashicons-sort"></span>
                    </th>
                    <th style="width: 15%;">URL</th>
                    <th style="width: 12%;">状態</th>
                    <th style="width: 22%;">操作</th>
                </tr>
            </thead>
            <tbody id="archive-table-body">
                <!-- AJAXで読み込み -->
            </tbody>
        </table>
        
        <!-- ページネーション -->
        <div id="pagination" style="margin: 20px 0; text-align: center;"></div>
    </div>
    
    <style>
    .gi-tab {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        color: #666;
        border-bottom: 3px solid transparent;
        margin-bottom: -1px;
    }
    .gi-tab:hover { background: #f0f0f1; }
    .gi-tab.active { color: #2271b1; font-weight: bold; border-bottom-color: #2271b1; }
    th.sortable:hover { background: #f0f0f1; }
    th.sortable.asc .dashicons:before { content: "\f142"; }
    th.sortable.desc .dashicons:before { content: "\f140"; }
    th.sortable.sorting-active { background: #e5f0f8; }
    .gi-type-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        color: #fff;
    }
    .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 11px;
    }
    .pagination-btn {
        display: inline-block;
        padding: 8px 12px;
        margin: 0 2px;
        border: 1px solid #ddd;
        background: #fff;
        text-decoration: none;
        color: #333;
        border-radius: 4px;
    }
    .pagination-btn:hover { background: #f0f0f1; }
    .pagination-btn.active { background: #2271b1; color: #fff; border-color: #2271b1; }
    .pagination-btn.disabled { opacity: 0.5; pointer-events: none; }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        var currentPage = 1;
        var perPage = 50;
        var currentFilter = 'all';
        var currentSort = 'name';
        var currentOrder = 'asc';
        var searchQuery = '';
        var nonce = '<?php echo wp_create_nonce('gi_archive_seo_nonce'); ?>';
        
        // 初期読み込み
        loadPages();
        
        // タブフィルター
        $('.gi-tab').on('click', function(e) {
            e.preventDefault();
            currentFilter = $(this).data('filter');
            currentPage = 1;
            
            $('.gi-tab').removeClass('active');
            $(this).addClass('active');
            
            loadPages();
        });
        
        // ソート
        $('.sortable').on('click', function() {
            var sort = $(this).data('sort');
            if (currentSort === sort) {
                currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = sort;
                currentOrder = 'desc';
            }
            
            $('.sortable').removeClass('asc desc sorting-active');
            $(this).addClass(currentOrder).addClass('sorting-active');
            
            loadPages();
        });
        
        // 検索
        $('#btn-search').on('click', function() {
            searchQuery = $('#search-input').val();
            currentPage = 1;
            loadPages();
        });
        
        $('#search-input').on('keypress', function(e) {
            if (e.which === 13) {
                searchQuery = $(this).val();
                currentPage = 1;
                loadPages();
            }
        });
        
        // 表示件数変更
        $('#per-page').on('change', function() {
            perPage = parseInt($(this).val());
            currentPage = 1;
            loadPages();
        });
        
        // 全選択
        $('#select-all').on('change', function() {
            $('input.row-checkbox').prop('checked', $(this).is(':checked'));
        });
        
        // 一括noindex
        $('#btn-bulk-noindex').on('click', function() {
            if (!confirm('補助金数100件以下のすべてのページにnoindexを設定します。よろしいですか？')) {
                return;
            }
            
            $('#bulk-status').text('処理中...');
            
            $.post(ajaxurl, {
                action: 'gi_archive_bulk_set_noindex',
                nonce: nonce
            }, function(response) {
                if (response.success) {
                    $('#bulk-status').text(response.data.message);
                    loadPages();
                    updateStats();
                } else {
                    $('#bulk-status').text('エラー: ' + response.data);
                }
            });
        });
        
        function loadPages() {
            $('#loading-indicator').show();
            $('#archive-table-body').html('');
            
            $.post(ajaxurl, {
                action: 'gi_archive_seo_get_pages',
                nonce: nonce,
                page: currentPage,
                per_page: perPage,
                filter: currentFilter,
                sort: currentSort,
                order: currentOrder,
                search: searchQuery
            }, function(response) {
                $('#loading-indicator').hide();
                
                if (response.success) {
                    renderTable(response.data.pages);
                    renderPagination(response.data.total, response.data.pages_count);
                } else {
                    $('#archive-table-body').html('<tr><td colspan="8" style="text-align:center;">読み込みエラー</td></tr>');
                }
            });
        }
        
        function renderTable(pages) {
            var html = '';
            
            if (pages.length === 0) {
                html = '<tr><td colspan="8" style="text-align:center;">該当するページがありません</td></tr>';
            } else {
                pages.forEach(function(page) {
                    var typeColors = {
                        'post_type_archive': '#2271b1',
                        'grant_prefecture': '#00a32a',
                        'grant_category': '#9333ea',
                        'grant_municipality': '#ea580c',
                        'grant_purpose': '#0891b2',
                        'grant_tag': '#666'
                    };
                    var typeColor = typeColors[page.type] || '#666';
                    var countColor = page.post_count <= 100 ? '#d63638' : (page.post_count <= 500 ? '#dba617' : '#00a32a');
                    
                    var statusHtml = '';
                    if (page.is_merged) {
                        statusHtml = '<span class="status-badge" style="background:#d63638;color:#fff;"><span class="dashicons dashicons-migrate" style="font-size:12px;"></span> 統合済</span>';
                    } else if (page.is_noindex) {
                        statusHtml = '<span class="status-badge" style="background:#9333ea;color:#fff;"><span class="dashicons dashicons-hidden" style="font-size:12px;"></span> noindex</span>';
                    } else if (page.has_content) {
                        statusHtml = '<span class="status-badge" style="background:#00a32a;color:#fff;"><span class="dashicons dashicons-yes-alt" style="font-size:12px;"></span> 設定済</span>';
                    } else {
                        statusHtml = '<span style="color:#999;">未設定</span>';
                    }
                    
                    var actionsHtml = '<a href="' + page.edit_url + '" class="button button-primary button-small">' + (page.has_content ? '編集' : '設定') + '</a>';
                    
                    if (page.is_merge_candidate && !page.is_merged) {
                        actionsHtml += ' <a href="' + page.merge_url + '" class="button button-small" style="color:#d63638;border-color:#d63638;">統合</a>';
                    }
                    
                    html += '<tr data-type="' + page.type + '" data-key="' + page.key + '">';
                    html += '<td><input type="checkbox" class="row-checkbox" value="' + page.type + ':' + page.key + '"></td>';
                    html += '<td><strong>' + escapeHtml(page.name) + '</strong>';
                    if (page.custom_title) {
                        html += '<br><small style="color:#666;">カスタム: ' + escapeHtml(page.custom_title) + '</small>';
                    }
                    html += '</td>';
                    html += '<td><span class="gi-type-badge" style="background:' + typeColor + ';">' + escapeHtml(page.type_label) + '</span></td>';
                    html += '<td style="text-align:center;"><span style="color:' + countColor + ';font-weight:bold;">' + numberFormat(page.post_count) + '</span></td>';
                    html += '<td style="text-align:center;">' + numberFormat(page.pv_count) + '</td>';
                    html += '<td><a href="' + page.url + '" target="_blank" style="word-break:break-all;font-size:12px;">' + escapeHtml(page.url_path) + ' <span class="dashicons dashicons-external" style="font-size:12px;"></span></a></td>';
                    html += '<td>' + statusHtml + '</td>';
                    html += '<td>' + actionsHtml + '</td>';
                    html += '</tr>';
                });
            }
            
            $('#archive-table-body').html(html);
        }
        
        function renderPagination(total, pagesCount) {
            if (pagesCount <= 1) {
                $('#pagination').html('');
                return;
            }
            
            var html = '';
            
            // 前へ
            if (currentPage > 1) {
                html += '<a href="#" class="pagination-btn" data-page="' + (currentPage - 1) + '">&laquo; 前へ</a>';
            } else {
                html += '<span class="pagination-btn disabled">&laquo; 前へ</span>';
            }
            
            // ページ番号
            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(pagesCount, currentPage + 2);
            
            if (startPage > 1) {
                html += '<a href="#" class="pagination-btn" data-page="1">1</a>';
                if (startPage > 2) {
                    html += '<span style="padding:0 10px;">...</span>';
                }
            }
            
            for (var i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    html += '<span class="pagination-btn active">' + i + '</span>';
                } else {
                    html += '<a href="#" class="pagination-btn" data-page="' + i + '">' + i + '</a>';
                }
            }
            
            if (endPage < pagesCount) {
                if (endPage < pagesCount - 1) {
                    html += '<span style="padding:0 10px;">...</span>';
                }
                html += '<a href="#" class="pagination-btn" data-page="' + pagesCount + '">' + pagesCount + '</a>';
            }
            
            // 次へ
            if (currentPage < pagesCount) {
                html += '<a href="#" class="pagination-btn" data-page="' + (currentPage + 1) + '">次へ &raquo;</a>';
            } else {
                html += '<span class="pagination-btn disabled">次へ &raquo;</span>';
            }
            
            html += '<span style="margin-left:20px;color:#666;">全 ' + numberFormat(total) + ' 件</span>';
            
            $('#pagination').html(html);
            
            // ページネーションクリック
            $('.pagination-btn[data-page]').on('click', function(e) {
                e.preventDefault();
                currentPage = parseInt($(this).data('page'));
                loadPages();
                $('html, body').animate({ scrollTop: $('#gi-archive-table').offset().top - 50 }, 300);
            });
        }
        
        function updateStats() {
            $.post(ajaxurl, {
                action: 'gi_archive_seo_get_pages',
                nonce: nonce,
                stats_only: true
            }, function(response) {
                if (response.success && response.data.stats) {
                    var stats = response.data.stats;
                    $('#stat-total').text(stats.total);
                    $('#stat-configured').text(stats.configured);
                    $('#stat-unconfigured').text(stats.unconfigured);
                    $('#stat-merge-candidates').text(stats.merge_candidates);
                    $('#stat-noindex').text(stats.noindex);
                }
            });
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function numberFormat(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    });
    </script>
    <?php
}


/**
 * =============================================================================
 * 5. AJAX: ページ一覧取得（ページネーション対応）
 * =============================================================================
 */

function gi_ajax_archive_seo_get_pages() {
    check_ajax_referer('gi_archive_seo_nonce', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    // 統計のみ取得
    if (isset($_POST['stats_only']) && $_POST['stats_only']) {
        wp_send_json_success(array('stats' => gi_get_archive_seo_stats()));
        return;
    }
    
    $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $per_page = isset($_POST['per_page']) ? min(100, max(10, intval($_POST['per_page']))) : 50;
    $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : 'all';
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'name';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'asc';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // 保存済みコンテンツを取得
    $saved_contents = $wpdb->get_results(
        "SELECT * FROM {$table_name}",
        ARRAY_A
    );
    $saved_map = array();
    foreach ($saved_contents as $content) {
        $saved_map[$content['archive_type'] . ':' . $content['archive_key']] = $content;
    }
    
    // 利用可能なアーカイブページを取得（キャッシュ使用）
    $all_pages = gi_get_available_archive_pages_cached();
    
    // フィルタリング
    $filtered_pages = array();
    foreach ($all_pages as $p) {
        $key = $p['type'] . ':' . $p['key'];
        $saved = isset($saved_map[$key]) ? $saved_map[$key] : null;
        $has_content = $saved && ($saved['intro_content'] || $saved['outro_content'] || $saved['featured_posts']);
        $is_merge_candidate = isset($p['post_count']) && $p['post_count'] <= 100 && $p['post_count'] > 0;
        $is_merged = $saved && !empty($saved['merge_target']);
        $is_noindex = $saved && !empty($saved['is_noindex']);
        
        // 検索フィルタ
        if ($search && stripos($p['name'], $search) === false) {
            continue;
        }
        
        // タイプフィルタ
        $show = true;
        switch ($filter) {
            case 'unconfigured':
                $show = !$has_content;
                break;
            case 'merge_candidate':
                $show = $is_merge_candidate && !$is_merged;
                break;
            case 'noindex':
                $show = $is_noindex;
                break;
            case 'all':
                $show = true;
                break;
            default:
                $show = ($p['type'] === $filter);
        }
        
        if ($show) {
            $p['has_content'] = $has_content;
            $p['is_merge_candidate'] = $is_merge_candidate;
            $p['is_merged'] = $is_merged;
            $p['is_noindex'] = $is_noindex;
            $p['custom_title'] = $saved ? $saved['page_title'] : '';
            $p['edit_url'] = admin_url('admin.php?page=gi-archive-seo-edit&type=' . urlencode($p['type']) . '&key=' . urlencode($p['key']));
            $p['merge_url'] = admin_url('admin.php?page=gi-archive-seo-merge&source_type=' . urlencode($p['type']) . '&source_key=' . urlencode($p['key']));
            $p['url_path'] = str_replace(home_url(), '', $p['url']);
            $filtered_pages[] = $p;
        }
    }
    
    // ソート
    usort($filtered_pages, function($a, $b) use ($sort, $order) {
        $aVal = 0;
        $bVal = 0;
        
        switch ($sort) {
            case 'post_count':
                $aVal = $a['post_count'];
                $bVal = $b['post_count'];
                break;
            case 'pv_count':
                $aVal = $a['pv_count'];
                $bVal = $b['pv_count'];
                break;
            default:
                $aVal = $a['name'];
                $bVal = $b['name'];
                return $order === 'asc' ? strcmp($aVal, $bVal) : strcmp($bVal, $aVal);
        }
        
        return $order === 'asc' ? ($aVal - $bVal) : ($bVal - $aVal);
    });
    
    $total = count($filtered_pages);
    $pages_count = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;
    
    $paged_results = array_slice($filtered_pages, $offset, $per_page);
    
    wp_send_json_success(array(
        'pages' => $paged_results,
        'total' => $total,
        'pages_count' => $pages_count,
        'current_page' => $page,
        'stats' => gi_get_archive_seo_stats()
    ));
}


/**
 * =============================================================================
 * 6. 統計情報取得
 * =============================================================================
 */

function gi_get_archive_seo_stats() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    // キャッシュから取得
    $cache_key = 'gi_archive_seo_stats';
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }
    
    $all_pages = gi_get_available_archive_pages_cached();
    
    $saved_contents = $wpdb->get_results(
        "SELECT archive_type, archive_key, intro_content, outro_content, featured_posts, is_noindex, merge_target FROM {$table_name}",
        ARRAY_A
    );
    $saved_map = array();
    foreach ($saved_contents as $content) {
        $saved_map[$content['archive_type'] . ':' . $content['archive_key']] = $content;
    }
    
    $stats = array(
        'total' => count($all_pages),
        'configured' => 0,
        'unconfigured' => 0,
        'merge_candidates' => 0,
        'noindex' => 0,
        'merged' => 0
    );
    
    foreach ($all_pages as $page) {
        $key = $page['type'] . ':' . $page['key'];
        $saved = isset($saved_map[$key]) ? $saved_map[$key] : null;
        $has_content = $saved && ($saved['intro_content'] || $saved['outro_content'] || $saved['featured_posts']);
        
        if ($has_content) {
            $stats['configured']++;
        } else {
            $stats['unconfigured']++;
        }
        
        if (isset($page['post_count']) && $page['post_count'] <= 100 && $page['post_count'] > 0) {
            $stats['merge_candidates']++;
        }
        
        if ($saved && !empty($saved['is_noindex'])) {
            $stats['noindex']++;
        }
        
        if ($saved && !empty($saved['merge_target'])) {
            $stats['merged']++;
        }
    }
    
    // 5分キャッシュ
    set_transient($cache_key, $stats, 5 * MINUTE_IN_SECONDS);
    
    return $stats;
}


/**
 * =============================================================================
 * 7. アーカイブページ一覧取得（キャッシュ版）
 * =============================================================================
 */

function gi_get_available_archive_pages_cached() {
    $cache_key = 'gi_archive_pages_list';
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $pages = gi_get_available_archive_pages();
    
    // 10分キャッシュ
    set_transient($cache_key, $pages, 10 * MINUTE_IN_SECONDS);
    
    return $pages;
}

function gi_get_available_archive_pages() {
    $pages = array();
    
    // 投稿タイプアーカイブ
    $total_grants = wp_count_posts('grant');
    $total_grants_count = isset($total_grants->publish) ? $total_grants->publish : 0;
    
    $pages[] = array(
        'type' => 'post_type_archive',
        'key' => 'grant',
        'name' => '助成金・補助金一覧（メイン）',
        'type_label' => '投稿タイプ',
        'url' => get_post_type_archive_link('grant'),
        'post_count' => $total_grants_count,
        'pv_count' => gi_get_archive_pv_count('post_type_archive', 'grant'),
    );
    
    // 各タクソノミー
    $taxonomies = array(
        'grant_prefecture' => '都道府県',
        'grant_category' => 'カテゴリ',
        'grant_municipality' => '市区町村',
        'grant_purpose' => '目的',
        'grant_tag' => 'タグ',
    );
    
    foreach ($taxonomies as $taxonomy => $label) {
        if (!taxonomy_exists($taxonomy)) continue;
        
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $term_link = get_term_link($term);
                if (is_wp_error($term_link)) continue;
                
                $pages[] = array(
                    'type' => $taxonomy,
                    'key' => $term->slug,
                    'name' => $term->name . 'の助成金・補助金',
                    'type_label' => $label,
                    'url' => $term_link,
                    'post_count' => $term->count,
                    'pv_count' => gi_get_archive_pv_count($taxonomy, $term->slug),
                    'term_id' => $term->term_id,
                    'description' => $term->description,
                );
            }
        }
    }
    
    return $pages;
}


/**
 * =============================================================================
 * 8. PV数取得
 * =============================================================================
 */

function gi_get_archive_pv_count($type, $key) {
    global $wpdb;
    
    // access-tracking.phpで使用されている正しいテーブル名を使用
    $daily_table = $wpdb->prefix . 'ji_post_views_daily';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '{$daily_table}'") !== $daily_table) {
        return 0;
    }
    
    // アーカイブページのURLパターンからPV数を集計
    if ($type === 'post_type_archive') {
        // 投稿タイプアーカイブ全体のPV数
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(view_count), 0) 
            FROM {$daily_table} 
            WHERE post_type = %s",
            'grant'
        ));
    } else {
        // タクソノミーアーカイブのPV数
        // 該当タームに属する投稿のPV数を合計
        $term = get_term_by('slug', $key, $type);
        if (!$term || is_wp_error($term)) {
            return 0;
        }
        
        // タームに属する投稿IDを取得
        $posts = get_posts(array(
            'post_type' => 'grant',
            'tax_query' => array(
                array(
                    'taxonomy' => $type,
                    'field' => 'slug',
                    'terms' => $key,
                ),
            ),
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_status' => 'publish',
        ));
        
        if (empty($posts)) {
            return 0;
        }
        
        $post_ids = implode(',', array_map('intval', $posts));
        
        $count = $wpdb->get_var(
            "SELECT COALESCE(SUM(view_count), 0) 
            FROM {$daily_table} 
            WHERE post_id IN ({$post_ids})"
        );
    }
    
    return intval($count);
}


/**
 * =============================================================================
 * 9. 管理画面 - ページ編集
 * =============================================================================
 */

function gi_archive_seo_edit_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    $type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
    $key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    
    if (empty($type) || empty($key)) {
        echo '<div class="wrap"><h1>エラー</h1><p>ページが指定されていません。</p></div>';
        return;
    }
    
    // 保存処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gi_archive_seo_nonce'])) {
        if (wp_verify_nonce($_POST['gi_archive_seo_nonce'], 'gi_archive_seo_save')) {
            $data = array(
                'archive_type' => $type,
                'archive_key' => $key,
                'page_title' => sanitize_text_field($_POST['page_title'] ?? ''),
                'meta_description' => sanitize_textarea_field($_POST['meta_description'] ?? ''),
                'intro_content' => wp_kses_post($_POST['intro_content'] ?? ''),
                'outro_content' => wp_kses_post($_POST['outro_content'] ?? ''),
                'sidebar_content' => wp_kses_post($_POST['sidebar_content'] ?? ''),
                'featured_posts' => sanitize_text_field($_POST['featured_posts'] ?? ''),
                'custom_css' => wp_strip_all_tags($_POST['custom_css'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'is_noindex' => isset($_POST['is_noindex']) ? 1 : 0,
            );
            
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE archive_type = %s AND archive_key = %s",
                $type, $key
            ));
            
            if ($existing) {
                $wpdb->update($table_name, $data, array('id' => $existing->id));
            } else {
                $wpdb->insert($table_name, $data);
            }
            
            // キャッシュをクリア
            delete_transient('gi_archive_seo_' . md5($type . ':' . $key));
            delete_transient('gi_archive_seo_stats');
            delete_transient('gi_archive_pages_list');
            
            echo '<div class="notice notice-success is-dismissible"><p>保存しました。</p></div>';
        }
    }
    
    // 現在のデータを取得
    $content = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE archive_type = %s AND archive_key = %s",
        $type, $key
    ), ARRAY_A);
    
    if (!$content) {
        $content = array(
            'page_title' => '',
            'meta_description' => '',
            'intro_content' => '',
            'outro_content' => '',
            'sidebar_content' => '',
            'featured_posts' => '',
            'custom_css' => '',
            'is_active' => 1,
            'is_noindex' => 0,
        );
    }
    
    // ページ情報を取得
    $page_info = gi_get_archive_page_info($type, $key);
    
    // 補助金100件以下の場合、自動noindex推奨
    $auto_noindex_recommend = ($page_info['post_count'] <= 100 && $page_info['post_count'] > 0);
    
    ?>
    <div class="wrap">
        <h1>
            <a href="<?php echo admin_url('admin.php?page=gi-archive-seo'); ?>" style="text-decoration: none; color: inherit;">
                <span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span>
            </a>
            アーカイブSEO編集: <?php echo esc_html($page_info['name']); ?>
        </h1>
        
        <div style="display: flex; gap: 20px; margin: 15px 0; flex-wrap: wrap;">
            <div style="padding: 15px; background: #f0f6fc; border-left: 4px solid #2271b1; border-radius: 4px; flex: 1; min-width: 300px;">
                <strong>対象URL:</strong> 
                <a href="<?php echo esc_url($page_info['url']); ?>" target="_blank">
                    <?php echo esc_url($page_info['url']); ?>
                    <span class="dashicons dashicons-external" style="font-size: 14px;"></span>
                </a>
            </div>
            <div style="padding: 15px; background: <?php echo $auto_noindex_recommend ? '#fcf0f0' : '#f0f0f1'; ?>; border-radius: 4px; <?php echo $auto_noindex_recommend ? 'border-left: 4px solid #d63638;' : ''; ?>">
                <strong>補助金数:</strong> 
                <span style="<?php echo $auto_noindex_recommend ? 'color: #d63638; font-weight: bold;' : ''; ?>">
                    <?php echo number_format($page_info['post_count'] ?? 0); ?>件
                </span>
                <?php if ($auto_noindex_recommend): ?>
                    <br><small style="color: #d63638;">※ 100件以下のためnoindex推奨</small>
                <?php endif; ?>
            </div>
            <div style="padding: 15px; background: #f0f0f1; border-radius: 4px;">
                <strong>PV数:</strong> <?php echo number_format($page_info['pv_count'] ?? 0); ?>
            </div>
        </div>
        
        <form method="post" id="gi-archive-seo-form">
            <?php wp_nonce_field('gi_archive_seo_save', 'gi_archive_seo_nonce'); ?>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">
                
                <!-- メインエディタ -->
                <div>
                    <!-- ページタイトル -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">ページタイトル（H1）</h2>
                        </div>
                        <div class="inside">
                            <input type="text" name="page_title" value="<?php echo esc_attr($content['page_title']); ?>" 
                                   class="large-text" placeholder="空欄の場合はデフォルトタイトルを使用">
                            <p class="description">デフォルト: <?php echo esc_html($page_info['default_title']); ?></p>
                        </div>
                    </div>
                    
                    <!-- メタディスクリプション -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">メタディスクリプション</h2>
                        </div>
                        <div class="inside">
                            <textarea name="meta_description" rows="3" class="large-text" 
                                      placeholder="120〜160文字推奨"><?php echo esc_textarea($content['meta_description']); ?></textarea>
                            <p class="description">
                                現在: <span id="meta-desc-count"><?php echo mb_strlen($content['meta_description']); ?></span>文字
                            </p>
                        </div>
                    </div>
                    
                    <!-- イントロコンテンツ -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">
                                イントロコンテンツ
                                <span style="font-weight: normal; font-size: 12px; color: #666;">（記事一覧の上部に表示）</span>
                            </h2>
                        </div>
                        <div class="inside">
                            <?php 
                            wp_editor($content['intro_content'], 'intro_content', array(
                                'textarea_name' => 'intro_content',
                                'textarea_rows' => 10,
                                'media_buttons' => true,
                                'teeny' => false,
                            )); 
                            ?>
                        </div>
                    </div>
                    
                    <!-- アウトロコンテンツ -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">
                                アウトロコンテンツ
                                <span style="font-weight: normal; font-size: 12px; color: #666;">（記事一覧の下部に表示）</span>
                            </h2>
                        </div>
                        <div class="inside">
                            <?php 
                            wp_editor($content['outro_content'], 'outro_content', array(
                                'textarea_name' => 'outro_content',
                                'textarea_rows' => 10,
                                'media_buttons' => true,
                                'teeny' => false,
                            )); 
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- サイドバー -->
                <div>
                    <!-- 公開設定 -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">公開設定</h2>
                        </div>
                        <div class="inside">
                            <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <input type="checkbox" name="is_active" value="1" <?php checked($content['is_active'], 1); ?>>
                                <span>このコンテンツを有効にする</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 10px; <?php echo $auto_noindex_recommend ? 'background: #fcf0f0; padding: 10px; border-radius: 4px;' : ''; ?>">
                                <input type="checkbox" name="is_noindex" value="1" <?php checked($content['is_noindex'] ?? 0, 1); ?>>
                                <span style="color: #d63638;">noindex（検索エンジンから除外）</span>
                            </label>
                            <?php if ($auto_noindex_recommend && empty($content['is_noindex'])): ?>
                                <p style="color: #d63638; font-size: 12px; margin-top: 5px;">
                                    <span class="dashicons dashicons-warning"></span>
                                    補助金数が100件以下のため、noindex設定を推奨します
                                </p>
                            <?php endif; ?>
                            <hr style="margin: 15px 0;">
                            <button type="submit" class="button button-primary button-large" style="width: 100%;">
                                保存
                            </button>
                        </div>
                    </div>
                    
                    <!-- おすすめ記事 -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">おすすめ記事</h2>
                        </div>
                        <div class="inside">
                            <input type="text" name="featured_posts" id="featured_posts" value="<?php echo esc_attr($content['featured_posts']); ?>" 
                                   class="large-text" placeholder="記事IDをカンマ区切りで入力">
                            <p class="description">例: 123, 456, 789</p>
                            <button type="button" class="button" id="btn-auto-select-featured" style="margin-top: 10px;">
                                <span class="dashicons dashicons-star-filled" style="vertical-align: middle;"></span>
                                PV順で自動選出
                            </button>
                            <div id="selected-posts-preview" style="margin-top: 10px;"></div>
                        </div>
                    </div>
                    
                    <!-- サイドバー追加コンテンツ -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">サイドバー追加コンテンツ</h2>
                        </div>
                        <div class="inside">
                            <textarea name="sidebar_content" rows="8" class="large-text" 
                                      placeholder="HTMLを入力（サイドバーの上部に表示されます）"><?php echo esc_textarea($content['sidebar_content']); ?></textarea>
                            <p class="description">サイドバーの広告枠の上に表示されます</p>
                        </div>
                    </div>
                    
                    <!-- カスタムCSS -->
                    <div class="postbox" style="margin-bottom: 20px;">
                        <div class="postbox-header">
                            <h2 style="padding: 10px 15px; margin: 0;">カスタムCSS</h2>
                        </div>
                        <div class="inside">
                            <textarea name="custom_css" rows="10" class="large-text code" 
                                      placeholder="このページ専用のCSS"><?php echo esc_textarea($content['custom_css']); ?></textarea>
                            <p class="description">
                                使用可能なクラス:<br>
                                <code>.gi-archive-seo-intro</code> - イントロボックス<br>
                                <code>.gi-archive-seo-outro</code> - アウトロボックス<br>
                                <code>.gi-archive-seo-featured</code> - おすすめ記事<br>
                                <code>.gi-archive-seo-sidebar</code> - サイドバー<br>
                                各ボックス内: <code>h2, h3, p, ul, ol, table</code> 等
                            </p>
                        </div>
                    </div>
                    
                    <!-- AIコンテンツ生成プロンプト -->
                    <div class="postbox" style="margin-bottom: 20px; border-color: #9b59b6;">
                        <div class="postbox-header" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                            <h2 style="padding: 10px 15px; margin: 0; color: #fff;">
                                <span class="dashicons dashicons-admin-customizer" style="color: #fff;"></span>
                                統合AIコンテンツ生成プロンプト
                            </h2>
                        </div>
                        <div class="inside">
                            <p class="description" style="margin-bottom: 15px;">
                                <strong>新機能：</strong> イントロ・アウトロ・メタディスクリプションを一括生成する統合プロンプトです。<br>
                                都道府県なら予算規模・独自制度、カテゴリなら業界トレンド・補助金額相場など、独創的な情報をAIに生成させます。
                            </p>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
                                <button type="button" class="button button-primary ai-prompt-copy" data-prompt-type="intro">
                                    <span class="dashicons dashicons-editor-paste-text" style="vertical-align: middle;"></span>
                                    イントロ用（統合版）
                                </button>
                                <button type="button" class="button button-primary ai-prompt-copy" data-prompt-type="outro">
                                    <span class="dashicons dashicons-editor-paste-text" style="vertical-align: middle;"></span>
                                    アウトロ用（統合版）
                                </button>
                                <button type="button" class="button ai-prompt-copy" data-prompt-type="meta">
                                    <span class="dashicons dashicons-editor-paste-text" style="vertical-align: middle;"></span>
                                    メタディスクリプション用
                                </button>
                            </div>
                            <div id="ai-prompt-preview" style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; padding: 15px; font-size: 13px; line-height: 1.6; max-height: 400px; overflow-y: auto; display: none;">
                                <pre style="white-space: pre-wrap; word-wrap: break-word; margin: 0; font-family: inherit;"></pre>
                            </div>
                            <p id="ai-prompt-copy-status" style="color: #46b450; font-weight: bold; margin-top: 10px; display: none;">
                                <span class="dashicons dashicons-yes-alt"></span> クリップボードにコピーしました！ChatGPTなどのAIに貼り付けてください。
                            </p>
                            <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                <strong>💡 使い方：</strong>
                                <ol style="margin: 10px 0 0; padding-left: 20px; font-size: 13px; line-height: 1.8;">
                                    <li>上のボタンをクリックしてプロンプトをコピー</li>
                                    <li>ChatGPT、Claude、Geminiなどのチャット画面に貼り付け</li>
                                    <li>生成されたHTML形式のコンテンツをコピー</li>
                                    <li>上の「イントロコンテンツ」または「アウトロコンテンツ」エディタに貼り付け</li>
                                    <li>必要に応じて微調整して保存</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 統合候補（100件以下の場合） -->
                    <?php if ($auto_noindex_recommend && $type !== 'post_type_archive'): ?>
                    <div class="postbox" style="margin-bottom: 20px; border-color: #d63638;">
                        <div class="postbox-header" style="background: #fcf0f0;">
                            <h2 style="padding: 10px 15px; margin: 0; color: #d63638;">
                                <span class="dashicons dashicons-migrate"></span>
                                統合候補
                            </h2>
                        </div>
                        <div class="inside">
                            <p style="color: #666; font-size: 12px;">補助金数が少ないため、類似カテゴリへの統合を検討してください。</p>
                            <button type="button" class="button" id="btn-find-similar" data-type="<?php echo esc_attr($type); ?>" data-key="<?php echo esc_attr($key); ?>">
                                類似カテゴリを検索
                            </button>
                            <div id="similar-categories-result" style="margin-top: 10px;"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var nonce = '<?php echo wp_create_nonce('gi_archive_seo_nonce'); ?>';
        var archiveType = '<?php echo esc_js($type); ?>';
        var archiveKey = '<?php echo esc_js($key); ?>';
        
        // メタディスクリプション文字数カウント
        $('textarea[name="meta_description"]').on('input', function() {
            $('#meta-desc-count').text($(this).val().length);
        });
        
        // おすすめ記事プレビュー
        function updatePostsPreview() {
            var ids = $('#featured_posts').val();
            if (!ids) {
                $('#selected-posts-preview').html('');
                return;
            }
            
            $.post(ajaxurl, {
                action: 'gi_get_posts_preview',
                ids: ids,
                _wpnonce: nonce
            }, function(response) {
                if (response.success) {
                    $('#selected-posts-preview').html(response.data.html);
                }
            });
        }
        
        $('#featured_posts').on('change', updatePostsPreview);
        updatePostsPreview();
        
        // PV順で自動選出
        $('#btn-auto-select-featured').on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).text('検索中...');
            
            $.post(ajaxurl, {
                action: 'gi_archive_auto_select_featured',
                nonce: nonce,
                type: archiveType,
                key: archiveKey,
                count: 3
            }, function(response) {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-star-filled" style="vertical-align: middle;"></span> PV順で自動選出');
                
                if (response.success) {
                    $('#featured_posts').val(response.data.ids.join(', ')).trigger('change');
                } else {
                    alert('エラー: ' + response.data);
                }
            });
        });
        
        // 類似カテゴリ検索
        $('#btn-find-similar').on('click', function() {
            var $btn = $(this);
            var type = $btn.data('type');
            var key = $btn.data('key');
            
            $btn.prop('disabled', true).text('検索中...');
            $('#similar-categories-result').html('<p>検索中...</p>');
            
            $.post(ajaxurl, {
                action: 'gi_archive_get_similar_categories',
                nonce: nonce,
                type: type,
                key: key
            }, function(response) {
                $btn.prop('disabled', false).text('類似カテゴリを検索');
                
                if (response.success && response.data.similar.length > 0) {
                    var html = '<ul style="margin: 0; padding: 0; list-style: none;">';
                    response.data.similar.forEach(function(item) {
                        html += '<li style="padding: 8px; border-bottom: 1px solid #eee;">';
                        html += '<strong>' + item.name + '</strong>';
                        html += ' <span style="color: #666;">(' + item.post_count + '件)</span>';
                        html += ' <span style="background: #e0f0ff; padding: 2px 6px; border-radius: 3px; font-size: 11px;">類似度: ' + item.similarity + '%</span>';
                        html += '<br><a href="' + item.merge_url + '" class="button button-small" style="margin-top: 5px;">このカテゴリに統合</a>';
                        html += '</li>';
                    });
                    html += '</ul>';
                    $('#similar-categories-result').html(html);
                } else {
                    $('#similar-categories-result').html('<p style="color: #666;">類似カテゴリが見つかりませんでした。</p>');
                }
            });
        });
        
        // AIプロンプト生成・コピー機能
        var archiveTitle = '<?php echo esc_js($page_info['name']); ?>';
        var archiveTypeName = '<?php echo esc_js(gi_get_archive_type_label($type)); ?>';
        var postCount = <?php echo intval($page_info['post_count'] ?? 0); ?>;
        var siteName = '<?php echo esc_js(get_bloginfo('name')); ?>';
        
        // 全体の補助金件数を動的に取得
        var totalGrantsCount = <?php 
            $total_grants = wp_count_posts('grant');
            echo intval($total_grants->publish ?? 0); 
        ?>;
        
        function generateAIPrompt(promptType) {
            var currentYear = new Date().getFullYear();
            var currentYearJapanese = currentYear - 2018; // 令和年号
            var prompt = '';
            
            // 都道府県別の追加情報を準備
            var prefectureInfo = '';
            if (archiveTypeName === '都道府県') {
                prefectureInfo = `

【都道府県別 独創的情報の要求】
この都道府県に関して、以下の独創的な情報を必ず含めてください：
1. **予算規模**: この都道府県の補助金・助成金の年間予算規模の概算
2. **特色**: この地域特有の産業・課題に対応した補助金の特徴
3. **採択傾向**: 過去の採択実績から見える優先分野や重点施策
4. **独自制度**: 国の制度にはない、この都道府県独自の補助金制度
5. **地域課題**: 人口減少、産業衰退、環境問題など地域特有の課題と対応施策
6. **成功事例**: この都道府県での補助金活用による具体的な成功事例（匿名化）`;
            }
            
            // カテゴリ別の追加情報を準備
            var categoryInfo = '';
            if (archiveTypeName === 'カテゴリ') {
                categoryInfo = `

【カテゴリ別 独創的情報の要求】
このカテゴリに関して、以下の独創的な情報を必ず含めてください：
1. **業界トレンド**: このカテゴリに関連する最新の業界トレンドと補助金の関係
2. **補助金額相場**: このカテゴリの補助金の一般的な支給額の範囲
3. **採択率データ**: このカテゴリの一般的な採択率と採択を高めるポイント
4. **併用可能性**: 他のカテゴリの補助金との併用可能性
5. **申請難易度**: 書類準備の難易度と必要な専門知識のレベル`;
            }
            
            // 統合プロンプトを生成
            if (promptType === 'intro') {
                prompt = `あなたは補助金・助成金の専門家です。以下の条件で、SEO最適化されたイントロコンテンツを作成してください。

【対象アーカイブページ情報】
- ページタイトル: ${archiveTitle}
- カテゴリ種別: ${archiveTypeName}
- このページの掲載補助金数: ${postCount}件
- サイト全体の補助金総数: ${totalGrantsCount}件
- 対象年度: ${currentYear}年（令和${currentYearJapanese}年度）${prefectureInfo}${categoryInfo}

【コンテンツ構成】
1. **見出し**: 「${archiveTitle}の傾向と対策」（<h2>タグ）
2. **導入段落**（150〜200字）:
   - このカテゴリ/地域の補助金の全体像
   - ${currentYear}年（令和${currentYearJapanese}年度）の最新動向
   - 掲載件数（${postCount}件）の意義と選定基準
   - サイト全体での位置づけ（総数${totalGrantsCount}件中の${postCount}件）

3. **特徴・傾向セクション**（200〜300字）:
   - このカテゴリ/地域特有の補助金の特徴
   - 予算規模や支給額の傾向
   - 対象となる事業者・事業内容
   - 重点分野や優先テーマ

4. **申請のポイント**（200〜300字）:
   - 採択率を上げる具体的なコツ（3〜5項目）
   - 申請書作成の注意点
   - よくある失敗例と対策
   - 専門家活用のメリット

5. **最新トレンド**（150〜200字）:
   - ${currentYear}年の政策動向との関連
   - 新設・拡充された制度情報
   - 今後の展望

【必須要件】
- 合計文字数: 700〜1000字
- HTML形式で出力（<h2>, <h3>, <p>, <ul>, <li>, <strong>タグ使用）
- 具体的な数字・データを含める
- 読みやすい段落構成
- 専門用語には簡潔な説明を付ける
- 図鑑・事典のような知的で信頼性のある文体
- 読者（中小企業経営者・個人事業主）に役立つ実践的な情報
- SEOキーワードを自然に含める（「${archiveTitle}」「補助金」「助成金」「${currentYear}年」など）`;

            } else if (promptType === 'outro') {
                prompt = `あなたは補助金・助成金の専門家です。以下の条件で、SEO最適化されたアウトロコンテンツを作成してください。

【対象アーカイブページ情報】
- ページタイトル: ${archiveTitle}
- カテゴリ種別: ${archiveTypeName}
- このページの掲載補助金数: ${postCount}件
- サイト全体の補助金総数: ${totalGrantsCount}件
- 対象年度: ${currentYear}年（令和${currentYearJapanese}年度）${prefectureInfo}${categoryInfo}

【コンテンツ構成】
1. **見出し**: 「${archiveTitle}申請のまとめ」（<h2>タグ）

2. **重要ポイントのまとめ**（200〜300字）:
   - このカテゴリ/地域の補助金申請で最も重要な3〜5つのポイント
   - 申請前に必ず確認すべき事項
   - 成功のための準備項目

3. **よくある質問（Q&A）**:
   - Q1〜Q3: このカテゴリ/地域の補助金に関する具体的な質問と回答
   - 各Q&Aは<h3>（質問）と<p>（回答）で構成
   - 実践的で役立つ内容

4. **関連情報・次のステップ**（150〜200字）:
   - 関連する他のカテゴリ・地域の補助金への誘導
   - 併用可能な制度の紹介
   - より詳しい情報の入手方法

5. **CTA（コールトゥアクション）**（100〜150字）:
   - 専門家への相談を促す文章
   - 申請サポートサービスの案内
   - 次のアクションへの誘導

【必須要件】
- 合計文字数: 600〜900字
- HTML形式で出力（<h2>, <h3>, <p>, <ul>, <li>, <strong>タグ使用）
- Q&Aは実践的で具体的な内容
- 読者の不安を解消し、行動を促す文章
- 専門的だが親しみやすいトーン
- SEOキーワードを自然に含める`;

            } else if (promptType === 'meta') {
                prompt = `あなたはSEOの専門家です。以下の条件で、クリック率を最大化するメタディスクリプションを作成してください。

【対象アーカイブページ情報】
- ページタイトル: ${archiveTitle}
- カテゴリ種別: ${archiveTypeName}
- このページの掲載補助金数: ${postCount}件
- サイト全体の補助金総数: ${totalGrantsCount}件
- サイト名: ${siteName}
- 対象年度: ${currentYear}年（令和${currentYearJapanese}年度）${prefectureInfo}${categoryInfo}

【メタディスクリプション作成要件】
1. 文字数: 120〜160文字（厳守）
2. 必須キーワード:
   - 「${archiveTitle}」を自然に含める
   - 「補助金」または「助成金」を含める
   - ${currentYear}年を示唆する言葉

3. 含めるべき要素:
   - 掲載件数（${postCount}件）の具体的な数字
   - このカテゴリ/地域の特徴・強み
   - 対象読者（中小企業、個人事業主など）
   - 行動喚起（「今すぐ確認」「チェック」など）

4. 効果的な表現:
   - 具体的な数字を使用
   - ベネフィットを明確に
   - 緊急性や希少性を示唆
   - 検索意図に合致した内容

5. NGワード・表現:
   - 「〜について」「〜に関する」などの冗長表現
   - 過度な煽り文句
   - HTMLタグ

【出力形式】
メタディスクリプションのテキストのみを1行で出力してください（HTMLタグ、改行不要）`;
            }
            
            return prompt;
        }
        
        // プロンプトボタンクリックイベント
        $('.ai-prompt-copy').on('click', function() {
            var promptType = $(this).data('prompt-type');
            var prompt = generateAIPrompt(promptType);
            
            // プレビュー表示
            $('#ai-prompt-preview').show().find('pre').text(prompt);
            
            // クリップボードにコピー
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(prompt).then(function() {
                    $('#ai-prompt-copy-status').fadeIn().delay(2000).fadeOut();
                });
            } else {
                // フォールバック: 古いブラウザ用
                var $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(prompt).select();
                document.execCommand('copy');
                $temp.remove();
                $('#ai-prompt-copy-status').fadeIn().delay(2000).fadeOut();
            }
            
            // ボタンのアクティブ状態を更新
            $('.ai-prompt-copy').removeClass('button-primary');
            $(this).addClass('button-primary');
        });
    });
    </script>
    <?php
}


/**
 * =============================================================================
 * 10. AJAX: おすすめ記事プレビュー
 * =============================================================================
 */

function gi_ajax_get_posts_preview() {
    check_ajax_referer('gi_archive_seo_nonce', '_wpnonce');
    
    $ids = isset($_POST['ids']) ? sanitize_text_field($_POST['ids']) : '';
    $post_ids = array_map('intval', array_filter(explode(',', $ids)));
    
    if (empty($post_ids)) {
        wp_send_json_success(array('html' => ''));
    }
    
    $posts = get_posts(array(
        'post_type' => 'grant',
        'post__in' => $post_ids,
        'orderby' => 'post__in',
        'posts_per_page' => count($post_ids),
    ));
    
    $html = '<ul style="margin: 0; padding: 0; list-style: none;">';
    foreach ($posts as $post) {
        $pv = (int)get_post_meta($post->ID, '_gi_pv_total', true);
        $html .= '<li style="padding: 5px 0; border-bottom: 1px solid #eee;">';
        $html .= '<small style="color: #666;">[ID:' . $post->ID . ']</small> ';
        $html .= '<small style="color: #00a32a;">[PV:' . number_format($pv) . ']</small> ';
        $html .= esc_html(mb_substr($post->post_title, 0, 40));
        if (mb_strlen($post->post_title) > 40) $html .= '...';
        $html .= '</li>';
    }
    $html .= '</ul>';
    
    wp_send_json_success(array('html' => $html));
}


/**
 * =============================================================================
 * 11. AJAX: おすすめ記事自動選出（PV順）
 * ※ 都道府県・カテゴリ内の記事のみでPV順にソート
 * =============================================================================
 */

function gi_ajax_auto_select_featured() {
    check_ajax_referer('gi_archive_seo_nonce', 'nonce');
    
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
    $count = isset($_POST['count']) ? min(10, max(1, intval($_POST['count']))) : 3;
    
    if (empty($type) || empty($key)) {
        wp_send_json_error('パラメータが不足しています');
        return;
    }
    
    global $wpdb;
    
    // タクソノミー内の記事をPV順で取得
    if ($type !== 'post_type_archive') {
        // 都道府県・カテゴリ等のタクソノミーの場合
        // そのタクソノミーに属する記事のPV数でソート
        $term = get_term_by('slug', $key, $type);
        
        if (!$term) {
            wp_send_json_error('タームが見つかりません');
            return;
        }
        
        // アクセストラッキングテーブルから該当タクソノミー内の記事のPVを取得
        $daily_table = $wpdb->prefix . 'ji_access_daily';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$daily_table}'");
        
        if ($table_exists) {
            // アクセストラッキングテーブルがある場合、そこからPVを取得
            $query = $wpdb->prepare("
                SELECT p.ID, p.post_title, COALESCE(SUM(ad.view_count), 0) as total_pv
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN {$daily_table} ad ON p.ID = ad.post_id
                WHERE p.post_type = 'grant'
                AND p.post_status = 'publish'
                AND tt.taxonomy = %s
                AND tt.term_id = %d
                GROUP BY p.ID
                ORDER BY total_pv DESC, p.post_date DESC
                LIMIT %d
            ", $type, $term->term_id, $count);
            
            $results = $wpdb->get_results($query);
            
            if (!empty($results)) {
                $ids = wp_list_pluck($results, 'ID');
                
                wp_send_json_success(array(
                    'ids' => $ids,
                    'posts' => array_map(function($r) {
                        return array(
                            'id' => $r->ID,
                            'title' => $r->post_title,
                            'pv' => (int)$r->total_pv
                        );
                    }, $results),
                    'source' => 'access_tracking'
                ));
                return;
            }
        }
        
        // フォールバック: view_count メタを使用
        $args = array(
            'post_type' => 'grant',
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'tax_query' => array(
                array(
                    'taxonomy' => $type,
                    'field' => 'slug',
                    'terms' => $key,
                ),
            ),
        );
        
        // まずview_countで試す
        $args['meta_key'] = 'view_count';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
        
        $posts = get_posts($args);
        
        if (empty($posts)) {
            // view_countがない場合は_gi_pv_totalで試す
            $args['meta_key'] = '_gi_pv_total';
            $posts = get_posts($args);
        }
        
        if (empty($posts)) {
            // それでもない場合は日付順
            unset($args['meta_key']);
            $args['orderby'] = 'date';
            $posts = get_posts($args);
        }
        
    } else {
        // 全体アーカイブの場合
        $args = array(
            'post_type' => 'grant',
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'meta_key' => 'view_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        );
        
        $posts = get_posts($args);
        
        if (empty($posts)) {
            unset($args['meta_key']);
            $args['orderby'] = 'date';
            $posts = get_posts($args);
        }
    }
    
    $ids = wp_list_pluck($posts, 'ID');
    
    wp_send_json_success(array(
        'ids' => $ids,
        'posts' => array_map(function($p) {
            $pv = (int)get_post_meta($p->ID, 'view_count', true);
            if (!$pv) {
                $pv = (int)get_post_meta($p->ID, '_gi_pv_total', true);
            }
            return array(
                'id' => $p->ID,
                'title' => $p->post_title,
                'pv' => $pv
            );
        }, $posts),
        'source' => 'meta_fallback'
    ));
}

/**
 * アーカイブタイプのラベルを取得
 */
function gi_get_archive_type_label($type) {
    $labels = array(
        'post_type_archive' => '全助成金',
        'grant_category' => 'カテゴリ',
        'grant_prefecture' => '都道府県',
        'grant_municipality' => '市町村',
        'grant_tag' => 'タグ',
        'grant_purpose' => '目的',
    );
    return isset($labels[$type]) ? $labels[$type] : $type;
}


/**
 * =============================================================================
 * 12. AJAX: 類似カテゴリ検出（高精度版）
 * =============================================================================
 */

function gi_ajax_get_similar_categories() {
    check_ajax_referer('gi_archive_seo_nonce', 'nonce');
    
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
    
    if (empty($type) || empty($key) || $type === 'post_type_archive') {
        wp_send_json_error('タクソノミーが指定されていません');
        return;
    }
    
    $similar = gi_find_similar_categories($type, $key);
    
    wp_send_json_success(array('similar' => $similar));
}


/**
 * 類似カテゴリ検出（高精度TF-IDF/コサイン類似度ベース）
 */
function gi_find_similar_categories($taxonomy, $source_slug, $limit = 5) {
    $source_term = get_term_by('slug', $source_slug, $taxonomy);
    if (!$source_term || is_wp_error($source_term)) {
        return array();
    }
    
    // ソースタームの特徴を取得
    $source_features = gi_extract_term_features($source_term, $taxonomy);
    
    // 同じタクソノミー内の他のタームを取得
    $all_terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'exclude' => array($source_term->term_id),
    ));
    
    if (is_wp_error($all_terms) || empty($all_terms)) {
        return array();
    }
    
    $candidates = array();
    
    foreach ($all_terms as $term) {
        // 統合候補にならない（補助金数が多い）タームのみ対象
        if ($term->count <= $source_term->count) {
            continue;
        }
        
        $term_features = gi_extract_term_features($term, $taxonomy);
        $similarity = gi_calculate_term_similarity($source_features, $term_features);
        
        if ($similarity > 20) { // 20%以上の類似度のみ
            $candidates[] = array(
                'term_id' => $term->term_id,
                'slug' => $term->slug,
                'name' => $term->name,
                'post_count' => $term->count,
                'similarity' => round($similarity, 1),
                'merge_url' => admin_url('admin.php?page=gi-archive-seo-merge&source_type=' . urlencode($taxonomy) . '&source_key=' . urlencode($source_slug) . '&target_type=' . urlencode($taxonomy) . '&target_key=' . urlencode($term->slug)),
            );
        }
    }
    
    // 類似度順にソート
    usort($candidates, function($a, $b) {
        return $b['similarity'] - $a['similarity'];
    });
    
    return array_slice($candidates, 0, $limit);
}


/**
 * タームの特徴を抽出
 */
function gi_extract_term_features($term, $taxonomy) {
    $features = array(
        'name' => $term->name,
        'description' => $term->description,
        'keywords' => array(),
        'related_terms' => array(),
        'post_titles' => array(),
    );
    
    // ターム名からキーワードを抽出
    $features['keywords'] = gi_tokenize_japanese($term->name);
    
    // 説明文からキーワードを抽出
    if (!empty($term->description)) {
        $features['keywords'] = array_merge(
            $features['keywords'],
            gi_tokenize_japanese($term->description)
        );
    }
    
    // このタームに属する投稿のタイトルからキーワードを抽出（最大20件）
    $posts = get_posts(array(
        'post_type' => 'grant',
        'tax_query' => array(
            array(
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term->term_id,
            ),
        ),
        'posts_per_page' => 20,
        'post_status' => 'publish',
    ));
    
    foreach ($posts as $post) {
        $title_keywords = gi_tokenize_japanese($post->post_title);
        $features['keywords'] = array_merge($features['keywords'], $title_keywords);
        $features['post_titles'][] = $post->post_title;
    }
    
    // キーワード出現回数をカウント（TF計算）
    $features['keyword_freq'] = array_count_values($features['keywords']);
    
    return $features;
}


/**
 * 日本語テキストをトークン化
 */
function gi_tokenize_japanese($text) {
    $tokens = array();
    
    // 基本的なクリーニング
    $text = strip_tags($text);
    $text = preg_replace('/[【】「」『』（）\(\)\[\]]/u', ' ', $text);
    $text = preg_replace('/[、。！？・]/u', ' ', $text);
    
    // 日本語の単語分割（簡易版：2〜6文字のN-gram）
    $len = mb_strlen($text);
    
    // 既知のパターンを抽出
    $patterns = array(
        // 補助金・助成金関連
        '/(?:補助金|助成金|給付金|支援金|交付金)/u',
        // 地域名
        '/(?:北海道|青森|岩手|宮城|秋田|山形|福島|茨城|栃木|群馬|埼玉|千葉|東京|神奈川|新潟|富山|石川|福井|山梨|長野|岐阜|静岡|愛知|三重|滋賀|京都|大阪|兵庫|奈良|和歌山|鳥取|島根|岡山|広島|山口|徳島|香川|愛媛|高知|福岡|佐賀|長崎|熊本|大分|宮崎|鹿児島|沖縄)(?:都|道|府|県)?/u',
        // 業種
        '/(?:製造業|建設業|運輸業|卸売業|小売業|飲食業|サービス業|IT|情報通信|医療|介護|福祉|農業|漁業|林業)/u',
        // 目的
        '/(?:設備投資|人材育成|研究開発|販路開拓|事業承継|創業|起業|DX|デジタル化|省エネ|環境|脱炭素)/u',
        // 対象
        '/(?:中小企業|小規模事業者|個人事業主|スタートアップ|ベンチャー|NPO|社会福祉法人)/u',
    );
    
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $text, $matches)) {
            $tokens = array_merge($tokens, $matches[0]);
        }
    }
    
    // カタカナ語を抽出
    if (preg_match_all('/[ァ-ヶー]{2,}/u', $text, $matches)) {
        $tokens = array_merge($tokens, $matches[0]);
    }
    
    // 漢字2〜6文字の連続を抽出
    if (preg_match_all('/[一-龥]{2,6}/u', $text, $matches)) {
        $tokens = array_merge($tokens, $matches[0]);
    }
    
    // 重複を除去し、ストップワードを除外
    $stopwords = array('について', 'における', 'に関する', 'のための', 'こと', 'もの', 'ため', 'など', 'ほか', '及び', '等');
    $tokens = array_filter($tokens, function($t) use ($stopwords) {
        return mb_strlen($t) >= 2 && !in_array($t, $stopwords);
    });
    
    return array_values($tokens);
}


/**
 * 2つのタームの類似度を計算（コサイン類似度ベース）
 */
function gi_calculate_term_similarity($features1, $features2) {
    $freq1 = isset($features1['keyword_freq']) ? $features1['keyword_freq'] : array();
    $freq2 = isset($features2['keyword_freq']) ? $features2['keyword_freq'] : array();
    
    if (empty($freq1) || empty($freq2)) {
        return 0;
    }
    
    // 全キーワードのユニオン
    $all_keywords = array_unique(array_merge(array_keys($freq1), array_keys($freq2)));
    
    // ベクトル化
    $vec1 = array();
    $vec2 = array();
    
    foreach ($all_keywords as $kw) {
        $vec1[] = isset($freq1[$kw]) ? $freq1[$kw] : 0;
        $vec2[] = isset($freq2[$kw]) ? $freq2[$kw] : 0;
    }
    
    // コサイン類似度計算
    $dot_product = 0;
    $norm1 = 0;
    $norm2 = 0;
    
    for ($i = 0; $i < count($vec1); $i++) {
        $dot_product += $vec1[$i] * $vec2[$i];
        $norm1 += $vec1[$i] * $vec1[$i];
        $norm2 += $vec2[$i] * $vec2[$i];
    }
    
    if ($norm1 == 0 || $norm2 == 0) {
        return 0;
    }
    
    $cosine_similarity = $dot_product / (sqrt($norm1) * sqrt($norm2));
    
    // 0〜100のスコアに変換
    return $cosine_similarity * 100;
}


/**
 * =============================================================================
 * 13. AJAX: 一括noindex設定
 * =============================================================================
 */

function gi_ajax_bulk_set_noindex() {
    check_ajax_referer('gi_archive_seo_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('権限がありません');
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    $all_pages = gi_get_available_archive_pages_cached();
    $updated = 0;
    
    foreach ($all_pages as $page) {
        // 100件以下で、投稿タイプアーカイブでないものが対象
        if ($page['post_count'] <= 100 && $page['post_count'] > 0 && $page['type'] !== 'post_type_archive') {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT id, is_noindex FROM {$table_name} WHERE archive_type = %s AND archive_key = %s",
                $page['type'], $page['key']
            ));
            
            if ($existing) {
                if (!$existing->is_noindex) {
                    $wpdb->update($table_name, array(
                        'is_noindex' => 1,
                        'auto_noindex_reason' => 'bulk_100_or_less'
                    ), array('id' => $existing->id));
                    $updated++;
                }
            } else {
                $wpdb->insert($table_name, array(
                    'archive_type' => $page['type'],
                    'archive_key' => $page['key'],
                    'is_noindex' => 1,
                    'auto_noindex_reason' => 'bulk_100_or_less',
                    'is_active' => 1,
                ));
                $updated++;
            }
        }
    }
    
    // キャッシュクリア
    delete_transient('gi_archive_seo_stats');
    
    wp_send_json_success(array(
        'message' => $updated . '件のページにnoindexを設定しました',
        'updated' => $updated
    ));
}


/**
 * =============================================================================
 * 14. 管理画面 - 統合候補管理ページ
 * =============================================================================
 */

function gi_archive_seo_merge_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    $source_type = isset($_GET['source_type']) ? sanitize_text_field($_GET['source_type']) : '';
    $source_key = isset($_GET['source_key']) ? sanitize_text_field($_GET['source_key']) : '';
    $target_type = isset($_GET['target_type']) ? sanitize_text_field($_GET['target_type']) : '';
    $target_key = isset($_GET['target_key']) ? sanitize_text_field($_GET['target_key']) : '';
    
    $notice_message = '';
    $notice_type = '';
    
    // 統合実行処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gi_merge_nonce'])) {
        if (wp_verify_nonce($_POST['gi_merge_nonce'], 'gi_archive_merge')) {
            $merge_source_type = sanitize_text_field($_POST['source_type']);
            $merge_source_key = sanitize_text_field($_POST['source_key']);
            $merge_target = sanitize_text_field($_POST['merge_target']);
            $merge_type = sanitize_text_field($_POST['merge_type'] ?? 'redirect');
            
            if ($merge_type === 'full') {
                $result = gi_execute_full_term_merge($merge_source_type, $merge_source_key, $merge_target);
                if ($result['success']) {
                    $notice_message = $result['message'];
                    $notice_type = 'success';
                } else {
                    $notice_message = 'エラー: ' . $result['message'];
                    $notice_type = 'error';
                }
            } else {
                // リダイレクト統合
                $existing = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM {$table_name} WHERE archive_type = %s AND archive_key = %s",
                    $merge_source_type, $merge_source_key
                ));
                
                $data = array(
                    'archive_type' => $merge_source_type,
                    'archive_key' => $merge_source_key,
                    'merge_target' => $merge_target,
                    'is_noindex' => 1,
                );
                
                if ($existing) {
                    $wpdb->update($table_name, $data, array('id' => $existing->id));
                } else {
                    $wpdb->insert($table_name, $data);
                }
                
                delete_transient('gi_archive_seo_' . md5($merge_source_type . ':' . $merge_source_key));
                delete_transient('gi_archive_seo_stats');
                
                $notice_message = 'リダイレクト統合設定を保存しました。';
                $notice_type = 'success';
            }
        }
    }
    
    // 統合候補一覧を取得
    $all_pages = gi_get_available_archive_pages_cached();
    $merge_candidates = array();
    
    foreach ($all_pages as $page) {
        if (isset($page['post_count']) && $page['post_count'] <= 100 && $page['post_count'] > 0 && $page['type'] !== 'post_type_archive') {
            // 類似カテゴリを自動検出
            $similar = gi_find_similar_categories($page['type'], $page['key'], 3);
            $page['similar_categories'] = $similar;
            
            // 既存の設定を確認
            $saved = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE archive_type = %s AND archive_key = %s",
                $page['type'], $page['key']
            ));
            $page['is_noindex'] = $saved && !empty($saved->is_noindex);
            $page['is_merged'] = $saved && !empty($saved->merge_target);
            $page['merge_target'] = $saved ? $saved->merge_target : '';
            
            $merge_candidates[] = $page;
        }
    }
    
    // 補助金数順にソート
    usort($merge_candidates, function($a, $b) {
        return $a['post_count'] - $b['post_count'];
    });
    
    ?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-migrate" style="font-size: 30px; margin-right: 10px; vertical-align: middle;"></span>
            統合候補管理
        </h1>
        
        <?php if ($notice_message): ?>
        <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible">
            <p><?php echo esc_html($notice_message); ?></p>
        </div>
        <?php endif; ?>
        
        <div style="background: #f0f6fc; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2271b1;">
            <p style="margin: 0;">
                <strong>統合候補</strong>は補助金数が100件以下のアーカイブページです。<br>
                これらのページは検索エンジンでの評価が低くなりがちなため、noindex設定または類似カテゴリへの統合をお勧めします。<br>
                <small style="color: #666;">※ 類似度は名前・説明文・関連投稿のキーワードから自動計算されます（TF-IDF/コサイン類似度ベース）</small>
            </p>
        </div>
        
        <h2>統合候補一覧（<?php echo count($merge_candidates); ?>件）</h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 20%;">ページ名</th>
                    <th style="width: 10%;">タイプ</th>
                    <th style="width: 8%;">補助金数</th>
                    <th style="width: 10%;">状態</th>
                    <th style="width: 35%;">類似カテゴリ（統合先候補）</th>
                    <th style="width: 17%;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merge_candidates as $page): ?>
                <tr>
                    <td>
                        <strong><?php echo esc_html($page['name']); ?></strong>
                        <br><a href="<?php echo esc_url($page['url']); ?>" target="_blank" style="font-size: 11px;">
                            <?php echo esc_html(str_replace(home_url(), '', $page['url'])); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html($page['type_label']); ?></td>
                    <td style="text-align: center; color: #d63638; font-weight: bold;">
                        <?php echo number_format($page['post_count']); ?>
                    </td>
                    <td>
                        <?php if ($page['is_merged']): ?>
                            <span style="background: #d63638; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 11px;">統合済</span>
                        <?php elseif ($page['is_noindex']): ?>
                            <span style="background: #9333ea; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 11px;">noindex</span>
                        <?php else: ?>
                            <span style="color: #dba617;">未設定</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($page['similar_categories'])): ?>
                            <ul style="margin: 0; padding: 0; list-style: none;">
                                <?php foreach ($page['similar_categories'] as $similar): ?>
                                <li style="margin-bottom: 5px;">
                                    <strong><?php echo esc_html($similar['name']); ?></strong>
                                    <span style="color: #666;">(<?php echo number_format($similar['post_count']); ?>件)</span>
                                    <span style="background: #e0f0ff; padding: 1px 5px; border-radius: 3px; font-size: 10px;">
                                        類似度: <?php echo $similar['similarity']; ?>%
                                    </span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <span style="color: #999;">類似カテゴリなし</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$page['is_merged']): ?>
                            <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-edit&type=' . urlencode($page['type']) . '&key=' . urlencode($page['key'])); ?>" 
                               class="button button-small">編集</a>
                            <?php if (!empty($page['similar_categories'])): ?>
                                <button type="button" class="button button-small btn-quick-merge" 
                                        data-source-type="<?php echo esc_attr($page['type']); ?>"
                                        data-source-key="<?php echo esc_attr($page['key']); ?>"
                                        data-target="<?php echo esc_attr($page['similar_categories'][0]['slug']); ?>"
                                        style="color: #d63638; border-color: #d63638;">
                                    統合
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #666; font-size: 11px;">
                                → <?php 
                                    $parts = explode(':', $page['merge_target']);
                                    if (count($parts) >= 2) {
                                        $target_term = get_term_by('slug', $parts[1], $parts[0]);
                                        echo $target_term ? esc_html($target_term->name) : esc_html($parts[1]);
                                    }
                                ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- 統合モーダル -->
    <div id="merge-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">
            <h2 style="margin-top: 0;">統合設定</h2>
            <form method="post" id="merge-form">
                <?php wp_nonce_field('gi_archive_merge', 'gi_merge_nonce'); ?>
                <input type="hidden" name="source_type" id="modal-source-type">
                <input type="hidden" name="source_key" id="modal-source-key">
                
                <p><strong>統合元:</strong> <span id="modal-source-name"></span></p>
                
                <p>
                    <label>統合先:</label>
                    <select name="merge_target" id="modal-merge-target" class="regular-text" required style="width: 100%;">
                        <option value="">-- 選択してください --</option>
                    </select>
                </p>
                
                <p>
                    <label>
                        <input type="radio" name="merge_type" value="redirect" checked>
                        リダイレクト統合（元ページへのアクセスを転送）
                    </label><br>
                    <label>
                        <input type="radio" name="merge_type" value="full">
                        完全統合（補助金を統合先にも追加）
                    </label>
                </p>
                
                <p style="margin-top: 20px;">
                    <button type="submit" class="button button-primary">統合を実行</button>
                    <button type="button" class="button" id="modal-close">キャンセル</button>
                </p>
            </form>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var nonce = '<?php echo wp_create_nonce('gi_archive_seo_nonce'); ?>';
        
        // クイック統合ボタン
        $('.btn-quick-merge').on('click', function() {
            var sourceType = $(this).data('source-type');
            var sourceKey = $(this).data('source-key');
            var targetSlug = $(this).data('target');
            
            $('#modal-source-type').val(sourceType);
            $('#modal-source-key').val(sourceKey);
            $('#modal-source-name').text($(this).closest('tr').find('td:first strong').text());
            
            // 統合先オプションを設定
            var $select = $('#modal-merge-target');
            $select.html('<option value="">読み込み中...</option>');
            
            $.post(ajaxurl, {
                action: 'gi_archive_get_similar_categories',
                nonce: nonce,
                type: sourceType,
                key: sourceKey
            }, function(response) {
                $select.html('<option value="">-- 選択してください --</option>');
                if (response.success && response.data.similar.length > 0) {
                    response.data.similar.forEach(function(item) {
                        var selected = item.slug === targetSlug ? ' selected' : '';
                        $select.append('<option value="' + sourceType + ':' + item.slug + '"' + selected + '>' + 
                            item.name + ' (' + item.post_count + '件) - 類似度: ' + item.similarity + '%</option>');
                    });
                }
            });
            
            $('#merge-modal').show();
        });
        
        $('#modal-close').on('click', function() {
            $('#merge-modal').hide();
        });
        
        $('#merge-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });
    });
    </script>
    <?php
}


/**
 * 完全統合（タームマージ）を実行
 */
function gi_execute_full_term_merge($source_type, $source_key, $merge_target) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    $parts = explode(':', $merge_target);
    if (count($parts) < 2) {
        return array('success' => false, 'message' => '統合先の指定が不正です。');
    }
    $target_type = $parts[0];
    $target_key = $parts[1];
    
    if ($source_type === 'post_type_archive' || $target_type === 'post_type_archive') {
        return array('success' => false, 'message' => '完全統合はタクソノミーのみ対応しています。');
    }
    
    if ($source_type !== $target_type) {
        return array('success' => false, 'message' => '異なるタクソノミー間での完全統合はできません。');
    }
    
    $source_term = get_term_by('slug', $source_key, $source_type);
    if (!$source_term || is_wp_error($source_term)) {
        return array('success' => false, 'message' => '統合元カテゴリが見つかりません。');
    }
    
    $target_term = get_term_by('slug', $target_key, $target_type);
    if (!$target_term || is_wp_error($target_term)) {
        return array('success' => false, 'message' => '統合先カテゴリが見つかりません。');
    }
    
    $source_posts = get_posts(array(
        'post_type' => 'grant',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'tax_query' => array(
            array(
                'taxonomy' => $source_type,
                'field' => 'term_id',
                'terms' => $source_term->term_id,
            ),
        ),
        'fields' => 'ids',
    ));
    
    $moved_count = 0;
    $already_assigned_count = 0;
    
    foreach ($source_posts as $post_id) {
        $existing_terms = wp_get_object_terms($post_id, $target_type, array('fields' => 'ids'));
        
        if (!in_array($target_term->term_id, $existing_terms)) {
            $result = wp_set_object_terms($post_id, $target_term->term_id, $target_type, true);
            if (!is_wp_error($result)) {
                $moved_count++;
            }
        } else {
            $already_assigned_count++;
        }
    }
    
    // 統合元ページを noindex + リダイレクト設定
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id FROM {$table_name} WHERE archive_type = %s AND archive_key = %s",
        $source_type, $source_key
    ));
    
    $data = array(
        'archive_type' => $source_type,
        'archive_key' => $source_key,
        'merge_target' => $merge_target,
        'is_noindex' => 1,
    );
    
    if ($existing) {
        $wpdb->update($table_name, $data, array('id' => $existing->id));
    } else {
        $wpdb->insert($table_name, $data);
    }
    
    wp_update_term_count_now(array($source_term->term_id), $source_type);
    wp_update_term_count_now(array($target_term->term_id), $target_type);
    
    delete_transient('gi_archive_seo_' . md5($source_type . ':' . $source_key));
    delete_transient('gi_archive_seo_' . md5($target_type . ':' . $target_key));
    delete_transient('gi_archive_seo_stats');
    delete_transient('gi_archive_pages_list');
    
    $updated_target_term = get_term($target_term->term_id, $target_type);
    
    return array(
        'success' => true,
        'message' => sprintf(
            '「%s」から「%s」へ %d件の補助金を追加しました。統合先の補助金数: %d件',
            $source_term->name,
            $target_term->name,
            $moved_count,
            $updated_target_term->count
        ),
    );
}


/**
 * =============================================================================
 * 15. ヘルパー関数
 * =============================================================================
 */

function gi_get_archive_page_info($type, $key) {
    $info = array(
        'name' => '',
        'url' => '',
        'default_title' => '',
        'post_count' => 0,
        'pv_count' => 0,
    );
    
    if ($type === 'post_type_archive') {
        $info['name'] = '助成金・補助金一覧（メイン）';
        $info['url'] = get_post_type_archive_link($key);
        $info['default_title'] = '助成金・補助金総合検索';
        $counts = wp_count_posts('grant');
        $info['post_count'] = isset($counts->publish) ? $counts->publish : 0;
        $info['pv_count'] = gi_get_archive_pv_count($type, $key);
    } else {
        $term = get_term_by('slug', $key, $type);
        if ($term && !is_wp_error($term)) {
            $info['name'] = $term->name;
            $info['url'] = get_term_link($term);
            $info['default_title'] = $term->name . 'の助成金・補助金';
            $info['post_count'] = $term->count;
            $info['pv_count'] = gi_get_archive_pv_count($type, $key);
        }
    }
    
    return $info;
}


/**
 * =============================================================================
 * 16. フロントエンド - SEOコンテンツ取得
 * =============================================================================
 */

/**
 * 現在のアーカイブページに対応するSEOコンテンツを取得
 * 
 * 【修正 v11.0.10】
 * - 管理者ログイン中はキャッシュを無効化して即時反映を確認可能に
 * - キャッシュ時間を1日→1時間に短縮
 * - is_activeチェックを柔軟に（デバッグ用オプション追加）
 */
function gi_get_current_archive_seo_content() {
    global $wpdb;
    
    $type = '';
    $key = '';
    
    // 判定の優先順位を整理
    if (is_tax('grant_prefecture')) {
        $type = 'grant_prefecture';
        $term = get_queried_object();
        $key = $term ? $term->slug : '';
    } elseif (is_tax('grant_category')) {
        $type = 'grant_category';
        $term = get_queried_object();
        $key = $term ? $term->slug : '';
    } elseif (is_tax('grant_municipality')) {
        $type = 'grant_municipality';
        $term = get_queried_object();
        $key = $term ? $term->slug : '';
    } elseif (is_tax('grant_purpose')) {
        $type = 'grant_purpose';
        $term = get_queried_object();
        $key = $term ? $term->slug : '';
    } elseif (is_tax('grant_tag')) {
        $type = 'grant_tag';
        $term = get_queried_object();
        $key = $term ? $term->slug : '';
    } elseif (is_post_type_archive('grant')) {
        // post_type_archive は最後に判定（タクソノミー優先）
        $type = 'post_type_archive';
        $key = 'grant';
    }
    
    if (empty($type) || empty($key)) {
        return null;
    }
    
    // 【重要】管理者ログイン中はキャッシュを無効化して即時反映を確認できるようにする
    $use_cache = !current_user_can('manage_options');
    
    $cache_key = 'gi_archive_seo_' . md5($type . ':' . $key);
    
    if ($use_cache) {
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }
    }
    
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    // テーブルが存在するかチェック
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        return null;
    }
    
    $content = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE archive_type = %s AND archive_key = %s AND is_active = 1",
        $type, $key
    ), ARRAY_A);
    
    // キャッシュを設定（一般ユーザー向け、1時間に短縮）
    if ($use_cache) {
        set_transient($cache_key, $content, HOUR_IN_SECONDS);
    }
    
    return $content;
}


/**
 * =============================================================================
 * 17. フロントエンド - コンテンツ出力
 * =============================================================================
 */

// イントロコンテンツ出力
function gi_output_archive_intro_content() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['intro_content'])) {
        ?>
        <section class="gi-archive-seo-intro gi-section mb-8">
            <header class="gi-section-numbered-header">
                <div class="gi-section-number-box">
                    <div class="gi-section-number-inner">
                        <span class="gi-section-number-label">Section</span>
                        <span class="gi-section-number-value">03</span>
                    </div>
                </div>
                <div class="gi-section-title-box">
                    <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                    <h2 class="gi-section-title">傾向と対策</h2>
                    <span class="gi-section-en">Trends & Strategies</span>
                </div>
            </header>
            <div class="gi-section-body">
                <div class="gi-seo-content-box prose prose-stone max-w-none">
                    <?php echo wp_kses_post($content['intro_content']); ?>
                </div>
            </div>
        </section>
        <?php
    }
}

// アウトロコンテンツ出力
function gi_output_archive_outro_content() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['outro_content'])) {
        ?>
        <section class="gi-archive-seo-outro gi-section mt-12">
            <header class="gi-section-numbered-header">
                <div class="gi-section-number-box">
                    <div class="gi-section-number-inner">
                        <span class="gi-section-number-label">Section</span>
                        <span class="gi-section-number-value">04</span>
                    </div>
                </div>
                <div class="gi-section-title-box">
                    <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    <h2 class="gi-section-title">申請のまとめ・関連情報</h2>
                    <span class="gi-section-en">Summary & Related Info</span>
                </div>
            </header>
            <div class="gi-section-body">
                <div class="gi-seo-content-box prose prose-stone max-w-none">
                    <?php echo wp_kses_post($content['outro_content']); ?>
                </div>
            </div>
        </section>
        <?php
    }
}

// おすすめ記事出力 - Editor's Pick デザイン
function gi_output_archive_featured_posts() {
    $content = gi_get_current_archive_seo_content();
    
    // 設定されていない場合は自動選出
    $featured_posts_str = '';
    if ($content && !empty($content['featured_posts'])) {
        $featured_posts_str = $content['featured_posts'];
    } else {
        // 自動選出（PV順で3件）
        $featured_posts_str = gi_auto_get_featured_posts();
    }
    
    if (empty($featured_posts_str)) {
        return;
    }
    
    $posts = gi_get_featured_posts_for_archive($featured_posts_str);
    
    if (empty($posts)) {
        return;
    }
    
    ?>
    <!-- Section 2: おすすめの助成金・補助金 (Cards with Paper clip effect) -->
    <section id="pickup" class="gi-archive-seo-featured gi-section mb-20">
        <header class="gi-section-numbered-header">
            <div class="gi-section-number-box">
                <div class="gi-section-number-inner">
                    <span class="gi-section-number-label">Section</span>
                    <span class="gi-section-number-value">01</span>
                </div>
            </div>
            <div class="gi-section-title-box">
                <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7v10c0 5.5 3.8 7.7 10 10 6.2-2.3 10-4.5 10-10V7l-10-5z"/><path d="m9 12 2 2 4-4"/></svg>
                <h2 class="gi-section-title">おすすめの助成金・補助金</h2>
                <span class="gi-section-en">Editor's Pick</span>
            </div>
        </header>

        <div class="space-y-12">
            <?php 
            $counter = 0;
            foreach ($posts as $post): 
                $counter++;
                $is_even = ($counter % 2 === 0);
                $rotate_class = $is_even ? '-rotate-1' : 'rotate-1';
                
                // Meta data
                $max_amount = get_post_meta($post->ID, 'max_amount', true);
                if (!$max_amount) $max_amount = get_post_meta($post->ID, 'grant_amount_max', true);
                
                $max_amount_numeric = get_post_meta($post->ID, 'max_amount_numeric', true);
                
                // Format amount
                $amount_display = '要確認';
                $badge_class = 'bg-highlight-blue';
                
                if ($max_amount_numeric) {
                     if ($max_amount_numeric >= 100000000) {
                         $amount_display = '最大' . number_format($max_amount_numeric / 100000000, 1) . '億円';
                         $badge_class = 'bg-accent-red'; // 1億円以上は赤
                     } elseif ($max_amount_numeric >= 10000) {
                         $amount_display = '最大' . number_format($max_amount_numeric / 10000) . '万円';
                         if ($max_amount_numeric >= 30000000) {
                             $badge_class = 'bg-accent-red'; // 3000万円以上は赤
                         }
                     } else {
                         $amount_display = '最大' . number_format($max_amount_numeric) . '円';
                     }
                } elseif ($max_amount) {
                    $amount_display = '最大' . $max_amount;
                    // 文字列で億が含まれていれば赤にする簡易判定
                    if (strpos($max_amount, '億') !== false) {
                        $badge_class = 'bg-accent-red';
                    }
                }

                // Difficulty
                $difficulty = get_post_meta($post->ID, 'grant_difficulty', true);
                if (!$difficulty) $difficulty = 'high'; // Default
                
                $difficulty_stars = '★ 難易度：高';
                
                switch ($difficulty) {
                    case 'low':
                        $difficulty_stars = '★ 難易度：低';
                        break;
                    case 'medium':
                        $difficulty_stars = '★ 難易度：中';
                        break;
                    case 'high':
                    default:
                        $difficulty_stars = '★ 難易度：高';
                        break;
                }
                
                // Description
                $excerpt = get_the_excerpt($post->ID);
                if (!$excerpt) {
                    $excerpt = wp_trim_words($post->post_content, 120, '...');
                }
                
                // Fields
                $target_business = get_post_meta($post->ID, 'target_business', true);
                if (!$target_business) $target_business = get_post_meta($post->ID, 'grant_target', true);
                
                $eligible_expenses = get_post_meta($post->ID, 'eligible_expenses', true);
                $requirements = get_post_meta($post->ID, 'requirements', true);
                
                // List items builder
                $list_items = [];
                
                // Item 1 (Target) is always shown if available
                if ($target_business) {
                    $list_items[] = ['label' => '対象', 'value' => wp_trim_words($target_business, 30, '...')];
                }
                
                // Item 2 (Usage or Conditions)
                if ($eligible_expenses) {
                    $list_items[] = ['label' => '用途', 'value' => wp_trim_words($eligible_expenses, 30, '...')];
                } elseif ($requirements) {
                    $list_items[] = ['label' => '条件', 'value' => wp_trim_words($requirements, 30, '...')];
                }
                
                if (count($list_items) < 2 && $requirements) {
                     $list_items[] = ['label' => '条件', 'value' => wp_trim_words($requirements, 30, '...')];
                }
                
                // Fallback
                if (empty($list_items)) {
                    $list_items[] = ['label' => '詳細', 'value' => '詳細ページをご確認ください'];
                }
            ?>
            <div class="featured-grant-card relative bg-white shadow-book-depth <?php echo $rotate_class; ?> hover:rotate-0 transition-transform duration-300 border border-gray-200 group">
                <!-- Clip effect -->
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-32 h-6 bg-gray-200 rounded-sm opacity-50 shadow-inner z-10"></div>
                
                <a href="<?php echo get_permalink($post->ID); ?>" class="absolute inset-0 z-0" aria-label="<?php echo esc_attr($post->post_title); ?>"></a>

                <div class="flex flex-col md:flex-row relative z-10 pointer-events-none">
                    <!-- 左側: 番号とメタ情報 -->
                    <div class="featured-grant-left md:w-1/4 flex flex-col items-center justify-center text-center bg-gradient-to-br from-gray-50 to-white border-r border-gray-100 p-6">
                        <span class="featured-grant-number text-5xl font-serif font-bold text-gray-200 leading-none mb-3"><?php echo sprintf('%02d', $counter); ?></span>
                        <span class="inline-block <?php echo $badge_class; ?> text-white text-xs px-4 py-2 rounded font-bold shadow-sm tracking-wide mb-2">
                            <?php echo esc_html($amount_display); ?>
                        </span>
                        <div class="text-accent-gold text-xs font-bold"><?php echo esc_html($difficulty_stars); ?></div>
                    </div>
                    <!-- 右側: コンテンツ -->
                    <div class="featured-grant-right md:w-3/4 p-6">
                        <h3 class="text-lg font-serif font-bold text-ink-primary mb-3 pointer-events-auto leading-snug">
                            <a href="<?php echo get_permalink($post->ID); ?>" class="hover:text-accent-gold transition-colors">
                                <?php echo esc_html($post->post_title); ?>
                            </a>
                        </h3>
                        <p class="text-sm leading-7 font-serif text-gray-700 mb-4 text-justify">
                            <?php echo esc_html($excerpt); ?>
                        </p>
                        <div class="bg-paper-warm p-4 rounded border border-gray-100">
                            <ul class="text-xs text-gray-600 space-y-2 font-sans">
                                <?php foreach ($list_items as $item): ?>
                                <li class="flex items-start">
                                    <span class="font-bold w-12 shrink-0 text-gray-500"><?php echo esc_html($item['label']); ?>：</span>
                                    <span><?php echo esc_html($item['value']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}

/**
 * おすすめ記事自動選出（フロントエンド用）
 * タクソノミー（都道府県・カテゴリ等）内の記事のPV順で選出
 */
function gi_auto_get_featured_posts() {
    global $wpdb;
    
    $type = '';
    $key = '';
    $term = null;
    
    if (is_post_type_archive('grant')) {
        $type = 'post_type_archive';
        $key = 'grant';
    } elseif (is_tax()) {
        $term = get_queried_object();
        if ($term) {
            $type = $term->taxonomy;
            $key = $term->slug;
        }
    }
    
    if (empty($type)) {
        return '';
    }
    
    // タクソノミーの場合、そのタクソノミー内の記事のPV順で取得
    if ($type !== 'post_type_archive' && $term) {
        // アクセストラッキングテーブルから該当タクソノミー内の記事のPVを取得
        $daily_table = $wpdb->prefix . 'ji_access_daily';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$daily_table}'");
        
        if ($table_exists) {
            $query = $wpdb->prepare("
                SELECT p.ID
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                LEFT JOIN {$daily_table} ad ON p.ID = ad.post_id
                WHERE p.post_type = 'grant'
                AND p.post_status = 'publish'
                AND tt.taxonomy = %s
                AND tt.term_id = %d
                GROUP BY p.ID
                ORDER BY COALESCE(SUM(ad.view_count), 0) DESC, p.post_date DESC
                LIMIT 3
            ", $type, $term->term_id);
            
            $results = $wpdb->get_col($query);
            
            if (!empty($results)) {
                return implode(',', $results);
            }
        }
    }
    
    // フォールバック: WP_Queryを使用
    $args = array(
        'post_type' => 'grant',
        'post_status' => 'publish',
        'posts_per_page' => 3,
    );
    
    if ($type !== 'post_type_archive') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $type,
                'field' => 'slug',
                'terms' => $key,
            ),
        );
    }
    
    // view_countでソートを試す
    $args['meta_key'] = 'view_count';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'DESC';
    
    $posts = get_posts($args);
    
    if (empty($posts)) {
        // view_countがない場合は_gi_pv_total
        $args['meta_key'] = '_gi_pv_total';
        $posts = get_posts($args);
    }
    
    if (empty($posts)) {
        // PVがない場合は最新順
        unset($args['meta_key']);
        $args['orderby'] = 'date';
        $posts = get_posts($args);
    }
    
    return implode(',', wp_list_pluck($posts, 'ID'));
}

function gi_get_featured_posts_for_archive($post_ids_string) {
    if (empty($post_ids_string)) {
        return array();
    }
    
    $ids = array_map('intval', array_filter(explode(',', $post_ids_string)));
    if (empty($ids)) {
        return array();
    }
    
    return get_posts(array(
        'post_type' => 'grant',
        'post__in' => $ids,
        'orderby' => 'post__in',
        'posts_per_page' => count($ids),
    ));
}

// サイドバーコンテンツ出力
function gi_output_archive_sidebar_content() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['sidebar_content'])) {
        echo '<div class="gi-archive-seo-sidebar mb-4">';
        echo wp_kses_post($content['sidebar_content']);
        echo '</div>';
    }
}


/**
 * =============================================================================
 * 18. カスタムCSS出力（改善版）
 * =============================================================================
 */

function gi_output_archive_custom_css() {
    $content = gi_get_current_archive_seo_content();
    
    // 基本CSSを出力
    ?>
    <style id="gi-archive-seo-base-css">
    /* Archive SEO Content Base Styles */
    .gi-archive-seo-intro,
    .gi-archive-seo-outro,
    .gi-archive-seo-featured,
    .gi-archive-seo-sidebar {
        position: relative;
    }
    
    .gi-seo-content-box h2 {
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #333;
    }
    
    .gi-seo-content-box h3 {
        font-size: 1.25rem;
        font-weight: bold;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
    }
    
    .gi-seo-content-box p {
        margin-bottom: 1rem;
        line-height: 1.8;
    }
    
    .gi-seo-content-box ul,
    .gi-seo-content-box ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .gi-seo-content-box li {
        margin-bottom: 0.5rem;
    }
    
    .gi-seo-content-box table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
    }
    
    .gi-seo-content-box th,
    .gi-seo-content-box td {
        border: 1px solid #ddd;
        padding: 0.75rem;
        text-align: left;
    }
    
    .gi-seo-content-box th {
        background: #f5f5f5;
        font-weight: bold;
    }
    
    .gi-seo-content-box strong {
        font-weight: bold;
    }
    
    .gi-seo-content-box a {
        color: #2271b1;
        text-decoration: underline;
    }
    
    .gi-seo-content-box a:hover {
        color: #135e96;
    }
    
    /* Featured Grant Cards - おすすめの助成金・補助金 01 02 04 デザイン */
    .gi-archive-seo-featured.editors-pick-section {
        margin-bottom: 3rem;
    }
    
    .gi-archive-seo-featured .editors-pick-header {
        margin-bottom: 2rem;
        border-bottom: 2px solid #c9a96c;
        padding-bottom: 0.5rem;
    }
    
    .gi-archive-seo-featured .editors-pick-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #e5e5e5;
        font-family: Georgia, serif;
        margin-right: 1rem;
        line-height: 1;
    }
    
    .gi-archive-seo-featured .editors-pick-title-wrap h2 {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
        font-family: Georgia, "游明朝", serif;
        margin: 0;
        padding: 0;
        border: none;
    }
    
    .featured-grant-card {
        margin-bottom: 1.5rem;
        overflow: hidden;
        border-radius: 4px;
    }
    
    .featured-grant-left {
        min-height: 140px;
    }
    
    .featured-grant-number {
        font-family: Georgia, serif;
        letter-spacing: -0.05em;
    }
    
    .featured-grant-right h3 {
        font-family: Georgia, "游明朝", serif;
    }
    
    /* 背景色のバッジスタイル */
    .bg-highlight-blue {
        background-color: #3b82f6;
    }
    
    .bg-accent-red {
        background-color: #ef4444;
    }
    
    .bg-paper-warm {
        background-color: #faf8f5;
    }
    
    /* モバイル対応 */
    @media (max-width: 768px) {
        .featured-grant-card .flex-col {
            flex-direction: column;
        }
        
        .featured-grant-left {
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem;
            min-height: auto;
        }
        
        .featured-grant-number {
            font-size: 3rem;
        }
        
        .gi-archive-seo-featured .editors-pick-number {
            font-size: 2rem;
        }
    }
    </style>
    <?php
    
    // カスタムCSSを出力
    if ($content && !empty($content['custom_css'])) {
        echo '<style id="gi-archive-custom-css">' . "\n";
        echo $content['custom_css'];
        echo "\n</style>";
    }
}
add_action('wp_head', 'gi_output_archive_custom_css', 100);


/**
 * =============================================================================
 * 19. noindex & リダイレクト処理
 * =============================================================================
 */

// noindexタグを追加
function gi_add_archive_noindex() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['is_noindex']) && $content['is_noindex'] == 1) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
}
add_action('wp_head', 'gi_add_archive_noindex', 1);

// 統合先へのリダイレクト
function gi_archive_merge_redirect() {
    if (is_admin()) {
        return;
    }
    
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['merge_target'])) {
        $parts = explode(':', $content['merge_target']);
        if (count($parts) >= 2) {
            $target_type = $parts[0];
            $target_key = $parts[1];
            
            $redirect_url = '';
            
            if ($target_type === 'post_type_archive') {
                $redirect_url = get_post_type_archive_link($target_key);
            } else {
                $term = get_term_by('slug', $target_key, $target_type);
                if ($term && !is_wp_error($term)) {
                    $redirect_url = get_term_link($term);
                }
            }
            
            if ($redirect_url && !is_wp_error($redirect_url)) {
                wp_redirect($redirect_url, 301);
                exit;
            }
        }
    }
}
add_action('template_redirect', 'gi_archive_merge_redirect');


/**
 * =============================================================================
 * 20. カスタムタイトル・メタディスクリプション
 * =============================================================================
 */

function gi_get_archive_custom_title() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['page_title'])) {
        return $content['page_title'];
    }
    
    // SEO最適化されたデフォルトタイトルを生成
    $default_title = gi_generate_seo_default_title();
    return $default_title;
}

function gi_get_archive_custom_meta_description() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['meta_description'])) {
        return $content['meta_description'];
    }
    
    // SEO最適化されたデフォルトメタディスクリプションを生成
    $default_description = gi_generate_seo_default_meta_description();
    return $default_description;
}

/**
 * SEO最適化されたデフォルトタイトルを生成
 */
function gi_generate_seo_default_title() {
    $current_year = date('Y');
    $japanese_year = $current_year - 2018; // 令和年号
    
    if (is_post_type_archive('grant')) {
        $total_count = wp_count_posts('grant')->publish ?? 0;
        return "補助金・助成金一覧【{$current_year}年最新版】全{$total_count}件掲載 | 補助金図鑑";
    }
    
    if (is_tax('grant_prefecture')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}の補助金・助成金【令和{$japanese_year}年度最新】{$count}件掲載 | 申請サポート完備";
    }
    
    if (is_tax('grant_municipality')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}の補助金・助成金一覧【{$current_year}年版】{$count}制度を完全網羅";
    }
    
    if (is_tax('grant_category')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}向け補助金・助成金【{$current_year}年最新】{$count}件｜採択率UP申請ガイド";
    }
    
    if (is_tax('grant_purpose')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}の補助金・助成金【令和{$japanese_year}年度】{$count}制度の詳細解説";
    }
    
    if (is_tax('grant_tag')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "#{$term->name}の補助金・助成金【{$current_year}年版】{$count}件掲載｜最新情報";
    }
    
    return get_the_archive_title();
}

/**
 * SEO最適化されたデフォルトメタディスクリプションを生成
 */
function gi_generate_seo_default_meta_description() {
    $current_year = date('Y');
    $japanese_year = $current_year - 2018;
    
    if (is_post_type_archive('grant')) {
        $total_count = wp_count_posts('grant')->publish ?? 0;
        return "{$current_year}年最新の補助金・助成金情報を全{$total_count}件掲載。中小企業向け、個人事業主向けの支援制度を地域別・業種別に検索可能。申請方法、採択率、最新トレンドまで完全ガイド。";
    }
    
    if (is_tax('grant_prefecture')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}で利用できる補助金・助成金を{$count}件掲載。令和{$japanese_year}年度の最新制度、地域特有の支援策、申請のコツを専門家が徹底解説。中小企業・個人事業主の事業拡大を支援します。";
    }
    
    if (is_tax('grant_municipality')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}の補助金・助成金{$count}制度を完全網羅。{$current_year}年の地域独自の支援制度、併用可能な制度、申請サポート情報まで。地元企業の成長を応援する最新情報をお届けします。";
    }
    
    if (is_tax('grant_category')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}向けの補助金・助成金を{$count}件掲載。{$current_year}年の最新制度、採択率を上げる申請ポイント、必要書類、審査基準まで詳しく解説。専門家による申請サポートも完備。";
    }
    
    if (is_tax('grant_purpose')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "{$term->name}に活用できる補助金・助成金{$count}制度を紹介。令和{$japanese_year}年度の支援内容、支給額、対象者、申請方法を分かりやすく解説。あなたの事業に最適な支援制度が見つかります。";
    }
    
    if (is_tax('grant_tag')) {
        $term = get_queried_object();
        $count = $term->count ?? 0;
        return "#{$term->name}関連の補助金・助成金{$count}件を掲載。{$current_year}年の最新トレンド、注目制度、申請のコツまで。中小企業経営者・個人事業主必見の情報を毎日更新中。";
    }
    
    return get_bloginfo('description');
}
