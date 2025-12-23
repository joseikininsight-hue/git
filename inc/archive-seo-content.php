<?php
/**
 * Archive SEO Content Manager
 * 
 * 既存のアーカイブページ（grant CPT、各タクソノミー）に対して
 * 管理画面から個別にSEOコンテンツ（イントロ/アウトロ）を編集・追加するシステム
 * 
 * @package Grant_Insight_Perfect
 * @version 2.0.0
 * 
 * v2.0.0 Changes:
 * - カスタムCSSのクラス名を統一（.gi-seo-content-box内のh2/h3/p/strong等）
 * - サイドバーコンテンツ出力関数を追加
 * - 管理画面にPV数・補助金数カラムを追加
 * - アーカイブ統合機能（100件以下対象、noindex、リダイレクト）を実装
 */

if (!defined('ABSPATH')) exit;

/**
 * =============================================================================
 * 1. データベーステーブル作成
 * =============================================================================
 */

/**
 * アーカイブSEOコンテンツ用テーブルを作成
 */
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
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY archive_key_unique (archive_type, archive_key),
        KEY archive_type_idx (archive_type),
        KEY is_active_idx (is_active),
        KEY merge_target_idx (merge_target)
    ) {$charset_collate};";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // 既存テーブルにカラムを追加（アップグレード用）
    $existing_columns = $wpdb->get_col("DESCRIBE {$table_name}", 0);
    if (!in_array('merge_target', $existing_columns)) {
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN merge_target varchar(150) DEFAULT NULL COMMENT '統合先（type:key形式）'");
    }
    if (!in_array('is_noindex', $existing_columns)) {
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN is_noindex tinyint(1) DEFAULT 0 COMMENT 'noindexフラグ'");
    }
}
add_action('after_switch_theme', 'gi_create_archive_seo_table');

// テーブルが存在しない場合は作成
function gi_ensure_archive_seo_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        gi_create_archive_seo_table();
    } else {
        // カラム追加チェック
        $existing_columns = $wpdb->get_col("DESCRIBE {$table_name}", 0);
        if (!in_array('merge_target', $existing_columns)) {
            $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN merge_target varchar(150) DEFAULT NULL COMMENT '統合先（type:key形式）'");
        }
        if (!in_array('is_noindex', $existing_columns)) {
            $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN is_noindex tinyint(1) DEFAULT 0 COMMENT 'noindexフラグ'");
        }
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
        'アーカイブ統合',
        'アーカイブ統合',
        'manage_options',
        'gi-archive-seo-merge',
        'gi_archive_seo_merge_page'
    );
}
add_action('admin_menu', 'gi_add_archive_seo_menu');


/**
 * =============================================================================
 * 3. 管理画面 - ページ一覧
 * =============================================================================
 */

function gi_archive_seo_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    // 保存済みコンテンツを取得
    $saved_contents = $wpdb->get_results(
        "SELECT * FROM {$table_name} ORDER BY archive_type, archive_key",
        ARRAY_A
    );
    $saved_map = array();
    foreach ($saved_contents as $content) {
        $saved_map[$content['archive_type'] . ':' . $content['archive_key']] = $content;
    }
    
    // 利用可能なアーカイブページを取得
    $archive_pages = gi_get_available_archive_pages();
    
    // 未設定ページ数をカウント
    $unsaved_count = 0;
    foreach ($archive_pages as $page) {
        $key = $page['type'] . ':' . $page['key'];
        $saved = isset($saved_map[$key]) ? $saved_map[$key] : null;
        $has_content = $saved && ($saved['intro_content'] || $saved['outro_content'] || $saved['featured_posts']);
        if (!$has_content) {
            $unsaved_count++;
        }
    }
    
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
        
        <div class="gi-archive-seo-stats" style="display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap;">
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #2271b1;"><?php echo count($archive_pages); ?></div>
                <div style="color: #666;">利用可能ページ</div>
            </div>
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #00a32a;"><?php echo count($saved_contents); ?></div>
                <div style="color: #666;">設定済みページ</div>
            </div>
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #dba617;"><?php echo $unsaved_count; ?></div>
                <div style="color: #666;">未設定ページ</div>
            </div>
            <div style="background: #fff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; color: #d63638;">
                    <?php 
                    $merge_candidates = 0;
                    foreach ($archive_pages as $page) {
                        if (isset($page['post_count']) && $page['post_count'] <= 100 && $page['post_count'] > 0) {
                            $merge_candidates++;
                        }
                    }
                    echo $merge_candidates;
                    ?>
                </div>
                <div style="color: #666;">統合候補（100件以下）</div>
            </div>
        </div>
        
        <!-- フィルタータブ -->
        <div class="gi-archive-tabs" style="margin: 20px 0; border-bottom: 1px solid #ccc;">
            <a href="#" class="gi-tab active" data-filter="all" style="display: inline-block; padding: 10px 20px; text-decoration: none; border-bottom: 3px solid #2271b1; margin-bottom: -1px;">すべて</a>
            <a href="#" class="gi-tab" data-filter="unsaved" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: #dba617;">未設定のみ</a>
            <a href="#" class="gi-tab" data-filter="post_type_archive" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: #666;">投稿タイプ</a>
            <a href="#" class="gi-tab" data-filter="grant_prefecture" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: #666;">都道府県</a>
            <a href="#" class="gi-tab" data-filter="grant_category" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: #666;">カテゴリ</a>
            <a href="#" class="gi-tab" data-filter="grant_municipality" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: #666;">市区町村</a>
            <a href="#" class="gi-tab" data-filter="grant_purpose" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: #666;">目的</a>
            <a href="#" class="gi-tab" data-filter="merge_candidate" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: #d63638;">統合候補</a>
        </div>
        
        <table class="wp-list-table widefat fixed striped" id="gi-archive-table" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th style="width: 25%;">ページ名</th>
                    <th style="width: 10%;">タイプ</th>
                    <th style="width: 8%; cursor: pointer;" class="sortable" data-sort="post_count">
                        補助金数 <span class="dashicons dashicons-sort" style="font-size: 14px;"></span>
                    </th>
                    <th style="width: 8%; cursor: pointer;" class="sortable" data-sort="pv_count">
                        PV数 <span class="dashicons dashicons-sort" style="font-size: 14px;"></span>
                    </th>
                    <th style="width: 20%;">URL</th>
                    <th style="width: 10%;">状態</th>
                    <th style="width: 19%;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archive_pages as $page): 
                    $key = $page['type'] . ':' . $page['key'];
                    $saved = isset($saved_map[$key]) ? $saved_map[$key] : null;
                    $has_content = $saved && ($saved['intro_content'] || $saved['outro_content'] || $saved['featured_posts']);
                    $is_merge_candidate = isset($page['post_count']) && $page['post_count'] <= 100 && $page['post_count'] > 0;
                    $is_merged = $saved && !empty($saved['merge_target']);
                    $post_count = isset($page['post_count']) ? intval($page['post_count']) : 0;
                    $pv_count = isset($page['pv_count']) ? intval($page['pv_count']) : 0;
                ?>
                <tr data-type="<?php echo esc_attr($page['type']); ?>" 
                    data-merge-candidate="<?php echo $is_merge_candidate ? '1' : '0'; ?>"
                    data-has-content="<?php echo $has_content ? '1' : '0'; ?>"
                    data-post-count="<?php echo $post_count; ?>"
                    data-pv-count="<?php echo $pv_count; ?>">
                    <td>
                        <strong><?php echo esc_html($page['name']); ?></strong>
                        <?php if ($saved && $saved['page_title']): ?>
                            <br><small style="color: #666;">カスタムタイトル: <?php echo esc_html($saved['page_title']); ?></small>
                        <?php endif; ?>
                        <?php if ($is_merged): ?>
                            <br><span style="background: #d63638; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px;">統合済</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="gi-type-badge" style="
                            display: inline-block;
                            padding: 3px 8px;
                            border-radius: 3px;
                            font-size: 11px;
                            background: <?php 
                                echo $page['type'] === 'post_type_archive' ? '#2271b1' : 
                                    ($page['type'] === 'grant_prefecture' ? '#00a32a' : 
                                    ($page['type'] === 'grant_category' ? '#9333ea' : 
                                    ($page['type'] === 'grant_municipality' ? '#ea580c' : '#666'))); 
                            ?>;
                            color: #fff;
                        ">
                            <?php echo esc_html($page['type_label']); ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <?php 
                        $count_color = $post_count <= 100 ? '#d63638' : ($post_count <= 500 ? '#dba617' : '#00a32a');
                        ?>
                        <span style="color: <?php echo $count_color; ?>; font-weight: bold;">
                            <?php echo number_format($post_count); ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <?php echo number_format($pv_count); ?>
                    </td>
                    <td>
                        <a href="<?php echo esc_url($page['url']); ?>" target="_blank" style="word-break: break-all; font-size: 12px;">
                            <?php echo esc_html(str_replace(home_url(), '', $page['url'])); ?>
                            <span class="dashicons dashicons-external" style="font-size: 12px;"></span>
                        </a>
                    </td>
                    <td>
                        <?php if ($is_merged): ?>
                            <span style="color: #d63638;">
                                <span class="dashicons dashicons-migrate"></span> 統合済
                            </span>
                        <?php elseif ($has_content): ?>
                            <span style="color: #00a32a;">
                                <span class="dashicons dashicons-yes-alt"></span> 設定済
                            </span>
                        <?php else: ?>
                            <span style="color: #999;">未設定</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-edit&type=' . urlencode($page['type']) . '&key=' . urlencode($page['key'])); ?>" 
                           class="button button-primary button-small">
                            <?php echo $has_content ? '編集' : '設定'; ?>
                        </a>
                        <?php if ($is_merge_candidate && !$is_merged): ?>
                            <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge&source_type=' . urlencode($page['type']) . '&source_key=' . urlencode($page['key'])); ?>" 
                               class="button button-small" style="color: #d63638; border-color: #d63638;">
                                統合
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <style>
    .gi-tab:hover { background: #f0f0f1; }
    .gi-tab.active { color: #2271b1; font-weight: bold; }
    th.sortable:hover { background: #f0f0f1; }
    th.sortable.asc .dashicons:before { content: "\f142"; }
    th.sortable.desc .dashicons:before { content: "\f140"; }
    th.sortable.sorting-active { background: #e5f0f8; }
    th.sortable.sort-disabled { opacity: 0.5; }
    th.sortable.sort-disabled:hover { background: inherit; cursor: not-allowed; }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        var currentFilter = 'all';
        var sortColumn = null;
        var sortDirection = 'desc';
        var originalOrder = []; // 元の順序を保存
        
        // 初期順序を保存
        $('#gi-archive-table tbody tr').each(function(index) {
            $(this).data('original-order', index);
            originalOrder.push(this);
        });
        
        // タブフィルター
        $('.gi-tab').on('click', function(e) {
            e.preventDefault();
            currentFilter = $(this).data('filter');
            
            $('.gi-tab').removeClass('active').css('border-bottom', 'none');
            $(this).addClass('active').css('border-bottom', '3px solid #2271b1');
            
            // 「未設定」以外のフィルターに切り替えた場合、ソートをリセット
            if (currentFilter !== 'unsaved' && currentFilter !== 'merge_candidate') {
                resetSort();
            }
            
            applyFilter();
            updateSortableState();
        });
        
        function resetSort() {
            sortColumn = null;
            sortDirection = 'desc';
            $('.sortable').removeClass('asc desc sorting-active');
            
            // 元の順序に戻す
            var $tbody = $('#gi-archive-table tbody');
            $.each(originalOrder, function(index, row) {
                $tbody.append(row);
            });
        }
        
        function updateSortableState() {
            // 「未設定」または「統合候補」の時のみソート可能
            var sortAllowed = (currentFilter === 'unsaved' || currentFilter === 'merge_candidate');
            
            if (sortAllowed) {
                $('.sortable').css('cursor', 'pointer').removeClass('sort-disabled');
                $('.sortable .dashicons').show();
            } else {
                $('.sortable').css('cursor', 'not-allowed').addClass('sort-disabled');
                // 全てのフィルターではソートアイコンをグレーアウト
            }
        }
        
        function applyFilter() {
            $('tbody tr').each(function() {
                var $row = $(this);
                var show = true;
                
                if (currentFilter === 'all') {
                    show = true;
                } else if (currentFilter === 'unsaved') {
                    show = $row.data('has-content') !== 1;
                } else if (currentFilter === 'merge_candidate') {
                    show = $row.data('merge-candidate') === 1;
                } else {
                    show = $row.data('type') === currentFilter;
                }
                
                $row.toggle(show);
            });
        }
        
        // ソート機能（未設定/統合候補フィルター時のみ有効）
        $('.sortable').on('click', function() {
            // 「未設定」または「統合候補」フィルターでない場合はソート無効
            if (currentFilter !== 'unsaved' && currentFilter !== 'merge_candidate') {
                alert('ソートは「未設定のみ」または「統合候補」フィルター選択時のみ有効です。\n設定済みページの順番は維持されます。');
                return;
            }
            
            var column = $(this).data('sort');
            
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'desc';
            }
            
            // 見た目を更新
            $('.sortable').removeClass('asc desc sorting-active');
            $(this).addClass(sortDirection).addClass('sorting-active');
            
            // ソート実行（表示中の行のみ）
            var $tbody = $('#gi-archive-table tbody');
            var $rows = $tbody.find('tr:visible').get();
            
            $rows.sort(function(a, b) {
                var aVal = parseInt($(a).data(column.replace('_', '-'))) || 0;
                var bVal = parseInt($(b).data(column.replace('_', '-'))) || 0;
                
                if (sortDirection === 'asc') {
                    return aVal - bVal;
                } else {
                    return bVal - aVal;
                }
            });
            
            $.each($rows, function(index, row) {
                $tbody.append(row);
            });
        });
        
        // 初期状態でソート可能状態を更新
        updateSortableState();
    });
    </script>
    <?php
}


/**
 * =============================================================================
 * 4. 管理画面 - ページ編集
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
            
            // 既存レコードをチェック
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
    
    ?>
    <div class="wrap">
        <h1>
            <a href="<?php echo admin_url('admin.php?page=gi-archive-seo'); ?>" style="text-decoration: none; color: inherit;">
                <span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span>
            </a>
            アーカイブSEO編集: <?php echo esc_html($page_info['name']); ?>
        </h1>
        
        <div style="display: flex; gap: 20px; margin: 15px 0;">
            <div style="padding: 15px; background: #f0f6fc; border-left: 4px solid #2271b1; border-radius: 4px; flex: 1;">
                <strong>対象URL:</strong> 
                <a href="<?php echo esc_url($page_info['url']); ?>" target="_blank">
                    <?php echo esc_url($page_info['url']); ?>
                    <span class="dashicons dashicons-external" style="font-size: 14px;"></span>
                </a>
            </div>
            <div style="padding: 15px; background: #f0f0f1; border-radius: 4px;">
                <strong>補助金数:</strong> <?php echo number_format($page_info['post_count'] ?? 0); ?>件
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
                            <label style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_noindex" value="1" <?php checked($content['is_noindex'] ?? 0, 1); ?>>
                                <span style="color: #d63638;">noindex（検索エンジンから除外）</span>
                            </label>
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
                            <input type="text" name="featured_posts" value="<?php echo esc_attr($content['featured_posts']); ?>" 
                                   class="large-text" placeholder="記事IDをカンマ区切りで入力">
                            <p class="description">例: 123, 456, 789</p>
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
                                <code>.gi-seo-content-box</code> - コンテンツボックス全体<br>
                                <code>.gi-seo-content-box h2</code> - 見出し2<br>
                                <code>.gi-seo-content-box h3</code> - 見出し3<br>
                                <code>.gi-seo-content-box p</code> - 段落<br>
                                <code>.gi-seo-content-box strong</code> - 強調
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // メタディスクリプション文字数カウント
        $('textarea[name="meta_description"]').on('input', function() {
            $('#meta-desc-count').text($(this).val().length);
        });
        
        // おすすめ記事プレビュー
        function updatePostsPreview() {
            var ids = $('input[name="featured_posts"]').val();
            if (!ids) {
                $('#selected-posts-preview').html('');
                return;
            }
            
            $.post(ajaxurl, {
                action: 'gi_get_posts_preview',
                ids: ids,
                _wpnonce: '<?php echo wp_create_nonce('gi_posts_preview'); ?>'
            }, function(response) {
                if (response.success) {
                    $('#selected-posts-preview').html(response.data.html);
                }
            });
        }
        
        $('input[name="featured_posts"]').on('change', updatePostsPreview);
        updatePostsPreview();
    });
    </script>
    <?php
}


/**
 * =============================================================================
 * 5. 管理画面 - アーカイブ統合
 * =============================================================================
 */

function gi_archive_seo_merge_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    $source_type = isset($_GET['source_type']) ? sanitize_text_field($_GET['source_type']) : '';
    $source_key = isset($_GET['source_key']) ? sanitize_text_field($_GET['source_key']) : '';
    $merge_mode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'redirect';
    
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
                // 完全統合（タームをマージし、補助金を移動）
                $result = gi_execute_full_term_merge($merge_source_type, $merge_source_key, $merge_target);
                if ($result['success']) {
                    $notice_message = $result['message'];
                    $notice_type = 'success';
                } else {
                    $notice_message = 'エラー: ' . $result['message'];
                    $notice_type = 'error';
                }
            } else {
                // リダイレクト統合（従来の方式）
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
                
                // キャッシュクリア
                delete_transient('gi_archive_seo_' . md5($merge_source_type . ':' . $merge_source_key));
                
                $notice_message = 'リダイレクト統合設定を保存しました。このページは noindex + 統合先へ301リダイレクトされます。';
                $notice_type = 'success';
            }
        }
    }
    
    // 統合候補一覧を取得
    $archive_pages = gi_get_available_archive_pages();
    $merge_candidates = array();
    $merge_targets = array();
    
    foreach ($archive_pages as $page) {
        if (isset($page['post_count']) && $page['post_count'] <= 100 && $page['post_count'] > 0) {
            $merge_candidates[] = $page;
        }
        if (isset($page['post_count']) && $page['post_count'] > 100) {
            $merge_targets[] = $page;
        }
    }
    
    // 同じタクソノミー内の統合先候補を別途取得（完全統合用）
    $same_taxonomy_targets = array();
    if ($source_type && $source_type !== 'post_type_archive') {
        foreach ($archive_pages as $page) {
            if ($page['type'] === $source_type && $page['key'] !== $source_key) {
                $same_taxonomy_targets[] = $page;
            }
        }
        // 補助金数で降順ソート
        usort($same_taxonomy_targets, function($a, $b) {
            return ($b['post_count'] ?? 0) - ($a['post_count'] ?? 0);
        });
    }
    
    // 既存の統合設定を取得
    $merged_pages = $wpdb->get_results(
        "SELECT * FROM {$table_name} WHERE merge_target IS NOT NULL AND merge_target != ''",
        ARRAY_A
    );
    
    ?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-migrate" style="font-size: 30px; margin-right: 10px; vertical-align: middle;"></span>
            アーカイブページ統合
        </h1>
        
        <?php if ($notice_message): ?>
        <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible">
            <p><?php echo esc_html($notice_message); ?></p>
        </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 20px; margin: 20px 0;">
            <div style="flex: 1; background: #e7f5ff; border: 1px solid #0d6efd; padding: 15px; border-radius: 8px;">
                <strong><span class="dashicons dashicons-randomize"></span> リダイレクト統合:</strong><br>
                統合元ページへのアクセスを統合先へ301リダイレクト。<br>
                <small style="color: #666;">・カテゴリ自体は残り、noindexが設定されます<br>・補助金は移動されません</small>
            </div>
            <div style="flex: 1; background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px;">
                <strong><span class="dashicons dashicons-database"></span> 完全統合（カテゴリマージ）:</strong><br>
                カテゴリを完全に統合し、紐づく全補助金を移動。<br>
                <small style="color: #666;">・統合元カテゴリに属する全補助金が統合先に追加されます<br>・<strong style="color: #d63638;">補助金の総数が増加します</strong></small>
            </div>
        </div>
        
        <?php if ($source_type && $source_key): 
            $source_info = gi_get_archive_page_info($source_type, $source_key);
            $is_taxonomy = ($source_type !== 'post_type_archive');
        ?>
        <!-- 個別統合設定 -->
        <div class="postbox" style="margin: 20px 0; border-left: 4px solid #2271b1;">
            <div class="postbox-header" style="background: #f0f6fc;">
                <h2 style="padding: 10px 15px; margin: 0;">
                    統合設定: <strong><?php echo esc_html($source_info['name']); ?></strong>
                    <span style="font-weight: normal; font-size: 14px; color: #666;">
                        （<?php echo number_format($source_info['post_count'] ?? 0); ?>件の補助金）
                    </span>
                </h2>
            </div>
            <div class="inside">
                <!-- 統合モード選択タブ -->
                <div style="margin-bottom: 20px; border-bottom: 1px solid #ccc;">
                    <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge&source_type=' . urlencode($source_type) . '&source_key=' . urlencode($source_key) . '&mode=redirect'); ?>" 
                       class="gi-merge-tab <?php echo $merge_mode === 'redirect' ? 'active' : ''; ?>" 
                       style="display: inline-block; padding: 10px 20px; text-decoration: none; <?php echo $merge_mode === 'redirect' ? 'border-bottom: 3px solid #2271b1; color: #2271b1; font-weight: bold;' : 'color: #666;'; ?>">
                        <span class="dashicons dashicons-randomize"></span> リダイレクト統合
                    </a>
                    <?php if ($is_taxonomy): ?>
                    <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge&source_type=' . urlencode($source_type) . '&source_key=' . urlencode($source_key) . '&mode=full'); ?>" 
                       class="gi-merge-tab <?php echo $merge_mode === 'full' ? 'active' : ''; ?>" 
                       style="display: inline-block; padding: 10px 20px; text-decoration: none; <?php echo $merge_mode === 'full' ? 'border-bottom: 3px solid #d4af37; color: #d4af37; font-weight: bold;' : 'color: #666;'; ?>">
                        <span class="dashicons dashicons-database"></span> 完全統合（カテゴリマージ）
                    </a>
                    <?php endif; ?>
                </div>
                
                <form method="post">
                    <?php wp_nonce_field('gi_archive_merge', 'gi_merge_nonce'); ?>
                    <input type="hidden" name="source_type" value="<?php echo esc_attr($source_type); ?>">
                    <input type="hidden" name="source_key" value="<?php echo esc_attr($source_key); ?>">
                    <input type="hidden" name="merge_type" value="<?php echo esc_attr($merge_mode); ?>">
                    
                    <?php if ($merge_mode === 'full' && $is_taxonomy): ?>
                    <!-- 完全統合モード -->
                    <div style="background: #fffdf0; border: 1px solid #d4af37; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin-top: 0; color: #b8860b;">
                            <span class="dashicons dashicons-warning"></span> 
                            完全統合について
                        </h3>
                        <p style="margin: 0;">
                            この操作を実行すると、<strong>「<?php echo esc_html($source_info['name']); ?>」に属する全ての補助金（<?php echo number_format($source_info['post_count'] ?? 0); ?>件）</strong>が
                            統合先カテゴリにも追加されます。
                        </p>
                        <ul style="margin: 10px 0 0 20px; color: #666;">
                            <li>統合先カテゴリの補助金数が増加します</li>
                            <li>統合元カテゴリは noindex + リダイレクト設定されます</li>
                            <li><span style="color: #d63638;">この操作は元に戻せません（手動でのみ復元可能）</span></li>
                        </ul>
                    </div>
                    
                    <table class="form-table">
                        <tr>
                            <th>統合元カテゴリ</th>
                            <td>
                                <div style="background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 6px;">
                                    <strong style="font-size: 16px;"><?php echo esc_html($source_info['name']); ?></strong><br>
                                    <span style="color: #d63638; font-size: 18px; font-weight: bold;"><?php echo number_format($source_info['post_count'] ?? 0); ?>件</span>
                                    <span style="color: #666;">の補助金が移動されます</span><br>
                                    <a href="<?php echo esc_url($source_info['url']); ?>" target="_blank" style="font-size: 12px;">
                                        <?php echo esc_url($source_info['url']); ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>統合先カテゴリ<br><small style="font-weight: normal; color: #666;">（同じタクソノミー内）</small></th>
                            <td>
                                <select name="merge_target" class="regular-text" required style="min-width: 400px;">
                                    <option value="">-- 統合先カテゴリを選択 --</option>
                                    <?php foreach ($same_taxonomy_targets as $target): 
                                        $new_total = ($target['post_count'] ?? 0) + ($source_info['post_count'] ?? 0);
                                    ?>
                                        <option value="<?php echo esc_attr($target['type'] . ':' . $target['key']); ?>">
                                            <?php echo esc_html($target['name']); ?> 
                                            (現在 <?php echo number_format($target['post_count'] ?? 0); ?>件 → 
                                            統合後 <?php echo number_format($new_total); ?>件)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description" style="color: #d63638; font-weight: bold;">
                                    <span class="dashicons dashicons-info"></span>
                                    統合後、統合先の補助金数は増加します
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th>確認</th>
                            <td>
                                <label style="display: flex; align-items: center; gap: 10px; color: #d63638;">
                                    <input type="checkbox" name="confirm_full_merge" value="1" required>
                                    <strong>この操作が元に戻せないことを理解しました</strong>
                                </label>
                            </td>
                        </tr>
                    </table>
                    
                    <p>
                        <button type="submit" class="button" style="background: #d4af37; border-color: #b8860b; color: #fff;">
                            <span class="dashicons dashicons-database" style="vertical-align: middle;"></span>
                            完全統合を実行
                        </button>
                        <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge'); ?>" class="button">キャンセル</a>
                    </p>
                    
                    <?php else: ?>
                    <!-- リダイレクト統合モード（従来） -->
                    <table class="form-table">
                        <tr>
                            <th>統合元</th>
                            <td>
                                <strong><?php echo esc_html($source_info['name']); ?></strong>
                                （<?php echo number_format($source_info['post_count'] ?? 0); ?>件）<br>
                                <a href="<?php echo esc_url($source_info['url']); ?>" target="_blank"><?php echo esc_url($source_info['url']); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <th>統合先</th>
                            <td>
                                <select name="merge_target" class="regular-text" required>
                                    <option value="">-- 統合先を選択 --</option>
                                    <?php foreach ($merge_targets as $target): ?>
                                        <option value="<?php echo esc_attr($target['type'] . ':' . $target['key']); ?>">
                                            <?php echo esc_html($target['name']); ?> (<?php echo number_format($target['post_count']); ?>件)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">統合元のページにアクセスしたユーザーは、統合先へ301リダイレクトされます。</p>
                            </td>
                        </tr>
                    </table>
                    
                    <p>
                        <button type="submit" class="button button-primary">リダイレクト統合を実行</button>
                        <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge'); ?>" class="button">キャンセル</a>
                    </p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 統合候補一覧 -->
        <h2>統合候補（100件以下）: <?php echo count($merge_candidates); ?>件</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 30%;">ページ名</th>
                    <th style="width: 15%;">タイプ</th>
                    <th style="width: 10%;">補助金数</th>
                    <th style="width: 25%;">URL</th>
                    <th style="width: 20%;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merge_candidates as $page): 
                    $is_taxonomy = ($page['type'] !== 'post_type_archive');
                ?>
                <tr>
                    <td><strong><?php echo esc_html($page['name']); ?></strong></td>
                    <td><?php echo esc_html($page['type_label']); ?></td>
                    <td style="text-align: center; color: #d63638; font-weight: bold;">
                        <?php echo number_format($page['post_count']); ?>
                    </td>
                    <td style="font-size: 12px;">
                        <a href="<?php echo esc_url($page['url']); ?>" target="_blank">
                            <?php echo esc_html(str_replace(home_url(), '', $page['url'])); ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge&source_type=' . urlencode($page['type']) . '&source_key=' . urlencode($page['key']) . '&mode=redirect'); ?>" 
                           class="button button-small">リダイレクト</a>
                        <?php if ($is_taxonomy): ?>
                        <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-merge&source_type=' . urlencode($page['type']) . '&source_key=' . urlencode($page['key']) . '&mode=full'); ?>" 
                           class="button button-small" style="color: #d4af37; border-color: #d4af37;">完全統合</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- 統合済み一覧 -->
        <?php if (!empty($merged_pages)): ?>
        <h2 style="margin-top: 30px;">統合済みページ: <?php echo count($merged_pages); ?>件</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 30%;">統合元</th>
                    <th style="width: 30%;">統合先</th>
                    <th style="width: 20%;">設定日</th>
                    <th style="width: 20%;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merged_pages as $merged): 
                    $source_info = gi_get_archive_page_info($merged['archive_type'], $merged['archive_key']);
                    list($target_type, $target_key) = explode(':', $merged['merge_target'] . ':');
                    $target_info = gi_get_archive_page_info($target_type, $target_key);
                ?>
                <tr>
                    <td><?php echo esc_html($source_info['name']); ?></td>
                    <td><?php echo esc_html($target_info['name']); ?></td>
                    <td><?php echo esc_html($merged['updated_at']); ?></td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=gi-archive-seo-edit&type=' . urlencode($merged['archive_type']) . '&key=' . urlencode($merged['archive_key'])); ?>" 
                           class="button button-small">編集</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php
}


/**
 * 完全統合（タームマージ）を実行
 * 統合元タームに属する全ての補助金を統合先タームにも追加する
 */
function gi_execute_full_term_merge($source_type, $source_key, $merge_target) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    
    // 統合先を解析
    $parts = explode(':', $merge_target);
    if (count($parts) < 2) {
        return array('success' => false, 'message' => '統合先の指定が不正です。');
    }
    $target_type = $parts[0];
    $target_key = $parts[1];
    
    // タクソノミーのみ対応
    if ($source_type === 'post_type_archive' || $target_type === 'post_type_archive') {
        return array('success' => false, 'message' => '完全統合はタクソノミー（カテゴリ等）のみ対応しています。');
    }
    
    // 同じタクソノミー内でのみ統合可能
    if ($source_type !== $target_type) {
        return array('success' => false, 'message' => '異なるタクソノミー間での完全統合はできません。');
    }
    
    // 統合元タームを取得
    $source_term = get_term_by('slug', $source_key, $source_type);
    if (!$source_term || is_wp_error($source_term)) {
        return array('success' => false, 'message' => '統合元カテゴリが見つかりません。');
    }
    
    // 統合先タームを取得
    $target_term = get_term_by('slug', $target_key, $target_type);
    if (!$target_term || is_wp_error($target_term)) {
        return array('success' => false, 'message' => '統合先カテゴリが見つかりません。');
    }
    
    // 統合元タームに属する全ての補助金を取得
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
    
    if (empty($source_posts)) {
        return array('success' => false, 'message' => '統合元カテゴリに補助金がありません。');
    }
    
    $moved_count = 0;
    $already_assigned_count = 0;
    
    // 各補助金に統合先タームを追加
    foreach ($source_posts as $post_id) {
        // 既に統合先タームが付いているかチェック
        $existing_terms = wp_get_object_terms($post_id, $target_type, array('fields' => 'ids'));
        
        if (!in_array($target_term->term_id, $existing_terms)) {
            // 統合先タームを追加（既存のタームは維持）
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
    
    // タームの投稿数を再計算
    wp_update_term_count_now(array($source_term->term_id), $source_type);
    wp_update_term_count_now(array($target_term->term_id), $target_type);
    
    // 更新後のカウントを取得
    $updated_source_term = get_term($source_term->term_id, $source_type);
    $updated_target_term = get_term($target_term->term_id, $target_type);
    
    // キャッシュクリア
    delete_transient('gi_archive_seo_' . md5($source_type . ':' . $source_key));
    delete_transient('gi_archive_seo_' . md5($target_type . ':' . $target_key));
    
    $message = sprintf(
        '完全統合が完了しました。「%s」から「%s」へ %d件の補助金を追加しました。' .
        '（既に割り当て済み: %d件）' .
        '統合先の補助金数: %d件',
        $source_term->name,
        $target_term->name,
        $moved_count,
        $already_assigned_count,
        $updated_target_term->count
    );
    
    return array(
        'success' => true,
        'message' => $message,
        'moved_count' => $moved_count,
        'already_assigned' => $already_assigned_count,
        'new_target_count' => $updated_target_term->count,
    );
}


/**
 * =============================================================================
 * 6. ヘルパー関数
 * =============================================================================
 */

/**
 * 利用可能なアーカイブページ一覧を取得（PV数・補助金数付き）
 */
function gi_get_available_archive_pages() {
    $pages = array();
    
    // 投稿タイプアーカイブ
    $total_grants = wp_count_posts('grant')->publish;
    $pages[] = array(
        'type' => 'post_type_archive',
        'key' => 'grant',
        'name' => '助成金・補助金一覧（メイン）',
        'type_label' => '投稿タイプ',
        'url' => get_post_type_archive_link('grant'),
        'post_count' => $total_grants,
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
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $pages[] = array(
                    'type' => $taxonomy,
                    'key' => $term->slug,
                    'name' => $term->name . 'の助成金・補助金',
                    'type_label' => $label,
                    'url' => get_term_link($term),
                    'post_count' => $term->count,
                    'pv_count' => gi_get_archive_pv_count($taxonomy, $term->slug),
                );
            }
        }
    }
    
    return $pages;
}

/**
 * アーカイブページのPV数を取得
 */
function gi_get_archive_pv_count($type, $key) {
    // アクセストラッキングテーブルがあれば使用
    global $wpdb;
    $table_name = $wpdb->prefix . 'gi_access_log';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
        $url_pattern = '';
        if ($type === 'post_type_archive') {
            $url_pattern = '%/grant/%';
        } else {
            $url_pattern = '%/' . $key . '/%';
        }
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE url LIKE %s",
            $url_pattern
        ));
        
        return intval($count);
    }
    
    return 0;
}

/**
 * アーカイブページ情報を取得
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
        $info['post_count'] = wp_count_posts('grant')->publish;
        $info['pv_count'] = gi_get_archive_pv_count($type, $key);
    } else {
        // タクソノミー
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
 * 現在のアーカイブページのSEOコンテンツを取得
 */
function gi_get_current_archive_seo_content() {
    global $wpdb;
    
    $type = '';
    $key = '';
    
    // 現在のページタイプを判定
    if (is_post_type_archive('grant')) {
        $type = 'post_type_archive';
        $key = 'grant';
    } elseif (is_tax('grant_prefecture')) {
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
    }
    
    if (empty($type) || empty($key)) {
        return null;
    }
    
    // キャッシュチェック
    $cache_key = 'gi_archive_seo_' . md5($type . ':' . $key);
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }
    
    // DBから取得
    $table_name = $wpdb->prefix . 'gi_archive_seo_content';
    $content = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE archive_type = %s AND archive_key = %s AND is_active = 1",
        $type, $key
    ), ARRAY_A);
    
    // 24時間キャッシュ
    set_transient($cache_key, $content, DAY_IN_SECONDS);
    
    return $content;
}

/**
 * おすすめ記事を取得
 */
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


/**
 * =============================================================================
 * 7. AJAX ハンドラー
 * =============================================================================
 */

function gi_ajax_get_posts_preview() {
    check_ajax_referer('gi_posts_preview', '_wpnonce');
    
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
        $html .= '<li style="padding: 5px 0; border-bottom: 1px solid #eee;">';
        $html .= '<small style="color: #666;">[ID:' . $post->ID . ']</small> ';
        $html .= esc_html($post->post_title);
        $html .= '</li>';
    }
    $html .= '</ul>';
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_gi_get_posts_preview', 'gi_ajax_get_posts_preview');


/**
 * =============================================================================
 * 8. フロントエンド出力関数
 * =============================================================================
 */

// イントロコンテンツ出力
function gi_output_archive_intro_content() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['intro_content'])) {
        echo '<div class="gi-archive-seo-intro">';
        echo '<div class="gi-seo-content-box">';
        echo wp_kses_post($content['intro_content']);
        echo '</div>';
        echo '</div>';
    }
}

// アウトロコンテンツ出力
function gi_output_archive_outro_content() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['outro_content'])) {
        echo '<div class="gi-archive-seo-outro">';
        echo '<div class="gi-seo-content-box">';
        echo wp_kses_post($content['outro_content']);
        echo '</div>';
        echo '</div>';
    }
}

// おすすめ記事出力
function gi_output_archive_featured_posts() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['featured_posts'])) {
        $posts = gi_get_featured_posts_for_archive($content['featured_posts']);
        
        if (!empty($posts)) {
            echo '<div class="gi-archive-featured-posts">';
            echo '<h2>おすすめの助成金・補助金</h2>';
            echo '<div class="gi-featured-grid">';
            
            foreach ($posts as $post) {
                $max_amount = get_post_meta($post->ID, 'grant_amount_max', true);
                $max_amount_display = $max_amount ? number_format($max_amount) . '万円' : '要確認';
                $deadline = get_post_meta($post->ID, 'deadline_date', true);
                $deadline_display = $deadline ? date('Y/m/d', strtotime($deadline)) : '';
                $prefecture_terms = get_the_terms($post->ID, 'grant_prefecture');
                $prefecture_name = ($prefecture_terms && !is_wp_error($prefecture_terms)) ? $prefecture_terms[0]->name : '全国';
                
                echo '<a href="' . get_permalink($post->ID) . '" class="gi-featured-card">';
                echo '<div class="gi-featured-card-header">';
                echo '<span class="gi-featured-prefecture">' . esc_html($prefecture_name) . '</span>';
                if ($deadline_display) {
                    echo '<span class="gi-featured-deadline">締切: ' . esc_html($deadline_display) . '</span>';
                }
                echo '</div>';
                echo '<h3>' . esc_html($post->post_title) . '</h3>';
                echo '<div class="gi-featured-amount">上限 ' . esc_html($max_amount_display) . '</div>';
                echo '</a>';
            }
            
            echo '</div>';
            echo '</div>';
        }
    }
}

// サイドバーコンテンツ出力
function gi_output_archive_sidebar_content() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['sidebar_content'])) {
        echo '<div class="gi-archive-seo-sidebar" style="margin-bottom: 20px;">';
        echo wp_kses_post($content['sidebar_content']);
        echo '</div>';
    }
}

// カスタムCSS出力（修正版：クラス名を正しく認識）
function gi_output_archive_custom_css() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['custom_css'])) {
        // CSSをそのまま出力（サニタイズ済み）
        echo '<style id="gi-archive-custom-css">' . "\n";
        echo $content['custom_css'];
        echo "\n</style>";
    }
}
add_action('wp_head', 'gi_output_archive_custom_css', 100);


/**
 * =============================================================================
 * 9. noindex & リダイレクト処理
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
 * 10. カスタムタイトル・メタディスクリプション
 * =============================================================================
 */

function gi_get_archive_custom_title() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['page_title'])) {
        return $content['page_title'];
    }
    
    return null;
}

function gi_get_archive_custom_meta_description() {
    $content = gi_get_current_archive_seo_content();
    
    if ($content && !empty($content['meta_description'])) {
        return $content['meta_description'];
    }
    
    return null;
}
