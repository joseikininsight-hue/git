<?php
/**
 * Taxonomy Template for Grant Category - Yahoo! JAPAN Inspired SEO Perfect Edition
 * 助成金カテゴリ（grant_category）アーカイブページ - Yahoo!風デザイン・SEO完全最適化版
 *
 * @package Grant_Insight_Perfect
 * @version 19.0.0 - Category Specialized with Yahoo! JAPAN Style
 *
 * === Features ===
 * - Based on archive-grant.php structure
 * - Category-fixed filter (category selector hidden / fixed)
 * - Yahoo! JAPAN inspired design
 * - Sidebar layout (PC only) with rankings & topics
 * - Ad spaces reserved in sidebar
 * - Mobile: No sidebar, optimized single column + filter panel
 * - SEO Perfect (Schema.org: CollectionPage + BreadcrumbList)
 * - All archive functions preserved
 */

get_header();

// CSS/JS を直接出力（テンプレート読み込み時点では wp_enqueue_scripts は実行済みのため）
$template_dir = get_template_directory();
$template_uri = get_template_directory_uri();
$css_file = $template_dir . '/assets/css/archive-common.css';
$js_file = $template_dir . '/assets/js/archive-common.js';
?>
<?php if (file_exists($css_file) && !wp_style_is('gi-archive-common', 'done')): ?>
<link rel="stylesheet" href="<?php echo esc_url($template_uri . '/assets/css/archive-common.css?ver=' . filemtime($css_file)); ?>" media="all">
<?php endif; ?>
<?php

/* =========================================================
 * Current Category Info
 * ========================================================= */
$current_category = get_queried_object();

$category_name        = isset($current_category->name) ? $current_category->name : '';
$category_slug        = isset($current_category->slug) ? $current_category->slug : '';
$category_description = isset($current_category->description) ? $current_category->description : '';
$category_count       = isset($current_category->count) ? (int) $current_category->count : 0;
$category_id          = isset($current_category->term_id) ? (int) $current_category->term_id : 0;

// Term meta (optional)
$category_meta  = $category_id ? get_term_meta($category_id) : array();
$category_icon  = isset($category_meta['category_icon'][0]) ? $category_meta['category_icon'][0] : '';
$category_color = isset($category_meta['category_color'][0]) ? $category_meta['category_color'][0] : '#000000';

/* =========================================================
 * Prefecture + region data
 * ========================================================= */
$prefectures = function_exists('gi_get_all_prefectures') ? gi_get_all_prefectures() : array();

$region_groups = array(
    'hokkaido' => '北海道',
    'tohoku'   => '東北',
    'kanto'    => '関東',
    'chubu'    => '中部',
    'kinki'    => '近畿',
    'chugoku'  => '中国',
    'shikoku'  => '四国',
    'kyushu'   => '九州・沖縄',
);

/* =========================================================
 * SEO data
 * ========================================================= */
$current_year  = date('Y');
$current_month = (int) date('n');

$season = ($current_month >= 3 && $current_month <= 5) ? '春'
    : (($current_month >= 6 && $current_month <= 8) ? '夏'
    : (($current_month >= 9 && $current_month <= 11) ? '秋' : '冬'));

$page_title = $category_name . 'の助成金・補助金一覧【' . $current_year . '年度最新版】';

$page_description = $category_description ?: (
    $category_name . 'に関する助成金・補助金を' . number_format($category_count) . '件掲載。' .
    $current_year . '年度の最新募集情報、申請要件、対象事業、助成金額、締切日を詳しく解説。都道府県・市町村別の検索も可能。専門家による申請サポート完備。'
);

$canonical_url = ($category_id ? get_term_link($current_category) : home_url('/'));
if (is_wp_error($canonical_url)) {
    $canonical_url = home_url('/');
}

// Total grants (site-wide)
$total_grants           = (int) wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// OGP image fallback
$og_image = get_site_icon_url(1200) ?: home_url('/wp-content/uploads/2025/10/1.png');

// Keywords
$keywords = array('助成金', '補助金', $category_name, '検索', '申請', '支援制度', $current_year . '年度');
$keywords_string = implode(',', array_filter($keywords));

/* =========================================================
 * Sidebar: recent topics (in category)
 * ========================================================= */
$recent_grants = new WP_Query(array(
    'post_type'      => 'grant',
    'posts_per_page' => 5,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
    'tax_query'      => array(
        array(
            'taxonomy' => 'grant_category',
            'field'    => 'term_id',
            'terms'    => $category_id,
        ),
    ),
));

/* =========================================================
 * Related categories
 * - If child: siblings in same parent
 * - If parent: children
 * ========================================================= */
$related_categories = array();

if (!empty($current_category->parent) && (int) $current_category->parent > 0) {
    $related_categories = get_terms(array(
        'taxonomy'   => 'grant_category',
        'parent'     => (int) $current_category->parent,
        'exclude'    => array($category_id),
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => 6,
    ));
} else {
    $related_categories = get_terms(array(
        'taxonomy'   => 'grant_category',
        'parent'     => $category_id,
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => 6,
    ));
}

if (is_wp_error($related_categories)) {
    $related_categories = array();
}

/* =========================================================
 * Breadcrumbs
 * ========================================================= */
$breadcrumbs = array(
    array('name' => 'ホーム', 'url' => home_url('/')),
    array('name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')),
);

if (!empty($current_category->parent) && (int) $current_category->parent > 0) {
    $parent_category = get_term((int) $current_category->parent, 'grant_category');
    if ($parent_category && !is_wp_error($parent_category)) {
        $parent_link = get_term_link($parent_category);
        if (!is_wp_error($parent_link)) {
            $breadcrumbs[] = array('name' => $parent_category->name, 'url' => $parent_link);
        }
    }
}
$breadcrumbs[] = array('name' => $category_name, 'url' => '');

/* =========================================================
 * Initial WP_Query (server render) - category fixed
 * ========================================================= */
$initial_query = new WP_Query(array(
    'post_type'      => 'grant',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
    'paged'          => get_query_var('paged') ? (int) get_query_var('paged') : 1,
    'tax_query'      => array(
        array(
            'taxonomy' => 'grant_category',
            'field'    => 'term_id',
            'terms'    => $category_id,
        ),
    ),
    'orderby'        => 'date',
    'order'          => 'DESC',
));

/* =========================================================
 * Schema.org: CollectionPage + BreadcrumbList
 * ========================================================= */
$schema_collection = array(
    '@context'     => 'https://schema.org',
    '@type'        => 'CollectionPage',
    'name'         => $page_title,
    'description'  => $page_description,
    'url'          => $canonical_url,
    'inLanguage'   => 'ja-JP',
    'dateModified' => current_time('c'),
    'provider'     => array(
        '@type' => 'Organization',
        'name'  => get_bloginfo('name'),
        'url'   => home_url('/'),
        'logo'  => array(
            '@type' => 'ImageObject',
            'url'   => get_site_icon_url(512) ?: home_url('/wp-content/uploads/2025/10/1.png'),
        ),
    ),
    'mainEntity'   => array(
        '@type'           => 'ItemList',
        'name'            => $page_title,
        'description'     => $page_description,
        'numberOfItems'   => $category_count,
        'itemListElement' => array(),
    ),
);

// Add current page items to ItemList (optional but helpful)
if ($initial_query->have_posts()) {
    $pos = 1 + (((int) $initial_query->get('paged') - 1) * (int) $initial_query->get('posts_per_page'));
    foreach ($initial_query->posts as $p) {
        $schema_collection['mainEntity']['itemListElement'][] = array(
            '@type'    => 'ListItem',
            'position' => $pos,
            'url'      => get_permalink($p->ID),
            'name'     => get_the_title($p->ID),
        );
        $pos++;
    }
}

$breadcrumb_schema = array(
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => array(),
);

foreach ($breadcrumbs as $index => $breadcrumb) {
    $breadcrumb_schema['itemListElement'][] = array(
        '@type'    => 'ListItem',
        'position' => $index + 1,
        'name'     => $breadcrumb['name'],
        'item'     => !empty($breadcrumb['url']) ? $breadcrumb['url'] : $canonical_url,
    );
}
?>

<!-- Structured Data: CollectionPage -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- Structured Data: BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main class="grant-archive-yahoo-style grant-category-archive"
      id="category-<?php echo esc_attr($category_slug); ?>"
      role="main"
      itemscope
      itemtype="https://schema.org/CollectionPage">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav"
         aria-label="パンくずリスト"
         itemscope
         itemtype="https://schema.org/BreadcrumbList">
        <div class="yahoo-container">
            <ol class="breadcrumb-list">
                <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                    <li class="breadcrumb-item"
                        itemprop="itemListElement"
                        itemscope
                        itemtype="https://schema.org/ListItem">
                        <?php if (!empty($breadcrumb['url'])): ?>
                            <a href="<?php echo esc_url($breadcrumb['url']); ?>"
                               itemprop="item"
                               title="<?php echo esc_attr($breadcrumb['name']); ?>へ移動">
                                <span itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                            </a>
                        <?php else: ?>
                            <span itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                        <?php endif; ?>
                        <meta itemprop="position" content="<?php echo (int) ($index + 1); ?>">
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>

    <!-- Category Hero -->
    <header class="yahoo-hero-section"
            itemscope
            itemtype="https://schema.org/WPHeader">
        <div class="yahoo-container">
            <div class="hero-content-wrapper">

                <div class="category-badge"
                     role="status"
                     <?php if (!empty($category_color)): ?>
                        style="background: <?php echo esc_attr($category_color); ?>;"
                     <?php endif; ?>>
                    <?php if (!empty($category_icon)): ?>
                        <img src="<?php echo esc_url($category_icon); ?>"
                             alt="<?php echo esc_attr($category_name); ?>アイコン"
                             class="badge-icon-img"
                             width="20"
                             height="20">
                    <?php else: ?>
                        <svg class="badge-icon"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             aria-hidden="true">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        </svg>
                    <?php endif; ?>
                    <span><?php echo esc_html($category_name); ?>カテゴリー</span>
                </div>

                <h1 class="yahoo-main-title" itemprop="headline">
                    <span class="category-name-highlight"><?php echo esc_html($category_name); ?></span>
                    <span class="title-text">の助成金・補助金</span>
                    <span class="year-badge"><?php echo esc_html($current_year); ?>年度版</span>
                </h1>

                <div class="yahoo-lead-section" itemprop="description">
                    <?php if (!empty($category_description)): ?>
                        <div class="category-description-rich">
                            <?php echo wpautop(wp_kses_post($category_description)); ?>
                        </div>
                    <?php endif; ?>

                    <p class="yahoo-lead-text">
                        <?php echo esc_html($category_name); ?>に関する助成金・補助金を
                        <strong><?php echo number_format($category_count); ?>件</strong>掲載。
                        <?php echo esc_html($current_year); ?>年度の最新募集情報を毎日更新中。
                        都道府県・市町村別の検索にも対応し、あなたの地域で利用できる助成金を簡単に見つけられます。
                    </p>
                </div>

                <div class="yahoo-meta-info" role="group" aria-label="カテゴリー統計情報">
                    <div class="meta-item" itemscope itemtype="https://schema.org/QuantitativeValue">
                        <svg class="meta-icon"
                             width="18" height="18"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             aria-hidden="true">
                            <path d="M9 11H7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V11h-2v8H9v-8z"/>
                            <path d="M13 7h2l-5-5-5 5h2v4h6V7z"/>
                        </svg>
                        <strong itemprop="value"><?php echo number_format($category_count); ?></strong>
                        <span itemprop="unitText">件の助成金</span>
                    </div>

                    <div class="meta-item">
                        <svg class="meta-icon"
                             width="18" height="18"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <time datetime="<?php echo esc_attr($current_year); ?>" itemprop="dateModified">
                            <?php echo esc_html($current_year); ?>年度最新情報
                        </time>
                    </div>

                    <div class="meta-item">
                        <svg class="meta-icon"
                             width="18" height="18"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>毎日更新中</span>
                    </div>

                    <div class="meta-item">
                        <svg class="meta-icon"
                             width="18" height="18"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>地域別対応</span>
                    </div>
                </div>

                <div class="feature-cards-grid">
                    <article class="feature-card">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>リアルタイム更新</h3>
                            <p>最新の募集情報・締切情報を毎日チェック。見逃しを防ぎます。</p>
                        </div>
                    </article>

                    <article class="feature-card">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>地域別検索対応</h3>
                            <p>都道府県・市町村で絞り込み。地域密着型の助成金も見つかります。</p>
                        </div>
                    </article>

                    <article class="feature-card">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>詳細な申請ガイド</h3>
                            <p>申請方法から採択のコツまで、専門家監修の情報を提供。</p>
                        </div>
                    </article>
                </div>

                <?php if (!empty($related_categories)): ?>
                    <div class="related-categories-section">
                        <h2 class="related-categories-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                            <?php echo (!empty($current_category->parent) && (int) $current_category->parent > 0) ? '関連カテゴリー' : 'サブカテゴリー'; ?>
                        </h2>
                        <div class="related-categories-grid">
                            <?php foreach ($related_categories as $rel_cat): ?>
                                <?php
                                $rel_link = get_term_link($rel_cat);
                                if (is_wp_error($rel_link)) continue;
                                ?>
                                <a href="<?php echo esc_url($rel_link); ?>"
                                   class="related-category-card"
                                   title="<?php echo esc_attr($rel_cat->name); ?>の助成金を見る">
                                    <span class="related-category-name"><?php echo esc_html($rel_cat->name); ?></span>
                                    <span class="related-category-count"><?php echo number_format((int) $rel_cat->count); ?>件</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </header>

    <!-- Two column -->
    <div class="yahoo-container yahoo-two-column-layout">

        <!-- Main -->
        <div class="yahoo-main-content">

            <!-- Search bar -->
            <section class="yahoo-search-section">
                <div class="search-bar-wrapper">
                    <label for="keyword-search" class="visually-hidden">キーワード検索</label>
                    <div class="search-input-container">
                        <svg class="search-icon"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="search"
                               id="keyword-search"
                               class="search-input"
                               placeholder="助成金名、実施機関、対象事業で検索..."
                               data-category="<?php echo esc_attr($category_slug); ?>"
                               aria-label="助成金を検索"
                               autocomplete="off">
                        <button class="search-clear-btn"
                                id="search-clear-btn"
                                style="display: none;"
                                aria-label="検索をクリア"
                                type="button">×</button>
                        <button class="search-execute-btn"
                                id="search-btn"
                                aria-label="検索を実行"
                                type="button">検索</button>
                    </div>
                </div>
            </section>

            <!-- Mobile floating filter button -->
            <button class="mobile-filter-toggle"
                    id="mobile-filter-toggle"
                    aria-label="フィルターを開く"
                    aria-expanded="false"
                    type="button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span class="filter-count-badge" id="mobile-filter-count" style="display: none;">0</span>
            </button>

            <div class="filter-panel-overlay" id="filter-panel-overlay"></div>

            <!-- Filter panel (category fixed) -->
            <section class="yahoo-filter-section" id="filter-panel"
                     role="search"
                     aria-label="助成金検索フィルター">

                <div class="filter-header">
                    <h2 class="filter-title">
                        <svg class="title-icon"
                             width="18" height="18"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             aria-hidden="true">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        絞り込み
                    </h2>

                    <button class="mobile-filter-close"
                            id="mobile-filter-close"
                            aria-label="フィルターを閉じる"
                            type="button">×</button>

                    <button class="filter-reset-all"
                            id="reset-all-filters-btn"
                            style="display: none;"
                            aria-label="すべてのフィルターをリセット"
                            type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="1 4 1 10 7 10"/>
                            <polyline points="23 20 23 14 17 14"/>
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>
                        リセット
                    </button>
                </div>

                <div class="yahoo-filters-grid">

                    <!-- Region -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="region-label">地域</label>
                        <div class="custom-select"
                             id="region-select"
                             role="combobox"
                             aria-labelledby="region-label"
                             aria-expanded="false">
                            <button class="select-trigger" type="button" aria-haspopup="listbox">
                                <span class="select-value">全国</span>
                                <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" role="listbox" style="display: none;">
                                <div class="select-option active" data-value="" role="option">全国</div>
                                <?php foreach ($region_groups as $region_slug => $region_name): ?>
                                    <div class="select-option" data-value="<?php echo esc_attr($region_slug); ?>" role="option">
                                        <?php echo esc_html($region_name); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Prefecture -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="prefecture-label">都道府県
                            <span class="multi-select-badge" id="prefecture-count-badge" style="display: none;">0</span>
                        </label>

                        <div class="custom-select multi-select"
                             id="prefecture-select"
                             role="combobox"
                             aria-labelledby="prefecture-label"
                             aria-expanded="false">
                            <button class="select-trigger" type="button" aria-haspopup="listbox">
                                <span class="select-value">選択</span>
                                <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>

                            <div class="select-dropdown multi-select-dropdown" role="listbox" style="display: none;">
                                <div class="select-search-wrapper">
                                    <input type="search"
                                           class="select-search-input"
                                           placeholder="検索..."
                                           id="prefecture-search"
                                           autocomplete="off">
                                </div>

                                <div class="select-options-wrapper" id="prefecture-options">
                                    <div class="select-option all-option" data-value="" role="option">
                                        <input type="checkbox" id="pref-all" class="option-checkbox">
                                        <label for="pref-all">すべて</label>
                                    </div>

                                    <?php foreach ($prefectures as $index => $pref): ?>
                                        <div class="select-option"
                                             data-value="<?php echo esc_attr($pref['slug']); ?>"
                                             data-region="<?php echo esc_attr($pref['region']); ?>"
                                             data-name="<?php echo esc_attr($pref['name']); ?>"
                                             role="option">
                                            <input type="checkbox"
                                                   id="pref-<?php echo (int) $index; ?>"
                                                   class="option-checkbox"
                                                   value="<?php echo esc_attr($pref['slug']); ?>">
                                            <label for="pref-<?php echo (int) $index; ?>">
                                                <?php echo esc_html($pref['name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="select-actions">
                                    <button class="select-action-btn clear-btn" id="clear-prefecture-btn" type="button">クリア</button>
                                    <button class="select-action-btn apply-btn" id="apply-prefecture-btn" type="button">適用</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Municipality -->
                    <div class="filter-dropdown-wrapper" id="municipality-wrapper" style="display: none;">
                        <label class="filter-label" id="municipality-label">市町村
                            <span class="selected-prefecture-name" id="selected-prefecture-name"></span>
                        </label>

                        <div class="custom-select"
                             id="municipality-select"
                             role="combobox"
                             aria-labelledby="municipality-label"
                             aria-expanded="false">
                            <button class="select-trigger" type="button" aria-haspopup="listbox">
                                <span class="select-value">すべて</span>
                                <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>

                            <div class="select-dropdown" role="listbox" style="display: none;">
                                <div class="select-search-wrapper">
                                    <input type="search"
                                           class="select-search-input"
                                           placeholder="検索..."
                                           id="municipality-search"
                                           autocomplete="off">
                                </div>

                                <div class="select-options-wrapper" id="municipality-options">
                                    <div class="select-option active" data-value="" role="option">すべて</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="amount-label">助成金額</label>

                        <div class="custom-select"
                             id="amount-select"
                             role="combobox"
                             aria-labelledby="amount-label"
                             aria-expanded="false">
                            <button class="select-trigger" type="button" aria-haspopup="listbox">
                                <span class="select-value">指定なし</span>
                                <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>

                            <div class="select-dropdown" role="listbox" style="display: none;">
                                <div class="select-option active" data-value="" role="option">指定なし</div>
                                <div class="select-option" data-value="0-100" role="option">〜100万円</div>
                                <div class="select-option" data-value="100-500" role="option">100万円〜500万円</div>
                                <div class="select-option" data-value="500-1000" role="option">500万円〜1000万円</div>
                                <div class="select-option" data-value="1000-3000" role="option">1000万円〜3000万円</div>
                                <div class="select-option" data-value="3000+" role="option">3000万円以上</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="status-label">募集状況</label>

                        <div class="custom-select"
                             id="status-select"
                             role="combobox"
                             aria-labelledby="status-label"
                             aria-expanded="false">
                            <button class="select-trigger" type="button" aria-haspopup="listbox">
                                <span class="select-value">すべて</span>
                                <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>

                            <div class="select-dropdown" role="listbox" style="display: none;">
                                <div class="select-option active" data-value="" role="option">すべて</div>
                                <div class="select-option" data-value="active" role="option">募集中</div>
                                <div class="select-option" data-value="upcoming" role="option">募集予定</div>
                                <div class="select-option" data-value="closed" role="option">募集終了</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="sort-label">並び順</label>

                        <div class="custom-select"
                             id="sort-select"
                             role="combobox"
                             aria-labelledby="sort-label"
                             aria-expanded="false">
                            <button class="select-trigger" type="button" aria-haspopup="listbox">
                                <span class="select-value">新着順</span>
                                <svg class="select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>

                            <div class="select-dropdown" role="listbox" style="display: none;">
                                <div class="select-option active" data-value="date_desc" role="option">新着順</div>
                                <div class="select-option" data-value="amount_desc" role="option">金額が高い順</div>
                                <div class="select-option" data-value="deadline_asc" role="option">締切が近い順</div>
                                <div class="select-option" data-value="featured_first" role="option">注目順</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Active filters -->
                <div class="active-filters-display" id="active-filters" style="display: none;">
                    <div class="active-filters-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        適用中:
                    </div>
                    <div class="active-filter-tags" id="active-filter-tags"></div>
                </div>

                <div class="mobile-filter-apply-section" id="mobile-filter-apply-section">
                    <button class="mobile-apply-filters-btn" id="mobile-apply-filters-btn" type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        フィルターを適用
                    </button>
                </div>
            </section>

            <!-- Results -->
            <section class="yahoo-results-section">

                <div class="results-header">
                    <div class="results-info">
                        <h2 class="results-title">検索結果</h2>
                        <div class="results-meta">
                            <span class="total-count">
                                <strong id="current-count">0</strong>件
                            </span>
                            <span class="showing-range">
                                （<span id="showing-from">1</span>〜<span id="showing-to">12</span>件を表示）
                            </span>
                        </div>
                    </div>

                    <div class="view-controls">
                        <button class="view-btn active" data-view="single" title="単体表示" type="button">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20"/>
                            </svg>
                        </button>
                        <button class="view-btn" data-view="grid" title="カード表示" type="button">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="loading-overlay" id="loading-overlay" style="display: none;">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="loading-text">検索中...</p>
                    </div>
                </div>

                <div class="grants-container-yahoo" id="grants-container" data-view="single">
                    <?php
                    if ($initial_query->have_posts()) :
                        while ($initial_query->have_posts()) :
                            $initial_query->the_post();
                            include(get_template_directory() . '/template-parts/grant-card-unified.php');
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<div class="no-results-message" style="text-align: center; padding: 60px 20px;">';
                        echo '<p style="font-size: 1.125rem; color: #666; margin-bottom: 20px;">該当する助成金が見つかりませんでした。</p>';
                        echo '<p style="color: #999;">検索条件を変更して再度お試しください。</p>';
                        echo '</div>';
                    endif;
                    ?>
                </div>

                <div class="no-results" id="no-results" style="display: none;">
                    <svg class="no-results-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <h3 class="no-results-title">該当する助成金が見つかりませんでした</h3>
                    <p class="no-results-message">検索条件を変更して再度お試しください。</p>
                </div>

                <div class="pagination-wrapper" id="pagination-wrapper">
                    <?php
                    if ($initial_query->max_num_pages > 1) {
                        $big = 999999999;

                        $preserved_params = array();
                        foreach ($_GET as $key => $value) {
                            if (!empty($value) && $key !== 'paged') {
                                $preserved_params[$key] = sanitize_text_field($value);
                            }
                        }

                        $base_url = add_query_arg(
                            $preserved_params,
                            str_replace($big, '%#%', esc_url(get_pagenum_link($big)))
                        );

                        echo paginate_links(array(
                            'base'      => $base_url,
                            'format'    => '&paged=%#%',
                            'current'   => max(1, (int) get_query_var('paged')),
                            'total'     => (int) $initial_query->max_num_pages,
                            'type'      => 'plain',
                            'prev_text' => '前へ',
                            'next_text' => '次へ',
                            'mid_size'  => 2,
                            'end_size'  => 1,
                            'add_args'  => $preserved_params,
                        ));
                    }
                    ?>
                </div>

            </section>
        </div>

        <!-- Sidebar (PC only) -->
        <aside class="yahoo-sidebar" role="complementary" aria-label="サイドバー">

            <?php
            error_log('🟣 taxonomy-grant_category.php: ji_display_ad exists: ' . (function_exists('ji_display_ad') ? 'YES' : 'NO'));
            error_log('🟣 taxonomy-grant_category.php: ji_get_ranking exists: ' . (function_exists('ji_get_ranking') ? 'YES' : 'NO'));
            ?>

            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-ad-space sidebar-ad-top">
                    <?php ji_display_ad('category_grant_sidebar_top', 'taxonomy-grant_category'); ?>
                </div>
            <?php endif; ?>

            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-ad-space sidebar-ad-middle">
                    <?php ji_display_ad('category_grant_sidebar_middle', 'taxonomy-grant_category'); ?>
                </div>
            <?php endif; ?>

            <?php
            $ranking_periods = array(
                array('days' => 3, 'label' => '3日間', 'id' => 'ranking-3days'),
                array('days' => 7, 'label' => '週間',   'id' => 'ranking-7days'),
                array('days' => 0, 'label' => '総合',   'id' => 'ranking-all'),
            );

            $default_period = 3;
            $ranking_data   = function_exists('ji_get_ranking') ? ji_get_ranking('grant', $default_period, 10) : array();
            ?>

            <section class="sidebar-widget sidebar-ranking">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        <polyline points="17 6 23 6 23 12"/>
                    </svg>
                    アクセスランキング
                </h3>

                <div class="ranking-tabs">
                    <?php foreach ($ranking_periods as $index => $period): ?>
                        <button type="button"
                                class="ranking-tab <?php echo $index === 0 ? 'active' : ''; ?>"
                                data-period="<?php echo esc_attr($period['days']); ?>"
                                data-target="#<?php echo esc_attr($period['id']); ?>">
                            <?php echo esc_html($period['label']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="widget-content">
                    <?php foreach ($ranking_periods as $index => $period): ?>
                        <div id="<?php echo esc_attr($period['id']); ?>"
                             class="ranking-content <?php echo $index === 0 ? 'active' : ''; ?>"
                             data-period="<?php echo esc_attr($period['days']); ?>">

                            <?php if ($index === 0): ?>
                                <?php if (!empty($ranking_data)): ?>
                                    <ol class="ranking-list">
                                        <?php foreach ($ranking_data as $rank => $item): ?>
                                            <li class="ranking-item rank-<?php echo (int) ($rank + 1); ?>">
                                                <a href="<?php echo esc_url(get_permalink($item->post_id)); ?>" class="ranking-link">
                                                    <span class="ranking-number"><?php echo (int) ($rank + 1); ?></span>
                                                    <span class="ranking-title"><?php echo esc_html(get_the_title($item->post_id)); ?></span>
                                                    <span class="ranking-views">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                        <?php echo number_format((int) $item->total_views); ?>
                                                    </span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php else: ?>
                                    <div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 10px; opacity: 0.3; display: block;" aria-hidden="true">
                                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                            <polyline points="17 6 23 6 23 12"/>
                                        </svg>
                                        <p style="margin: 0; font-size: 14px; font-weight: 500;">まだデータがありません</p>
                                        <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.7;">ページが閲覧されるとランキングが表示されます</p>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="ranking-loading">読み込み中...</div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="sidebar-widget sidebar-topics">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/>
                        <line x1="10" y1="1" x2="10" y2="4"/>
                        <line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                    <?php echo esc_html($category_name); ?>の新着トピックス
                </h3>

                <div class="widget-content">
                    <?php if ($recent_grants->have_posts()) : ?>
                        <ul class="topics-list">
                            <?php while ($recent_grants->have_posts()) : $recent_grants->the_post(); ?>
                                <li class="topics-item">
                                    <a href="<?php the_permalink(); ?>" class="topics-link">
                                        <time class="topics-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                            <?php echo esc_html(get_the_date('Y/m/d')); ?>
                                        </time>
                                        <span class="topics-title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data">データがありません</p>
                    <?php endif; ?>
                </div>
            </section>

            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-ad-space sidebar-ad-bottom">
                    <?php ji_display_ad('category_grant_sidebar_bottom', 'taxonomy-grant_category'); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($related_categories)): ?>
                <section class="sidebar-widget sidebar-related-categories">
                    <h3 class="widget-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                        </svg>
                        <?php echo (!empty($current_category->parent) && (int) $current_category->parent > 0) ? '関連カテゴリー' : 'サブカテゴリー'; ?>
                    </h3>

                    <div class="widget-content">
                        <ul class="related-categories-list">
                            <?php foreach ($related_categories as $rel_cat): ?>
                                <?php
                                $rel_link = get_term_link($rel_cat);
                                if (is_wp_error($rel_link)) continue;
                                ?>
                                <li class="related-category-item">
                                    <a href="<?php echo esc_url($rel_link); ?>" class="related-category-link">
                                        <?php echo esc_html($rel_cat->name); ?>
                                        <span class="related-category-count">(<?php echo number_format((int) $rel_cat->count); ?>)</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php endif; ?>

        </aside>
    </div>

</main>

<?php 
/**
 * CSS/JS外部化 - 共通ファイルを使用
 * archive-common.css と archive-common.js はフォールバックとして直接読み込み
 */
$js_file = get_template_directory() . '/assets/js/archive-common.js';
$js_uri = get_template_directory_uri() . '/assets/js/archive-common.js';
?>

<?php if (file_exists($js_file) && !wp_script_is('gi-archive-common-js', 'done')): ?>
<script src="<?php echo esc_url($js_uri . '?ver=' . filemtime($js_file)); ?>"></script>
<?php endif; ?>

<!-- 初期化スクリプト -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ArchiveCommon !== 'undefined') {
        ArchiveCommon.init({
            ajaxUrl: '<?php echo admin_url("admin-ajax.php"); ?>',
            nonce: '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>',
            postType: 'grant',
            fixedCategory: '<?php echo esc_js($category_slug ?? ""); ?>',
            fixedPrefecture: '',
            fixedMunicipality: '',
            fixedPurpose: '',
            fixedTag: ''
        });
    }
});
</script>

<?php get_footer(); ?>
