<?php
/**
 * Template Part: Grant Search Section
 * 補助金・助成金検索セクション v40.1
 * 
 * @package Grant_Insight_Perfect
 * @version 40.1.0 - Taxonomy Archive URL Structure
 * 
 * Design Concept:
 * - 官公庁風のシンプルで信頼感あるデザイン
 * - 白背景ベース + 濃紺×金のアクセント
 * - 折りたたみ式のフィルターグループ
 * 
 * v40.1 Changes:
 * - 都道府県: /prefecture/{slug}/ 形式に修正
 * - 市町村: /grant_municipality/{slug}/ 形式に修正
 * - タグ: /grant-tag/{slug}/ 形式に修正
 * - タクソノミーアーカイブへの正しいURL構造
 */

if (!defined('ABSPATH')) exit;

// ==========================================================================
// データ取得
// ==========================================================================

$cache_key = 'search_section_data_v40';
$cached_data = get_transient($cache_key);

if ($cached_data === false) {
    
    // カテゴリー取得
    $all_categories = get_terms([
        'taxonomy'   => 'grant_category',
        'hide_empty' => false,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => 100,
    ]);
    if (is_wp_error($all_categories)) {
        $all_categories = [];
    }
    
    // 都道府県取得
    $prefectures = function_exists('gi_get_all_prefectures') 
        ? gi_get_all_prefectures() 
        : [];
    
    // タグ取得（人気キーワード）
    $all_tags = get_terms([
        'taxonomy'   => 'grant_tag',
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => 100,
    ]);
    if (is_wp_error($all_tags)) {
        $all_tags = [];
    }
    
    // 統計データ
    $total_grants = wp_count_posts('grant')->publish ?? 0;
    
    $cached_data = [
        'categories'   => $all_categories,
        'prefectures'  => $prefectures,
        'tags'         => $all_tags,
        'total_grants' => $total_grants,
    ];
    
    set_transient($cache_key, $cached_data, 30 * MINUTE_IN_SECONDS);
}

// データ展開
$all_categories = $cached_data['categories'];
$prefectures    = $cached_data['prefectures'];
$all_tags       = $cached_data['tags'];
$total_grants   = $cached_data['total_grants'];

// 人気タグ（上位10件）
$popular_tags = is_array($all_tags) ? array_slice($all_tags, 0, 10) : [];

// 都道府県リスト（gi_get_all_prefecturesから取得）
// スラッグと名前のマッピングを使用してアーカイブページと連携
$pref_list = $prefectures; // gi_get_all_prefectures() の結果をそのまま使用
?>

<!-- ==========================================================================
     補助金・助成金検索セクション v39.0
     ========================================================================== -->
<section class="gs-search" id="grant-search-section">
    <div class="gs-search__container">

        <!-- Section Header -->
        <div class="gs-search__header">
            <span class="gs-search__badge">SEARCH GRANTS</span>
            <h2 class="gs-search__title">
                <span class="gs-search__title-line"></span>
                補助金・助成金を検索
                <span class="gs-search__title-line"></span>
            </h2>
        </div>

        <!-- Main Search Panel -->
        <div class="gs-search__panel">
            <!-- Decorative Corners -->
            <div class="gs-search__corner gs-search__corner--tl"></div>
            <div class="gs-search__corner gs-search__corner--br"></div>

            <form action="<?php echo esc_url(home_url('/grants/')); ?>" method="get" class="gs-search__form">
                
                <!-- Search Header Controls -->
                <div class="gs-search__controls">
                    <div class="gs-search__toggle-group">
                        <label class="gs-search__toggle">
                            <input type="checkbox" name="application_status" value="open" checked class="gs-search__toggle-input">
                            <span class="gs-search__toggle-switch"></span>
                            <span class="gs-search__toggle-label">受付状況募集中のみ</span>
                        </label>
                        <span class="gs-search__divider">|</span>
                        <button type="button" class="gs-search__link-btn" id="show-all-btn">すべて表示</button>
                    </div>
                    <div class="gs-search__quick-links">
                        <button type="button" class="gs-search__quick-link" id="clear-conditions">条件クリア</button>
                        <a href="<?php echo esc_url(home_url('/saved-searches/')); ?>" class="gs-search__quick-link">保存条件</a>
                        <a href="<?php echo esc_url(home_url('/history/')); ?>" class="gs-search__quick-link">閲覧履歴</a>
                    </div>
                </div>

                <!-- Filter Groups -->
                <div class="gs-search__filters">
                    
                    <!-- 1. Category Filter -->
                    <div class="gs-search__group">
                        <button type="button" class="gs-search__group-header" onclick="toggleGroup('category-content')" aria-expanded="true">
                            <h3 class="gs-search__group-title">
                                <span class="gs-search__group-bar"></span>
                                用途・カテゴリー
                            </h3>
                            <div class="gs-search__group-action">
                                <span class="gs-search__group-hint">選択してください</span>
                                <span class="gs-search__group-arrow">▼</span>
                            </div>
                        </button>

                        <div id="category-content" class="gs-search__group-content gs-search__group-content--open">
                            <div class="gs-search__index-grid gs-search__scrollable">
                                <?php foreach ($all_categories as $cat): ?>
                                <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="gs-search__category-link" data-slug="<?php echo esc_attr($cat->slug); ?>">
                                    <?php echo esc_html($cat->name); ?> <span class="gs-search__count">(<?php echo esc_html($cat->count); ?>)</span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Prefecture Filter -->
                    <div class="gs-search__group">
                        <button type="button" class="gs-search__group-header" onclick="toggleGroup('pref-content')" aria-expanded="false">
                            <h3 class="gs-search__group-title">
                                <span class="gs-search__group-bar"></span>
                                都道府県
                            </h3>
                            <div class="gs-search__group-action">
                                <span class="gs-search__group-hint">選択してください</span>
                                <span class="gs-search__group-arrow">▼</span>
                            </div>
                        </button>

                        <div id="pref-content" class="gs-search__group-content">
                            <div class="gs-search__pref-grid">
                                <?php foreach ($pref_list as $pref): ?>
                                <a href="<?php echo esc_url(home_url('/prefecture/' . $pref['slug'] . '/')); ?>" class="gs-search__pref-link" data-slug="<?php echo esc_attr($pref['slug']); ?>">
                                    <?php echo esc_html($pref['name']); ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Municipality Search -->
                    <div class="gs-search__group">
                        <button type="button" class="gs-search__group-header" onclick="toggleGroup('city-content')" aria-expanded="false">
                            <h3 class="gs-search__group-title">
                                <span class="gs-search__group-bar"></span>
                                市町村から探す
                            </h3>
                            <div class="gs-search__group-action">
                                <span class="gs-search__group-selected" id="selected-pref">宮城県</span>
                                <span class="gs-search__group-arrow">▼</span>
                            </div>
                        </button>

                        <div id="city-content" class="gs-search__group-content">
                            <div class="gs-search__municipality">
                                <div class="gs-search__municipality-select">
                                    <select id="municipality-pref-select" class="gs-search__select" data-current-slug="miyagi">
                                        <option value="">都道府県を選択...</option>
                                        <?php foreach ($pref_list as $pref): ?>
                                        <option value="<?php echo esc_attr($pref['slug']); ?>" data-name="<?php echo esc_attr($pref['name']); ?>" <?php echo $pref['slug'] === 'miyagi' ? 'selected' : ''; ?>>
                                            <?php echo esc_html($pref['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="gs-search__municipality-list gs-search__scrollable" id="municipality-list">
                                    <!-- 市町村リストはJavaScriptで動的に生成 -->
                                    <div class="gs-search__municipality-loading">読み込み中...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Free Word -->
                    <div class="gs-search__group gs-search__group--freeword">
                        <h3 class="gs-search__group-title gs-search__group-title--inline">
                            <span class="gs-search__group-bar"></span>
                            フリーワード
                        </h3>
                        <div class="gs-search__freeword">
                            <svg class="gs-search__freeword-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M21 21l-6-6"/>
                            </svg>
                            <input type="text" name="search" placeholder="キーワードを入力..." class="gs-search__freeword-input" autocomplete="off">
                        </div>
                    </div>

                </div>

                <!-- Search Button -->
                <div class="gs-search__submit">
                    <button type="submit" class="gs-search__submit-btn">
                        <span>この条件で検索</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                    <a href="<?php echo esc_url(home_url('/grants/advanced-search/')); ?>" class="gs-search__advanced-link">詳細検索はこちら</a>
                </div>

            </form>
        </div>

        <!-- Popular Keywords Section -->
        <div class="gs-search__keywords">
            <div class="gs-search__keywords-header">
                <h3 class="gs-search__keywords-title">
                    <span class="gs-search__keywords-badge">INDEX</span>
                    人気キーワードから探す
                </h3>
                <button type="button" id="keywords-toggle" class="gs-search__keywords-toggle">
                    <span id="keywords-toggle-text">すべて表示</span>
                    <svg id="keywords-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            <div id="keywords-container" class="gs-search__keywords-container">
                <div class="gs-search__keywords-list">
                    <!-- Top Keywords (Bold) -->
                    <?php foreach ($popular_tags as $tag): ?>
                    <a href="<?php echo esc_url(home_url('/grant-tag/' . $tag->slug . '/')); ?>" class="gs-search__keyword gs-search__keyword--popular">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                    <?php endforeach; ?>
                    
                    <span class="gs-search__keyword-divider"></span>
                    
                    <!-- All Keywords -->
                    <?php $remaining_tags = array_slice($all_tags, 10); ?>
                    <?php foreach ($remaining_tags as $tag): ?>
                    <a href="<?php echo esc_url(home_url('/grant-tag/' . $tag->slug . '/')); ?>" class="gs-search__keyword">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Gradient Overlay -->
                <div id="keywords-gradient" class="gs-search__keywords-gradient"></div>
            </div>
            
            <p class="gs-search__keywords-note">
                当サイトの情報は各省庁・自治体の公表データに基づき、専門家監修のもと更新されています。
            </p>
        </div>

    </div>
</section>

<!-- JavaScript -->
<script>
(function() {
    'use strict';

    // Base URL for municipality archives
    const municipalityBaseUrl = '<?php echo esc_url(home_url('/grant_municipality/')); ?>';

    // Group Toggle
    window.toggleGroup = function(contentId) {
        const content = document.getElementById(contentId);
        const header = content.previousElementSibling;
        const arrow = header.querySelector('.gs-search__group-arrow');
        const isOpen = content.classList.contains('gs-search__group-content--open');
        
        if (isOpen) {
            content.classList.remove('gs-search__group-content--open');
            header.setAttribute('aria-expanded', 'false');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        } else {
            content.classList.add('gs-search__group-content--open');
            header.setAttribute('aria-expanded', 'true');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        }
    };

    // Keywords Toggle
    const keywordsToggle = document.getElementById('keywords-toggle');
    const keywordsContainer = document.getElementById('keywords-container');
    const keywordsToggleText = document.getElementById('keywords-toggle-text');
    const keywordsToggleIcon = document.getElementById('keywords-toggle-icon');
    const keywordsGradient = document.getElementById('keywords-gradient');
    
    let keywordsExpanded = false;

    if (keywordsToggle) {
        keywordsToggle.addEventListener('click', function() {
            keywordsExpanded = !keywordsExpanded;
            
            if (keywordsExpanded) {
                keywordsContainer.classList.add('gs-search__keywords-container--expanded');
                keywordsToggleText.textContent = '閉じる';
                keywordsToggleIcon.style.transform = 'rotate(180deg)';
                if (keywordsGradient) keywordsGradient.style.opacity = '0';
            } else {
                keywordsContainer.classList.remove('gs-search__keywords-container--expanded');
                keywordsToggleText.textContent = 'すべて表示';
                keywordsToggleIcon.style.transform = 'rotate(0deg)';
                if (keywordsGradient) keywordsGradient.style.opacity = '1';
            }
        });
    }

    // Clear Conditions
    const clearBtn = document.getElementById('clear-conditions');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            const form = document.querySelector('.gs-search__form');
            if (form) {
                form.reset();
                // Re-check the status toggle
                const statusToggle = form.querySelector('input[name="application_status"]');
                if (statusToggle) statusToggle.checked = true;
                // Clear free word input
                const freewordInput = form.querySelector('input[name="search"]');
                if (freewordInput) freewordInput.value = '';
            }
        });
    }

    // Show All Button
    const showAllBtn = document.getElementById('show-all-btn');
    if (showAllBtn) {
        showAllBtn.addEventListener('click', function() {
            const statusToggle = document.querySelector('input[name="application_status"]');
            if (statusToggle) statusToggle.checked = false;
        });
    }

    // Municipality Prefecture Select - Dynamic Loading
    const prefSelect = document.getElementById('municipality-pref-select');
    const selectedPref = document.getElementById('selected-pref');
    const municipalityList = document.getElementById('municipality-list');

    // Function to load municipalities for a prefecture
    function loadMunicipalities(prefSlug) {
        if (!municipalityList) return;
        
        // AJAX request to get municipalities
        municipalityList.innerHTML = '<div class="gs-search__municipality-loading">読み込み中...</div>';
        
        // POSTリクエストでAJAX送信
        const formData = new FormData();
        formData.append('action', 'gi_get_municipalities_for_prefecture');
        formData.append('prefecture_slug', prefSlug);
        formData.append('nonce', '<?php echo wp_create_nonce('gi_ajax_nonce'); ?>');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log('Municipality AJAX response:', data); // デバッグ用
                
                // wp_send_json_success は { success: true, data: { municipalities: [...] } } を返す
                const municipalities = data.data?.municipalities || [];
                
                if (data.success && municipalities.length > 0) {
                    let html = '';
                    municipalities.forEach(function(city) {
                        const citySlug = city.slug || city.name.toLowerCase().replace(/\s+/g, '-');
                        html += '<a href="' + municipalityBaseUrl + citySlug + '/" class="gs-search__city-link">' + city.name + '</a>';
                    });
                    municipalityList.innerHTML = html;
                } else if (data.success && municipalities.length === 0) {
                    municipalityList.innerHTML = '<div class="gs-search__municipality-empty">この都道府県の市町村データはまだ登録されていません</div>';
                } else {
                    console.error('Municipality AJAX error:', data);
                    municipalityList.innerHTML = '<div class="gs-search__municipality-empty">市町村データがありません</div>';
                }
            })
            .catch(function(error) {
                console.error('Municipality load error:', error);
                municipalityList.innerHTML = '<div class="gs-search__municipality-error">読み込みエラー</div>';
            });
    }

    if (prefSelect) {
        // Load initial municipalities (宮城県)
        const initialSlug = prefSelect.getAttribute('data-current-slug') || 'miyagi';
        loadMunicipalities(initialSlug);

        prefSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const prefName = selectedOption.getAttribute('data-name') || this.value;
            const prefSlug = this.value;
            
            if (selectedPref) {
                selectedPref.textContent = prefName || '選択してください';
            }
            
            if (prefSlug) {
                loadMunicipalities(prefSlug);
            }
        });
    }

    console.log('✅ Grant Search Section v40.0 Initialized (Archive Integration Complete)');
})();
</script>
