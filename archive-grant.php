<?php
/**
 * Archive Template for Grant Post Type - Yahoo! JAPAN Inspired SEO Perfect Edition
 * 助成金・補助金アーカイブページ - Yahoo!風デザイン・SEO完全最適化版
 * 
 * @package Grant_Insight_Perfect
 * @version 19.0.0 - Yahoo! JAPAN Style with Sidebar
 * 
 * === Features ===
 * - Yahoo! JAPAN inspired design
 * - Sidebar layout (PC only) with rankings & topics
 * - Ad spaces reserved in sidebar
 * - Mobile: No sidebar, optimized single column
 * - SEO Perfect (Schema.org, OGP, Twitter Card)
 * - All functions preserved (no breaking changes)
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

// URLパラメータの取得と処理
$url_params = array(
    'application_status' => isset($_GET['application_status']) ? sanitize_text_field($_GET['application_status']) : '',
    'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '',
    'target' => isset($_GET['target']) ? sanitize_text_field($_GET['target']) : '',
    'view' => isset($_GET['view']) ? sanitize_text_field($_GET['view']) : '',
    'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
    'category' => isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '',
    'prefecture' => isset($_GET['prefecture']) ? sanitize_text_field($_GET['prefecture']) : '',
);

// 各種データ取得
$queried_object = get_queried_object();
$current_category = $queried_object; // 互換性のため別名も保持
$is_category_archive = is_tax('grant_category');
$is_prefecture_archive = is_tax('grant_prefecture');
$is_municipality_archive = is_tax('grant_municipality');
$is_tag_archive = is_tax('grant_tag');

// タイトル・説明文の生成（URLパラメータに基づく）
if (!empty($url_params['application_status']) && $url_params['application_status'] === 'open') {
    $archive_title = '募集中の助成金・補助金';
    $archive_description = '現在募集中の助成金・補助金情報。今すぐ申請可能な最新の支援制度を掲載。専門家による申請サポート完備。';
} elseif (!empty($url_params['orderby']) && $url_params['orderby'] === 'deadline') {
    $archive_title = '締切間近の助成金・補助金';
    $archive_description = '締切が迫っている助成金・補助金を優先表示。今すぐチェックして申請のチャンスを逃さないようにしましょう。';
} elseif (!empty($url_params['orderby']) && $url_params['orderby'] === 'new') {
    $archive_title = '新着の助成金・補助金';
    $archive_description = '最新公開の助成金・補助金情報。新しい支援制度をいち早くチェック。毎日更新中。';
} elseif (!empty($url_params['target'])) {
    $target_labels = array(
        'individual' => '個人向け',
        'business' => '法人・事業者向け',
        'npo' => 'NPO・団体向け',
        'startup' => 'スタートアップ向け'
    );
    $target_label = isset($target_labels[$url_params['target']]) ? $target_labels[$url_params['target']] : '';
    if ($target_label) {
        $archive_title = $target_label . 'の助成金・補助金';
        $archive_description = $target_label . 'に適した助成金・補助金を厳選。申請要件や対象経費など詳細情報を掲載。';
    } else {
        $archive_title = '助成金・補助金総合検索';
        $archive_description = '全国の助成金・補助金情報を網羅的に検索。都道府県・市町村・業種・金額で詳細に絞り込み可能。専門家による申請サポート完備。毎日更新。';
    }
} elseif (!empty($url_params['view']) && $url_params['view'] === 'prefectures') {
    $archive_title = '都道府県別助成金・補助金一覧';
    $archive_description = '全国47都道府県の助成金・補助金情報を地域別に検索。お住まいの地域で利用できる支援制度を簡単に見つけられます。';
} elseif ($is_category_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->description ?: $current_category->name . 'に関する助成金・補助金の情報を網羅。申請方法から採択のコツまで、専門家監修の最新情報を提供しています。';
} elseif ($is_prefecture_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->name . 'で利用できる助成金・補助金の最新情報。地域別・業種別に検索可能。専門家による申請サポート完備。';
} elseif ($is_municipality_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->name . 'の地域密着型助成金・補助金情報。市町村独自の支援制度から国の制度まで幅広く掲載。';
} elseif ($is_tag_archive) {
    $archive_title = $current_category->name . 'の助成金・補助金';
    $archive_description = $current_category->name . 'に関連する助成金・補助金の一覧。最新の募集情報を毎日更新。';
} else {
    $archive_title = '助成金・補助金総合検索';
    $archive_description = '全国の助成金・補助金情報を網羅的に検索。都道府県・市町村・業種・金額で詳細に絞り込み可能。専門家による申請サポート完備。毎日更新。';
}

// カテゴリデータの取得
$all_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
]);

// SEO対策データ
$current_year = date('Y');
$current_month = date('n');
$popular_categories = array_slice($all_categories, 0, 6);
$current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));

// FIX: canonical URL excludes filter parameters to prevent duplicate content
// フィルターパラメータを除外した正規URLを使用
$canonical_url = get_post_type_archive_link('grant');
if ($is_category_archive && $queried_object) {
    $canonical_url = get_term_link($queried_object);
}
if ($is_prefecture_archive && $queried_object) {
    $canonical_url = get_term_link($queried_object);
}
if ($is_municipality_archive && $queried_object) {
    $canonical_url = get_term_link($queried_object);
}
if ($is_tag_archive && $queried_object) {
    $canonical_url = get_term_link($queried_object);
}

// 都道府県データ
$prefectures = gi_get_all_prefectures();

$region_groups = [
    'hokkaido' => '北海道',
    'tohoku' => '東北',
    'kanto' => '関東',
    'chubu' => '中部',
    'kinki' => '近畿',
    'chugoku' => '中国',
    'shikoku' => '四国',
    'kyushu' => '九州・沖縄'
];

// 総件数
$total_grants = wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// サイドバー用：新着トピックス
$recent_grants = new WP_Query([
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
]);

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')]
];

if ($is_category_archive || $is_prefecture_archive || $is_municipality_archive || $is_tag_archive) {
    $breadcrumbs[] = ['name' => $archive_title, 'url' => ''];
} else {
    $breadcrumbs[] = ['name' => '検索結果', 'url' => ''];
}

// 構造化データ: CollectionPage
$schema_collection = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $archive_title,
    'description' => $archive_description,
    'url' => $canonical_url,
    'inLanguage' => 'ja-JP',
    'dateModified' => current_time('c'),
    'provider' => [
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url(),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => get_site_icon_url(512) ?: home_url('/wp-content/uploads/2025/10/1.png')
        ]
    ],
    'mainEntity' => [
        '@type' => 'ItemList',
        'name' => $archive_title,
        'description' => $archive_description,
        'numberOfItems' => $total_grants,
        'itemListElement' => []
    ]
];

// 構造化データ: BreadcrumbList
$breadcrumb_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => []
];

foreach ($breadcrumbs as $index => $breadcrumb) {
    $breadcrumb_schema['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $breadcrumb['name'],
        'item' => !empty($breadcrumb['url']) ? $breadcrumb['url'] : $canonical_url
    ];
}

// NOTE: SearchAction（WebSite schema）はheader.phpでサイト全体に出力されるため削除
// 重複するWebSite schemaはGoogleのSEO評価に悪影響を与える可能性があります

// OGP画像
$og_image = get_site_icon_url(1200) ?: home_url('/wp-content/uploads/2025/10/1.png');

// キーワード生成
$keywords = ['助成金', '補助金', '検索', '申請', '支援制度'];
if ($is_category_archive) {
    $keywords[] = $current_category->name;
}
if ($is_prefecture_archive) {
    $keywords[] = $current_category->name;
}
$keywords_string = implode(',', $keywords);
?>

<?php
/**
 * 構造化データ出力
 * ⚠️ SEOプラグイン（Rank Math等）が有効な場合は出力をスキップ
 */
if (!function_exists('gi_is_seo_plugin_active') || !gi_is_seo_plugin_active()):
?>
<!-- 構造化データ: CollectionPage (Theme Generated - No SEO Plugin) -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php endif; // End SEO plugin check ?>

<main class="grant-archive-yahoo-style" 
      id="grant-archive" 
      role="main"
      itemscope 
      itemtype="https://schema.org/CollectionPage">

    <!-- 📚 本・図鑑風パンくずリスト -->
    <nav class="breadcrumb-nav book-breadcrumb" 
         aria-label="パンくずリスト" 
         itemscope 
         itemtype="https://schema.org/BreadcrumbList">
        <div class="book-breadcrumb-spine"></div>
        <div class="yahoo-container">
            <div class="book-breadcrumb-inner">
                <div class="book-breadcrumb-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        <path d="M8 7h8M8 11h5"/>
                    </svg>
                </div>
                <ol class="breadcrumb-list">
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                    <li class="breadcrumb-item" 
                        itemprop="itemListElement" 
                        itemscope 
                        itemtype="https://schema.org/ListItem">
                        <?php if (!empty($breadcrumb['url'])): ?>
                            <a href="<?php echo esc_url($breadcrumb['url']); ?>" 
                               itemprop="item"
                               class="book-breadcrumb-link"
                               title="<?php echo esc_attr($breadcrumb['name']); ?>へ移動">
                                <span class="book-breadcrumb-chapter">第<?php echo $index + 1; ?>章</span>
                                <span itemprop="name" class="book-breadcrumb-text"><?php echo esc_html($breadcrumb['name']); ?></span>
                            </a>
                            <span class="book-breadcrumb-sep" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </span>
                        <?php else: ?>
                            <span class="book-breadcrumb-current">
                                <span class="book-breadcrumb-chapter">本ページ</span>
                                <span itemprop="name" class="book-breadcrumb-text"><?php echo esc_html($breadcrumb['name']); ?></span>
                            </span>
                        <?php endif; ?>
                        <meta itemprop="position" content="<?php echo $index + 1; ?>">
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </nav>

    <!-- ヒーローセクション（図鑑式・横長レイアウト） -->
    <header class="yahoo-hero-section" 
            itemscope 
            itemtype="https://schema.org/WPHeader">
        <div class="yahoo-container">
            <div class="hero-content-wrapper">
                <div class="hero-encyclopedia-layout">
                    
                    <!-- 左側：タイトル・説明 -->
                    <div class="hero-main-info">
                        <div class="hero-region-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <span>助成金・補助金検索</span>
                        </div>
                        <h1 class="hero-title-encyclopedia" itemprop="headline">
                            <span class="hero-title-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    <path d="M8 7h8M8 11h5"/>
                                </svg>
                            </span>
                            <?php echo esc_html($archive_title); ?>
                        </h1>
                        <p class="hero-subtitle" itemprop="description">
                            <?php echo esc_html($archive_description); ?>
                        </p>
                    </div>
                    
                    <!-- 中央：統計情報 -->
                    <div class="hero-stats-area">
                        <div class="hero-stat-card" itemscope itemtype="https://schema.org/QuantitativeValue">
                            <span class="hero-stat-number" itemprop="value"><?php echo $total_grants_formatted; ?></span>
                            <span class="hero-stat-label" itemprop="unitText">件の助成金</span>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hero-stat-number"><?php echo $current_year; ?></span>
                            <span class="hero-stat-label">年度版</span>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hero-stat-number">47</span>
                            <span class="hero-stat-label">都道府県対応</span>
                        </div>
                    </div>
                    
                    <!-- 右側：クイックリンク -->
                    <div class="hero-action-area">
                        <div class="hero-quick-links">
                            <a href="<?php echo esc_url(add_query_arg('view', 'prefectures', get_post_type_archive_link('grant'))); ?>" class="hero-quick-link">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                都道府県から探す
                            </a>
                            <a href="#filter-panel" class="hero-quick-link">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                絞り込み検索
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </header>

    <!-- 2カラムレイアウト -->
    <div class="yahoo-container yahoo-two-column-layout">
        
        <!-- メインコンテンツ -->
        <div class="yahoo-main-content">
            
            <!-- 検索バー -->
            <section class="yahoo-search-section">
                <div class="search-bar-wrapper">
                    <label for="keyword-search" class="visually-hidden">キーワード検索</label>
                    <div class="search-input-container">
                        <svg class="search-icon" 
                             width="20" 
                             height="20" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" 
                               id="keyword-search" 
                               class="search-input" 
                               placeholder="助成金名、実施機関、対象事業で検索（スペース区切りでAND検索）..."
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
                    <!-- 検索候補ドロップダウン -->
                    <div class="search-suggestions" id="search-suggestions" style="display: none;">
                        <div class="suggestions-header">検索候補</div>
                        <ul class="suggestions-list" id="suggestions-list"></ul>
                    </div>
                </div>
            </section>

            <!-- モバイル用フィルター開閉ボタン -->
            <button class="mobile-filter-toggle" id="mobile-filter-toggle" type="button" aria-label="フィルターを開く">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span>絞り込み</span>
            </button>

            <!-- フィルターパネル背景オーバーレイ -->
            <div class="filter-panel-overlay" id="filter-panel-overlay"></div>

            <!-- プルダウン式フィルターセクション -->
            <section class="yahoo-filter-section" id="filter-panel" 
                     role="search" 
                     aria-label="助成金検索フィルター">
                
                <!-- フィルターヘッダー -->
                <!-- FIX: Changed h2 to h3 for better heading hierarchy (h1: page title, h2: major sections, h3: subsections) -->
                <div class="filter-header">
                    <h3 class="filter-title">
                        <svg class="title-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        絞り込み
                    </h3>
                    <button class="mobile-filter-close" 
                            id="mobile-filter-close"
                            aria-label="フィルターを閉じる"
                            type="button">×</button>
                    <button class="filter-reset-all" 
                            id="reset-all-filters-btn" 
                            style="display: none;" 
                            aria-label="すべてのフィルターをリセット"
                            type="button">
                        <svg width="14" 
                             height="14" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <polyline points="1 4 1 10 7 10"/>
                            <polyline points="23 20 23 14 17 14"/>
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>
                        リセット
                    </button>
                </div>

                <!-- プルダウンフィルターグリッド -->
                <div class="yahoo-filters-grid">
                    
                    <!-- カテゴリ選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="category-label">カテゴリ
                            <span class="multi-select-badge" 
                                  id="category-count-badge" 
                                  style="display: none;">0</span>
                        </label>
                        <div class="custom-select multi-select" 
                             id="category-select" 
                             role="combobox" 
                             aria-labelledby="category-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">選択</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown multi-select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-search-wrapper">
                                    <input type="search" 
                                           class="select-search-input" 
                                           placeholder="検索..."
                                           id="category-search"
                                           autocomplete="off">
                                </div>
                                <div class="select-options-wrapper" id="category-options">
                                    <div class="select-option all-option" 
                                         data-value="" 
                                         role="option">
                                        <input type="checkbox" 
                                               id="cat-all" 
                                               class="option-checkbox">
                                        <label for="cat-all">すべて</label>
                                    </div>
                                    <?php foreach ($all_categories as $index => $category): ?>
                                        <div class="select-option" 
                                             data-value="<?php echo esc_attr($category->slug); ?>"
                                             data-name="<?php echo esc_attr($category->name); ?>"
                                             role="option">
                                            <input type="checkbox" 
                                                   id="cat-<?php echo $index; ?>" 
                                                   class="option-checkbox" 
                                                   value="<?php echo esc_attr($category->slug); ?>">
                                            <label for="cat-<?php echo $index; ?>">
                                                <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="select-actions">
                                    <button class="select-action-btn clear-btn" 
                                            id="clear-category-btn" 
                                            type="button">クリア</button>
                                    <button class="select-action-btn apply-btn" 
                                            id="apply-category-btn" 
                                            type="button">適用</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 地域選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="region-label">地域</label>
                        <div class="custom-select" 
                             id="region-select" 
                             role="combobox" 
                             aria-labelledby="region-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">全国</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option">全国</div>
                                <?php foreach ($region_groups as $region_slug => $region_name): ?>
                                    <div class="select-option" 
                                         data-value="<?php echo esc_attr($region_slug); ?>" 
                                         role="option">
                                        <?php echo esc_html($region_name); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 都道府県選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="prefecture-label">都道府県
                            <span class="multi-select-badge" 
                                  id="prefecture-count-badge" 
                                  style="display: none;">0</span>
                        </label>
                        <div class="custom-select multi-select" 
                             id="prefecture-select" 
                             role="combobox" 
                             aria-labelledby="prefecture-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">選択</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown multi-select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-search-wrapper">
                                    <input type="search" 
                                           class="select-search-input" 
                                           placeholder="検索..."
                                           id="prefecture-search"
                                           autocomplete="off">
                                </div>
                                <div class="select-options-wrapper" id="prefecture-options">
                                    <div class="select-option all-option" 
                                         data-value="" 
                                         role="option">
                                        <input type="checkbox" 
                                               id="pref-all" 
                                               class="option-checkbox">
                                        <label for="pref-all">すべて</label>
                                    </div>
                                    <?php foreach ($prefectures as $index => $pref): ?>
                                        <div class="select-option" 
                                             data-value="<?php echo esc_attr($pref['slug']); ?>"
                                             data-region="<?php echo esc_attr($pref['region']); ?>"
                                             data-name="<?php echo esc_attr($pref['name']); ?>"
                                             role="option">
                                            <input type="checkbox" 
                                                   id="pref-<?php echo $index; ?>" 
                                                   class="option-checkbox" 
                                                   value="<?php echo esc_attr($pref['slug']); ?>">
                                            <label for="pref-<?php echo $index; ?>">
                                                <?php echo esc_html($pref['name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="select-actions">
                                    <button class="select-action-btn clear-btn" 
                                            id="clear-prefecture-btn" 
                                            type="button">クリア</button>
                                    <button class="select-action-btn apply-btn" 
                                            id="apply-prefecture-btn" 
                                            type="button">適用</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 市町村選択 -->
                    <div class="filter-dropdown-wrapper" 
                         id="municipality-wrapper" 
                         style="display: none;">
                        <label class="filter-label" id="municipality-label">市町村
                            <span class="selected-prefecture-name" 
                                  id="selected-prefecture-name"></span>
                        </label>
                        <div class="custom-select" 
                             id="municipality-select" 
                             role="combobox" 
                             aria-labelledby="municipality-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">すべて</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-search-wrapper">
                                    <input type="search" 
                                           class="select-search-input" 
                                           placeholder="検索..."
                                           id="municipality-search"
                                           autocomplete="off">
                                </div>
                                <div class="select-options-wrapper" id="municipality-options">
                                    <div class="select-option active" 
                                         data-value="" 
                                         role="option">すべて</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 助成金額 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="amount-label">助成金額</label>
                        <div class="custom-select" 
                             id="amount-select" 
                             role="combobox" 
                             aria-labelledby="amount-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">指定なし</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option">指定なし</div>
                                <div class="select-option" 
                                     data-value="0-100" 
                                     role="option">〜100万円</div>
                                <div class="select-option" 
                                     data-value="100-500" 
                                     role="option">100万円〜500万円</div>
                                <div class="select-option" 
                                     data-value="500-1000" 
                                     role="option">500万円〜1000万円</div>
                                <div class="select-option" 
                                     data-value="1000-3000" 
                                     role="option">1000万円〜3000万円</div>
                                <div class="select-option" 
                                     data-value="3000+" 
                                     role="option">3000万円以上</div>
                            </div>
                        </div>
                    </div>

                    <!-- 募集状況 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="status-label">募集状況</label>
                        <div class="custom-select" 
                             id="status-select" 
                             role="combobox" 
                             aria-labelledby="status-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">すべて</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option">すべて</div>
                                <div class="select-option" 
                                     data-value="active" 
                                     role="option">募集中</div>
                                <div class="select-option" 
                                     data-value="upcoming" 
                                     role="option">募集予定</div>
                                <div class="select-option" 
                                     data-value="closed" 
                                     role="option">募集終了</div>
                            </div>
                        </div>
                    </div>

                    <!-- 並び順 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="sort-label">並び順</label>
                        <div class="custom-select" 
                             id="sort-select" 
                             role="combobox" 
                             aria-labelledby="sort-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">新着順</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-option active" 
                                     data-value="date_desc" 
                                     role="option">新着順</div>
                                <div class="select-option" 
                                     data-value="amount_desc" 
                                     role="option">金額が高い順</div>
                                <div class="select-option" 
                                     data-value="deadline_asc" 
                                     role="option">締切が近い順</div>
                                <div class="select-option" 
                                     data-value="popular_desc" 
                                     role="option">人気順</div>
                                <div class="select-option" 
                                     data-value="featured_first" 
                                     role="option">注目順</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 選択中のフィルター表示 -->
                <div class="active-filters-display" 
                     id="active-filters" 
                     style="display: none;">
                    <div class="active-filters-label">
                        <svg width="14" 
                             height="14" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        適用中:
                    </div>
                    <div class="active-filter-tags" id="active-filter-tags"></div>
                </div>
                
                <!-- モバイル用フィルター適用ボタン -->
                <div class="mobile-filter-apply-section" id="mobile-filter-apply-section">
                    <button class="mobile-apply-filters-btn" 
                            id="mobile-apply-filters-btn" 
                            type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        フィルターを適用
                    </button>
                </div>
            </section>

            <!-- 検索結果セクション -->
            <section class="yahoo-results-section">
                
                <!-- 結果ヘッダー（ページ遷移時のスクロール先） -->
                <div class="results-header" id="results-header">
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
                        <button class="view-btn active" 
                                data-view="single" 
                                title="単体表示" 
                                type="button">
                            <svg width="18" 
                                 height="18" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20"/>
                            </svg>
                        </button>
                        <button class="view-btn" 
                                data-view="grid" 
                                title="カード表示" 
                                type="button">
                            <svg width="18" 
                                 height="18" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ローディング -->
                <div class="loading-overlay" 
                     id="loading-overlay" 
                     style="display: none;">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="loading-text">検索中...</p>
                    </div>
                </div>

                <!-- 都道府県一覧表示（view=prefectures の場合） -->
                <?php if (!empty($url_params['view']) && $url_params['view'] === 'prefectures'): ?>
                <div class="prefectures-grid-container" style="padding: 40px 0;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 30px; color: #000;">都道府県から助成金を探す</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                        <?php
                        $all_prefectures = get_terms(array(
                            'taxonomy' => 'grant_prefecture',
                            'hide_empty' => true,
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ));
                        
                        if ($all_prefectures && !is_wp_error($all_prefectures)) {
                            foreach ($all_prefectures as $pref) {
                                $pref_link = get_term_link($pref);
                                $pref_count = $pref->count;
                                echo '<a href="' . esc_url($pref_link) . '" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; text-decoration: none; transition: all 0.15s; color: #000;">';
                                echo '<span style="font-weight: 600; font-size: 0.9375rem;">' . esc_html($pref->name) . '</span>';
                                echo '<span style="color: #666; font-size: 0.875rem;">' . number_format($pref_count) . '件</span>';
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php else: ?>
                
                <!-- 助成金表示エリア -->
                <div class="grants-container-yahoo" 
                     id="grants-container" 
                     data-view="single">
                    <?php
                    // WP_Queryの引数を構築
                    $query_args = array(
                        'post_type' => 'grant',
                        'posts_per_page' => 12,
                        'post_status' => 'publish',
                        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                    );
                    
                    // メタクエリの初期化
                    $meta_query = array('relation' => 'AND');
                    
                    // 募集状況フィルタ
                    if (!empty($url_params['application_status']) && $url_params['application_status'] === 'open') {
                        $meta_query[] = array(
                            'key' => 'application_status',
                            'value' => 'open',
                            'compare' => '='
                        );
                    }
                    
                    // 対象者フィルタ
                    if (!empty($url_params['target'])) {
                        $meta_query[] = array(
                            'key' => 'grant_target',
                            'value' => $url_params['target'],
                            'compare' => 'LIKE'
                        );
                    }
                    
                    // メタクエリを追加
                    if (count($meta_query) > 1) {
                        $query_args['meta_query'] = $meta_query;
                    }
                    
                    // タクソノミークエリ
                    $tax_query = array();
                    
                    // カテゴリーフィルタ（URL パラメータ）
                    if (!empty($url_params['category'])) {
                        $tax_query[] = array(
                            'taxonomy' => 'grant_category',
                            'field' => 'slug',
                            'terms' => $url_params['category']
                        );
                    }
                    
                    // 都道府県フィルタ（URLパラメータ）
                    if (!empty($url_params['prefecture'])) {
                        $tax_query[] = array(
                            'taxonomy' => 'grant_prefecture',
                            'field' => 'slug',
                            'terms' => $url_params['prefecture']
                        );
                    }
                    
                    // タクソノミークエリを追加
                    if (!empty($tax_query)) {
                        $query_args['tax_query'] = $tax_query;
                    }
                    
                    // 検索キーワード
                    if (!empty($url_params['search'])) {
                        $query_args['s'] = $url_params['search'];
                    }
                    
                    // ソート順の設定
                    if (!empty($url_params['orderby'])) {
                        switch ($url_params['orderby']) {
                            case 'deadline':
                                // 締切日順（昇順 = 近い順）
                                $query_args['meta_key'] = 'deadline_date';
                                $query_args['orderby'] = 'meta_value';
                                $query_args['order'] = 'ASC';
                                // 過去の締切は除外
                                $meta_query[] = array(
                                    'key' => 'deadline_date',
                                    'value' => date('Y-m-d'),
                                    'compare' => '>=',
                                    'type' => 'DATE'
                                );
                                if (count($meta_query) > 0) {
                                    $query_args['meta_query'] = $meta_query;
                                }
                                break;
                            case 'new':
                                // 新着順
                                $query_args['orderby'] = 'date';
                                $query_args['order'] = 'DESC';
                                break;
                            case 'popular':
                                // 人気順（閲覧数）
                                $query_args['meta_key'] = 'view_count';
                                $query_args['orderby'] = 'meta_value_num';
                                $query_args['order'] = 'DESC';
                                break;
                            default:
                                // デフォルト：日付順
                                $query_args['orderby'] = 'date';
                                $query_args['order'] = 'DESC';
                        }
                    } else {
                        // デフォルト：日付順
                        $query_args['orderby'] = 'date';
                        $query_args['order'] = 'DESC';
                    }
                    
                    // クエリ実行
                    $initial_grants_query = new WP_Query($query_args);
                    
                    if ($initial_grants_query->have_posts()) :
                        $grant_count = 0; // インフィード広告用カウンター
                        while ($initial_grants_query->have_posts()) : 
                            $initial_grants_query->the_post();
                            include(get_template_directory() . '/template-parts/grant-card-unified.php');
                            
                            $grant_count++;
                            
                            // 4件目と8件目の後にインフィード広告を挿入（スマホ収益化対策）
                            if (($grant_count === 4 || $grant_count === 8) && function_exists('ji_display_ad')) : ?>
                                <div class="archive-infeed-ad" style="margin: 24px 0; padding: 15px; background: #f9f9f9; border-radius: 8px; text-align: center;">
                                    <span style="font-size: 10px; color: #999; display: block; text-align: left; margin-bottom: 8px;">スポンサーリンク</span>
                                    <?php ji_display_ad('archive_grant_infeed'); ?>
                                </div>
                            <?php endif;
                            
                        endwhile;
                        wp_reset_postdata();
                    else :
                        // 結果なしの場合
                        echo '<div class="no-results-message" style="text-align: center; padding: 60px 20px;">';
                        echo '<p style="font-size: 1.125rem; color: #666; margin-bottom: 20px;">該当する助成金が見つかりませんでした。</p>';
                        echo '<p style="color: #999;">検索条件を変更して再度お試しください。</p>';
                        echo '</div>';
                    endif;
                    ?>
                </div>
                <?php endif; // view=prefectures の条件終了 ?>

                <!-- 結果なし -->
                <div class="no-results" 
                     id="no-results" 
                     style="display: none;">
                    <svg class="no-results-icon" 
                         width="64" 
                         height="64" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <h3 class="no-results-title">該当する助成金が見つかりませんでした</h3>
                    <p class="no-results-message">
                        検索条件を変更して再度お試しください。
                    </p>
                </div>

                <!-- ページネーション（都道府県一覧以外） -->
                <?php if (empty($url_params['view']) || $url_params['view'] !== 'prefectures'): ?>
                <div class="pagination-wrapper" 
                     id="pagination-wrapper">
                    <?php
                    if (isset($initial_grants_query) && $initial_grants_query->max_num_pages > 1) {
                        $big = 999999999;
                        
                        // すべての現在のクエリパラメータを保持
                        $preserved_params = array();
                        foreach ($url_params as $key => $value) {
                            if (!empty($value) && $key !== 'paged') {
                                $preserved_params[$key] = $value;
                            }
                        }
                        
                        // ベースURLにクエリパラメータを追加
                        $base_url = add_query_arg($preserved_params, str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ));
                        
                        echo paginate_links( array(
                            'base' => $base_url,
                            'format' => '&paged=%#%',
                            'current' => max( 1, get_query_var('paged') ),
                            'total' => $initial_grants_query->max_num_pages,
                            'type' => 'plain',
                            'prev_text' => '前へ',
                            'next_text' => '次へ',
                            'mid_size' => 2,
                            'end_size' => 1,
                            'add_args' => $preserved_params,
                        ) );
                    }
                    ?>
                </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- サイドバー（PC only） -->
        <aside class="yahoo-sidebar" role="complementary" aria-label="サイドバー">
            
            <!-- 広告枠1: サイドバー上部 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-top">
                <?php ji_display_ad('archive_grant_sidebar_top', 'archive-grant'); ?>
            </div>
            <?php endif; ?>

            <!-- 広告枠2: サイドバー中央 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-middle">
                <?php ji_display_ad('archive_grant_sidebar_middle', 'archive-grant'); ?>
            </div>
            <?php endif; ?>

            <!-- アクセスランキング -->
            <?php
            $ranking_periods = array(
                array('days' => 3, 'label' => '3日間', 'id' => 'ranking-3days'),
                array('days' => 7, 'label' => '週間', 'id' => 'ranking-7days'),
                array('days' => 0, 'label' => '総合', 'id' => 'ranking-all'),
            );
            
            $default_period = 3;
            $ranking_data = function_exists('ji_get_ranking') ? ji_get_ranking('grant', $default_period, 10) : array();
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
                        <button 
                            type="button" 
                            class="ranking-tab <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-period="<?php echo esc_attr($period['days']); ?>"
                            data-target="#<?php echo esc_attr($period['id']); ?>">
                            <?php echo esc_html($period['label']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="widget-content">
                    <?php foreach ($ranking_periods as $index => $period): ?>
                        <div 
                            id="<?php echo esc_attr($period['id']); ?>" 
                            class="ranking-content <?php echo $index === 0 ? 'active' : ''; ?>"
                            data-period="<?php echo esc_attr($period['days']); ?>">
                            
                            <?php if ($index === 0): ?>
                                <?php if (!empty($ranking_data)): ?>
                                    <ol class="ranking-list">
                                        <?php foreach ($ranking_data as $rank => $item): ?>
                                            <li class="ranking-item rank-<?php echo $rank + 1; ?>">
                                                <a href="<?php echo get_permalink($item->post_id); ?>" class="ranking-link">
                                                    <span class="ranking-number"><?php echo $rank + 1; ?></span>
                                                    <span class="ranking-title">
                                                        <?php echo esc_html(get_the_title($item->post_id)); ?>
                                                    </span>
                                                    <span class="ranking-views">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                        <?php echo number_format($item->total_views); ?>
                                                    </span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php else: ?>
                                    <div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 10px; opacity: 0.3; display: block;">
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

            <!-- 新着トピックス -->
            <section class="sidebar-widget sidebar-topics">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/>
                        <line x1="10" y1="1" x2="10" y2="4"/>
                        <line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                    新着トピックス
                </h3>
                <div class="widget-content">
                    <?php if ($recent_grants->have_posts()) : ?>
                        <ul class="topics-list">
                            <?php while ($recent_grants->have_posts()) : $recent_grants->the_post(); ?>
                                <li class="topics-item">
                                    <a href="<?php the_permalink(); ?>" class="topics-link">
                                        <time class="topics-date" datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date('Y/m/d'); ?>
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

            <!-- 広告枠3 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-bottom">
                <?php ji_display_ad('archive_grant_sidebar_bottom', 'archive-grant'); ?>
            </div>
            <?php endif; ?>

            <!-- カテゴリ一覧 -->
            <section class="sidebar-widget sidebar-categories">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                    カテゴリ
                </h3>
                <div class="widget-content">
                    <?php if (!empty($all_categories)) : ?>
                        <ul class="categories-list">
                            <?php foreach (array_slice($all_categories, 0, 10) as $category) : ?>
                                <li class="categories-item">
                                    <a href="<?php echo get_term_link($category); ?>" class="categories-link">
                                        <?php echo esc_html($category->name); ?>
                                        <span class="categories-count">(<?php echo $category->count; ?>)</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data">データがありません</p>
                    <?php endif; ?>
                </div>
            </section>
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
            fixedCategory: '',
            fixedPrefecture: '',
            fixedMunicipality: '',
            fixedPurpose: '',
            fixedTag: ''
        });
    }
});
</script>

<?php 
// デバッグ情報（開発時のみ）
if (defined('WP_DEBUG') && WP_DEBUG): ?>
<script>
(function() {
    console.log('\n🔍 === Archive Debug Info ===');
    console.log('📍 Page: archive-grant.php');
    <?php
    echo "console.log('🔵 PHP Debug Info:');";
    echo "console.log('  - ji_display_ad exists: " . (function_exists('ji_display_ad') ? 'YES ✅' : 'NO ❌') . "');";
    echo "console.log('  - JI_Affiliate_Ad_Manager class exists: " . (class_exists('JI_Affiliate_Ad_Manager') ? 'YES ✅' : 'NO ❌') . "');";
    ?>
    console.log('🔍 ================================\n');
})();
</script>
<?php endif;

// CSS/JSは外部ファイルに移行済み (assets/css/archive-common.css, assets/js/archive-common.js)

// モバイル検索モーダルを追加（一覧ページ用）
get_template_part('template-parts/sidebar/mobile-search-modal'); 

get_footer(); 
?>