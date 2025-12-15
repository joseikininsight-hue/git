<?php
/**
 * Prefecture Archive Template for Grant - Yahoo! JAPAN Inspired SEO Perfect Edition
 * 都道府県別助成金・補助金アーカイブページ - Yahoo!風デザイン・SEO完全最適化版
 * 
 * @package Grant_Insight_Perfect
 * @version 19.0.0 - Prefecture Specialized with Yahoo! JAPAN Style
 * 
 * === Features ===
 * - Based on archive-grant.php structure
 * - Prefecture-fixed filter (prefecture selector hidden)
 * - Yahoo! JAPAN inspired design
 * - Sidebar layout (PC only) with rankings & topics
 * - Ad spaces reserved in sidebar
 * - Mobile: No sidebar, optimized single column
 * - SEO Perfect (Schema.org, OGP, Twitter Card)
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

// 現在の都道府県情報を取得
$current_prefecture = get_queried_object();
$prefecture_name = $current_prefecture->name;
$prefecture_slug = $current_prefecture->slug;
$prefecture_description = $current_prefecture->description;
$prefecture_count = $current_prefecture->count;
$prefecture_id = $current_prefecture->term_id;

// 都道府県メタ情報取得
$prefecture_meta = get_term_meta($prefecture_id);

// 都道府県データ
$prefectures = gi_get_all_prefectures();
$current_prefecture_data = null;
$related_municipalities = [];

// 現在の都道府県の市町村を取得
foreach ($prefectures as $pref) {
    if ($pref['slug'] === $prefecture_slug) {
        $current_prefecture_data = $pref;
        if (isset($pref['municipalities']) && is_array($pref['municipalities'])) {
            $related_municipalities = $pref['municipalities'];
        }
        break;
    }
}

// 地域グループ
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

// 現在の都道府県が所属する地域を取得
$current_region = '';
$current_region_name = '';
if ($current_prefecture_data && isset($current_prefecture_data['region'])) {
    $current_region = $current_prefecture_data['region'];
    $current_region_name = isset($region_groups[$current_region]) ? $region_groups[$current_region] : '';
}

// 同じ地域の他の都道府県を取得
$same_region_prefectures = [];
foreach ($prefectures as $pref) {
    if (isset($pref['region']) && $pref['region'] === $current_region && $pref['slug'] !== $prefecture_slug) {
        $same_region_prefectures[] = $pref;
    }
}

// SEO用データ
$current_year = date('Y');
$current_month = date('n');

// ページタイトル・説明文の生成
$page_title = $prefecture_name . 'の助成金・補助金一覧【' . $current_year . '年度最新版】';
$page_description = $prefecture_description ?: 
    $prefecture_name . 'で利用できる助成金・補助金を' . number_format($prefecture_count) . '件掲載。' . 
    ($current_region_name ? $current_region_name . '地方の都道府県として、' : '') .
    '県独自の制度から国の支援まで幅広く網羅。' .
    $current_year . '年度の最新募集情報を毎日更新中。';

$canonical_url = get_term_link($current_prefecture);

// 総件数
$total_grants = wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// サイドバー用：新着トピックス（都道府県内）
$recent_grants = new WP_Query([
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'tax_query' => [
        [
            'taxonomy' => 'grant_prefecture',
            'field' => 'term_id',
            'terms' => $prefecture_id
        ]
    ]
]);

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')]
];

if ($current_region_name) {
    $breadcrumbs[] = ['name' => $current_region_name . '地方', 'url' => ''];
}

$breadcrumbs[] = ['name' => $prefecture_name, 'url' => ''];

// 構造化データ: CollectionPage
$schema_collection = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $page_title,
    'description' => $page_description,
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
        'name' => $page_title,
        'description' => $page_description,
        'numberOfItems' => $prefecture_count,
        'itemListElement' => []
    ],
    'spatialCoverage' => [
        '@type' => 'AdministrativeArea',
        'name' => $prefecture_name,
        'addressCountry' => 'JP'
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

// 構造化データ: GovernmentService
$government_service_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'GovernmentService',
    'name' => $prefecture_name . 'の助成金・補助金サービス',
    'description' => $page_description,
    'serviceType' => '助成金・補助金情報提供サービス',
    'provider' => [
        '@type' => 'GovernmentOrganization',
        'name' => $prefecture_name,
        'url' => $canonical_url
    ],
    'areaServed' => [
        '@type' => 'AdministrativeArea',
        'name' => $prefecture_name,
        'addressCountry' => 'JP'
    ],
    'availableChannel' => [
        '@type' => 'ServiceChannel',
        'serviceUrl' => $canonical_url,
        'serviceType' => 'オンライン情報提供'
    ]
];

// OGP画像
$og_image = get_site_icon_url(1200) ?: home_url('/wp-content/uploads/2025/10/1.png');

// キーワード生成
$keywords = ['助成金', '補助金', $prefecture_name, '検索', '申請', '支援制度', $current_year . '年度'];
if ($current_region_name) {
    $keywords[] = $current_region_name;
}
$keywords_string = implode(',', $keywords);
?>

<!-- 構造化データ: CollectionPage -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: GovernmentService -->
<script type="application/ld+json">
<?php echo wp_json_encode($government_service_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main class="grant-archive-yahoo-style grant-prefecture-archive" 
      id="prefecture-<?php echo esc_attr($prefecture_slug); ?>" 
      role="main"
      itemscope 
      itemtype="https://schema.org/CollectionPage">

    <!-- パンくずリスト -->
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
                    <meta itemprop="position" content="<?php echo $index + 1; ?>">
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>

    <!-- 都道府県ヒーローセクション -->
    <header class="yahoo-hero-section" 
            itemscope 
            itemtype="https://schema.org/WPHeader">
        <div class="yahoo-container">
            <div class="hero-content-wrapper">
                
                <!-- 都道府県バッジ -->
                <div class="category-badge prefecture-badge">
                    <svg class="badge-icon" 
                         width="20" 
                         height="20" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>都道府県別助成金</span>
                </div>

                <!-- メインタイトル -->
                <h1 class="yahoo-main-title" itemprop="headline">
                    <span class="category-name-highlight"><?php echo esc_html($prefecture_name); ?></span>
                    <span class="title-text">の助成金・補助金</span>
                    <span class="year-badge"><?php echo $current_year; ?>年度版</span>
                </h1>

                <!-- 都道府県説明文 -->
                <div class="yahoo-lead-section" itemprop="description">
                    <?php if ($prefecture_description): ?>
                    <div class="category-description-rich">
                        <?php echo wpautop(wp_kses_post($prefecture_description)); ?>
                    </div>
                    <?php endif; ?>
                    <p class="yahoo-lead-text">
                        <?php echo esc_html($prefecture_name); ?>で利用できる助成金・補助金を
                        <strong><?php echo number_format($prefecture_count); ?>件</strong>掲載。
                        <?php if ($current_region_name): ?>
                        <?php echo esc_html($current_region_name); ?>地方の都道府県として、県独自の制度から国の支援まで幅広く網羅。
                        <?php endif; ?>
                        <?php echo $current_year; ?>年度の最新募集情報を毎日更新しています。
                    </p>
                </div>

                <!-- メタ情報 -->
                <div class="yahoo-meta-info" role="group" aria-label="都道府県統計情報">
                    <div class="meta-item" itemscope itemtype="https://schema.org/QuantitativeValue">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M9 11H7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V11h-2v8H9v-8z"/>
                            <path d="M13 7h2l-5-5-5 5h2v4h6V7z"/>
                        </svg>
                        <strong itemprop="value"><?php echo number_format($prefecture_count); ?></strong>
                        <span itemprop="unitText">件の助成金</span>
                    </div>
                    <?php if ($current_region_name): ?>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                        地域: <span class="region-name"><?php echo esc_html($current_region_name); ?>地方</span>
                    </div>
                    <?php endif; ?>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <time datetime="<?php echo $current_year; ?>" itemprop="dateModified">
                            <?php echo $current_year; ?>年度最新情報
                        </time>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>毎日更新中</span>
                    </div>
                </div>

                <!-- 特徴カード -->
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
                            <p><?php echo esc_html($prefecture_name); ?>の最新募集情報・締切情報を毎日チェック。見逃しを防ぎます。</p>
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
                            <h3>県内全域対応</h3>
                            <p><?php echo esc_html($prefecture_name); ?>の県独自の助成金から国の制度まで網羅。市町村別の検索も可能です。</p>
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
                            <p>申請方法から採択のコツまで、専門家監修の情報を提供。初めての方でも安心して申請できます。</p>
                        </div>
                    </article>
                </div>

                <!-- 関連市町村 -->
                <?php if (!empty($related_municipalities)): ?>
                <div class="related-areas-section">
                    <h2 class="related-areas-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                        </svg>
                        <?php echo esc_html($prefecture_name); ?>の市町村
                    </h2>
                    <div class="related-areas-grid">
                        <?php $displayed = 0; foreach ($related_municipalities as $municipality): 
                            if ($displayed >= 8) break;
                            $displayed++;
                        ?>
                        <a href="<?php echo esc_url(get_term_link($municipality['slug'], 'grant_municipality')); ?>" 
                           class="related-area-card municipality-card"
                           title="<?php echo esc_attr($municipality['name']); ?>の助成金を見る">
                            <div class="card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                                </svg>
                            </div>
                            <span class="card-name"><?php echo esc_html($municipality['name']); ?></span>
                            <span class="card-label">市町村</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($related_municipalities) > 8): ?>
                    <p class="more-areas-link">
                        <a href="#municipality-filter">全<?php echo count($related_municipalities); ?>市町村から絞り込む →</a>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- 同地域の他の都道府県 -->
                <?php if (!empty($same_region_prefectures)): ?>
                <div class="related-areas-section same-region-section">
                    <h2 class="related-areas-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <?php echo esc_html($current_region_name); ?>地方の他の都道府県
                    </h2>
                    <div class="related-areas-grid">
                        <?php $displayed = 0; foreach ($same_region_prefectures as $pref): 
                            if ($displayed >= 6) break;
                            $displayed++;
                        ?>
                        <a href="<?php echo esc_url(get_term_link($pref['slug'], 'grant_prefecture')); ?>" 
                           class="related-area-card prefecture-card"
                           title="<?php echo esc_attr($pref['name']); ?>の助成金を見る">
                            <div class="card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <span class="card-name"><?php echo esc_html($pref['name']); ?></span>
                            <span class="card-label">都道府県</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
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
                        <input type="search" 
                               id="keyword-search" 
                               class="search-input" 
                               placeholder="助成金名、実施機関、対象事業で検索..."
                               data-prefecture="<?php echo esc_attr($prefecture_slug); ?>"
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

            <!-- モバイル用フローティングフィルターボタン -->
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

            <!-- フィルターパネル背景オーバーレイ -->
            <div class="filter-panel-overlay" id="filter-panel-overlay"></div>

            <!-- プルダウン式フィルターセクション（都道府県固定） -->
            <section class="yahoo-filter-section" id="filter-panel" 
                     role="search" 
                     aria-label="助成金検索フィルター">
                
                <!-- フィルターヘッダー -->
                <div class="filter-header">
                    <h2 class="filter-title">
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

                <!-- プルダウンフィルターグリッド（都道府県固定、市町村選択可能） -->
                <div class="yahoo-filters-grid">
                    
                    <!-- 市町村フィルター（都道府県内のみ） -->
                    <?php if (!empty($related_municipalities)): ?>
                    <div class="filter-dropdown-wrapper" id="municipality-filter">
                        <label class="filter-label" id="municipality-label">市町村</label>
                        <div class="custom-select custom-multi-select" 
                             id="municipality-select" 
                             role="combobox" 
                             aria-labelledby="municipality-label" 
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
                                <div class="select-search-container">
                                    <input type="text" 
                                           class="select-search-input" 
                                           placeholder="市町村名で検索..."
                                           aria-label="市町村を検索">
                                </div>
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option">指定なし</div>
                                <?php foreach ($related_municipalities as $municipality): ?>
                                <div class="select-option" 
                                     data-value="<?php echo esc_attr($municipality['slug']); ?>" 
                                     role="option"><?php echo esc_html($municipality['name']); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- カテゴリフィルター -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="category-label">カテゴリ</label>
                        <div class="custom-select custom-multi-select" 
                             id="category-select" 
                             role="combobox" 
                             aria-labelledby="category-label" 
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
                                <div class="select-search-container">
                                    <input type="text" 
                                           class="select-search-input" 
                                           placeholder="カテゴリ名で検索..."
                                           aria-label="カテゴリを検索">
                                </div>
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option">指定なし</div>
                                <?php
                                $categories = get_terms([
                                    'taxonomy' => 'grant_category',
                                    'hide_empty' => true,
                                    'orderby' => 'count',
                                    'order' => 'DESC'
                                ]);
                                if (!is_wp_error($categories) && !empty($categories)):
                                    foreach ($categories as $category):
                                ?>
                                <div class="select-option" 
                                     data-value="<?php echo esc_attr($category->slug); ?>" 
                                     role="option"><?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)</div>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
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
                                     role="option">100〜500万円</div>
                                <div class="select-option" 
                                     data-value="500-1000" 
                                     role="option">500万〜1000万円</div>
                                <div class="select-option" 
                                     data-value="1000-5000" 
                                     role="option">1000万〜5000万円</div>
                                <div class="select-option" 
                                     data-value="5000+" 
                                     role="option">5000万円以上</div>
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
                                     data-value="open" 
                                     role="option">募集中</div>
                                <div class="select-option" 
                                     data-value="upcoming" 
                                     role="option">募集予定</div>
                                <div class="select-option" 
                                     data-value="deadline-soon" 
                                     role="option">締切間近（7日以内）</div>
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
                                     data-value="date" 
                                     role="option">新着順</div>
                                <div class="select-option" 
                                     data-value="deadline" 
                                     role="option">締切日が近い順</div>
                                <div class="select-option" 
                                     data-value="amount-desc" 
                                     role="option">助成金額が高い順</div>
                                <div class="select-option" 
                                     data-value="popularity" 
                                     role="option">人気順</div>
                            </div>
                        </div>
                    </div>
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

            <!-- 選択中のフィルタータグ表示 -->
            <div class="active-filters-section" id="active-filters" style="display: none;">
                <div class="active-filters-header">
                    <span class="active-filters-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        選択中の条件:
                    </span>
                </div>
                <div class="active-filters-tags" id="active-filters-tags">
                    <!-- JavaScriptで動的に追加 -->
                </div>
            </div>

            <!-- 結果カウント・統計 -->
            <div class="results-header">
                <div class="results-count-section">
                    <span class="results-count" id="results-count" aria-live="polite">
                        <?php echo esc_html($prefecture_name); ?>の助成金・補助金を読み込み中...
                    </span>
                </div>
            </div>

            <!-- 助成金一覧 -->
            <div class="grants-container-yahoo" 
                 id="grants-container" 
                 data-view="single">
                <?php
                // 初期表示用WP_Query（都道府県固定）
                $initial_query = new WP_Query([
                    'post_type' => 'grant',
                    'posts_per_page' => 12,
                    'post_status' => 'publish',
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                    'tax_query' => [
                        [
                            'taxonomy' => 'grant_prefecture',
                            'field' => 'term_id',
                            'terms' => $prefecture_id
                        ]
                    ],
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
                
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

            <!-- ページネーション -->
            <div class="pagination-wrapper" 
                 id="pagination-wrapper">
                <?php
                if (isset($initial_query) && $initial_query->max_num_pages > 1) {
                    $big = 999999999;
                    
                    // すべての現在のクエリパラメータを保持
                    $preserved_params = array();
                    foreach ($_GET as $key => $value) {
                        if (!empty($value) && $key !== 'paged') {
                            $preserved_params[$key] = sanitize_text_field($value);
                        }
                    }
                    
                    // ベースURLにクエリパラメータを追加
                    $base_url = add_query_arg($preserved_params, str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ));
                    
                    echo paginate_links( array(
                        'base' => $base_url,
                        'format' => '&paged=%#%',
                        'current' => max( 1, get_query_var('paged') ),
                        'total' => $initial_query->max_num_pages,
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
        </div>

        <!-- サイドバー (PCのみ) -->
        <aside class="yahoo-sidebar" role="complementary" aria-label="サイドバー">
            
            <!-- 広告枠1 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-top">
                <?php ji_display_ad('prefecture_grant_sidebar_top', 'taxonomy-grant_prefecture'); ?>
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
                    <?php echo esc_html($prefecture_name); ?>人気ランキング
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

            <!-- 広告枠2 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-middle">
                <?php ji_display_ad('prefecture_grant_sidebar_middle', 'taxonomy-grant_prefecture'); ?>
            </div>
            <?php endif; ?>

            <!-- 新着トピックス -->
            <section class="sidebar-widget sidebar-topics-widget">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <?php echo esc_html($prefecture_name); ?>新着
                </h3>
                <div class="widget-content">
                    <?php if ($recent_grants->have_posts()): ?>
                        <ul class="topics-list">
                            <?php while ($recent_grants->have_posts()): $recent_grants->the_post(); ?>
                            <li class="topic-item">
                                <a href="<?php the_permalink(); ?>" class="topic-link" title="<?php the_title_attribute(); ?>">
                                    <span class="topic-date"><?php echo get_the_date('m/d'); ?></span>
                                    <span class="topic-title"><?php the_title(); ?></span>
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
                <?php ji_display_ad('prefecture_grant_sidebar_bottom', 'taxonomy-grant_prefecture'); ?>
            </div>
            <?php endif; ?>

            <!-- 市町村一覧（サイドバー版） -->
            <?php if (!empty($related_municipalities)): ?>
            <section class="sidebar-widget sidebar-municipalities-widget">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                    </svg>
                    <?php echo esc_html($prefecture_name); ?>の市町村
                </h3>
                <div class="widget-content">
                    <ul class="municipalities-list">
                        <?php $displayed = 0; foreach ($related_municipalities as $municipality): 
                            if ($displayed >= 10) break;
                            $displayed++;
                        ?>
                        <li class="municipality-item">
                            <a href="<?php echo esc_url(get_term_link($municipality['slug'], 'grant_municipality')); ?>" class="municipality-link">
                                <?php echo esc_html($municipality['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($related_municipalities) > 10): ?>
                    <p class="more-link">
                        <a href="#municipality-filter">全<?php echo count($related_municipalities); ?>市町村を見る →</a>
                    </p>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- 同地域の都道府県（サイドバー版） -->
            <?php if (!empty($same_region_prefectures)): ?>
            <section class="sidebar-widget sidebar-related-prefectures">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <?php echo esc_html($current_region_name); ?>地方
                </h3>
                <div class="widget-content">
                    <ul class="prefectures-list">
                        <?php foreach ($same_region_prefectures as $pref): ?>
                        <li class="prefecture-item">
                            <a href="<?php echo esc_url(get_term_link($pref['slug'], 'grant_prefecture')); ?>" class="prefecture-link">
                                <?php echo esc_html($pref['name']); ?>
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
            fixedCategory: '',
            fixedPrefecture: '<?php echo esc_js($prefecture_slug ?? ""); ?>',
            fixedMunicipality: '',
            fixedPurpose: '',
            fixedTag: ''
        });
    }
});
</script>

<?php get_footer(); ?>
