<?php
/**
 * Municipality Archive Template for Grant - Yahoo! JAPAN Inspired SEO Perfect Edition
 * 市町村別助成金・補助金アーカイブページ - Yahoo!風デザイン・SEO完全最適化版
 * 
 * @package Grant_Insight_Perfect
 * @version 19.0.0 - Municipality Specialized with Yahoo! JAPAN Style
 * 
 * === Features ===
 * - Based on archive-grant.php structure
 * - Municipality-fixed filter
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

// 現在の市町村情報を取得
$current_municipality = get_queried_object();
$municipality_name = $current_municipality->name;
$municipality_slug = $current_municipality->slug;
$municipality_description = $current_municipality->description;
$municipality_count = $current_municipality->count;
$municipality_id = $current_municipality->term_id;

// 市町村メタ情報取得
$municipality_meta = get_term_meta($municipality_id);

// 都道府県データ
$prefectures = gi_get_all_prefectures();
$parent_prefecture = null;
$related_municipalities = [];

// 現在の市町村の都道府県を特定
foreach ($prefectures as $pref) {
    if (isset($pref['municipalities']) && is_array($pref['municipalities'])) {
        foreach ($pref['municipalities'] as $municipality) {
            if ($municipality['slug'] === $municipality_slug) {
                $parent_prefecture = $pref;
                $related_municipalities = array_filter($pref['municipalities'], function($m) use ($municipality_slug) {
                    return $m['slug'] !== $municipality_slug;
                });
                break 2;
            }
        }
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

// SEO用データ
$current_year = date('Y');
$current_month = date('n');

// ページタイトル・説明文の生成（SEO中キーワード対策）
$page_title = $municipality_name . '補助金一覧【' . $current_year . '年度最新版】全' . number_format($municipality_count) . '件';
$page_title_h1 = $municipality_name . 'の補助金・助成金 完全ガイド';
$page_description = $municipality_description ?: 
    $municipality_name . 'の補助金・助成金を' . number_format($municipality_count) . '件掲載。' . 
    ($parent_prefecture ? $parent_prefecture['name'] . 'の制度と合わせて活用可能。' : '') .
    $current_year . '年度の最新募集情報を毎日更新。' .
    '新着補助金、締切間近の助成金、金額帯別など多彩な検索が可能。';

// SEO用キャッチコピー
$seo_catchphrase = $municipality_name . '補助金一覧 | ' . number_format($municipality_count) . '件の助成金情報を完全収録';

$canonical_url = get_term_link($current_municipality);

// 総件数
$total_grants = wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// サイドバー用：新着トピックス（市町村内）
$recent_grants = new WP_Query([
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'tax_query' => [
        [
            'taxonomy' => 'grant_municipality',
            'field' => 'term_id',
            'terms' => $municipality_id
        ]
    ]
]);

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')]
];

if ($parent_prefecture) {
    $breadcrumbs[] = ['name' => $parent_prefecture['name'], 'url' => get_term_link($parent_prefecture['slug'], 'grant_prefecture')];
}

$breadcrumbs[] = ['name' => $municipality_name, 'url' => ''];

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
        'numberOfItems' => $municipality_count,
        'itemListElement' => []
    ]
];

if ($parent_prefecture) {
    $schema_collection['spatialCoverage'] = [
        '@type' => 'City',
        'name' => $municipality_name,
        'containedInPlace' => [
            '@type' => 'AdministrativeArea',
            'name' => $parent_prefecture['name'],
            'addressCountry' => 'JP'
        ]
    ];
}

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
    'name' => $municipality_name . 'の助成金・補助金サービス',
    'description' => $page_description,
    'serviceType' => '助成金・補助金情報提供サービス',
    'provider' => [
        '@type' => 'GovernmentOrganization',
        'name' => $municipality_name,
        'url' => $canonical_url
    ],
    'areaServed' => [
        '@type' => 'City',
        'name' => $municipality_name,
        'addressCountry' => 'JP'
    ],
    'availableChannel' => [
        '@type' => 'ServiceChannel',
        'serviceUrl' => $canonical_url,
        'serviceType' => 'オンライン情報提供'
    ]
];

if ($parent_prefecture) {
    $government_service_schema['areaServed']['containedInPlace'] = [
        '@type' => 'AdministrativeArea',
        'name' => $parent_prefecture['name'],
        'addressCountry' => 'JP'
    ];
}

// OGP画像
$og_image = get_site_icon_url(1200) ?: home_url('/wp-content/uploads/2025/10/1.png');

// キーワード生成
$keywords = ['助成金', '補助金', $municipality_name, '検索', '申請', '支援制度', $current_year . '年度'];
if ($parent_prefecture) {
    $keywords[] = $parent_prefecture['name'];
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

<main class="grant-archive-yahoo-style grant-municipality-archive" 
      id="municipality-<?php echo esc_attr($municipality_slug); ?>" 
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

    <!-- 市町村ヒーローセクション（図鑑式・横長レイアウト） -->
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
                                <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                            </svg>
                            <span><?php echo esc_html($parent_prefecture ? $parent_prefecture['name'] : '市町村'); ?></span>
                        </div>
                        <h1 class="hero-title-encyclopedia" itemprop="headline">
                            <span class="hero-title-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                                </svg>
                            </span>
                            <?php echo esc_html($municipality_name); ?>の補助金・助成金
                        </h1>
                        <p class="hero-subtitle">
                            <?php echo $current_year; ?>年度の最新情報を毎日更新。<?php if ($parent_prefecture): ?><?php echo esc_html($parent_prefecture['name']); ?>の制度と合わせて活用可能。<?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- 中央：統計情報 -->
                    <div class="hero-stats-area">
                        <div class="hero-stat-card" itemscope itemtype="https://schema.org/QuantitativeValue">
                            <span class="hero-stat-number" itemprop="value"><?php echo number_format($municipality_count); ?></span>
                            <span class="hero-stat-label" itemprop="unitText">件の助成金</span>
                        </div>
                        <div class="hero-stat-card">
                            <span class="hero-stat-number"><?php echo $current_year; ?></span>
                            <span class="hero-stat-label">年度版</span>
                        </div>
                    </div>
                    
                    <!-- 右側：クイックリンク -->
                    <div class="hero-action-area">
                        <div class="hero-quick-links">
                            <?php if ($parent_prefecture): ?>
                            <a href="<?php echo esc_url(get_term_link($parent_prefecture['slug'], 'grant_prefecture')); ?>" class="hero-quick-link">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <?php echo esc_html($parent_prefecture['name']); ?>全体
                            </a>
                            <?php endif; ?>
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
                               data-municipality="<?php echo esc_attr($municipality_slug); ?>"
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

            <!-- モバイル用フィルター開閉ボタン (archive-grant.php と統一) -->
            <button class="mobile-filter-toggle" id="mobile-filter-toggle" type="button" aria-label="フィルターを開く">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span>絞り込み</span>
            </button>

            <!-- フィルターパネル背景オーバーレイ -->
            <div class="filter-panel-overlay" id="filter-panel-overlay"></div>

            <!-- プルダウン式フィルターセクション（市町村固定） -->
            <section class="yahoo-filter-section" id="filter-panel" 
                     role="search" 
                     aria-label="助成金検索フィルター">
                
                <!-- フィルターヘッダー -->
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

                <!-- プルダウンフィルターグリッド（市町村選択を除外） -->
                <div class="yahoo-filters-grid">
                    
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
                
                <!-- 結果ヘッダー -->
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

                <!-- 助成金表示エリア -->
                <div class="grants-container-yahoo" 
                     id="grants-container" 
                     data-view="single">
                    <?php
                    // 初期表示用WP_Query（市町村固定）
                    $initial_query = new WP_Query([
                        'post_type' => 'grant',
                        'posts_per_page' => 12,
                        'post_status' => 'publish',
                        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                        'tax_query' => [
                            [
                                'taxonomy' => 'grant_municipality',
                                'field' => 'term_id',
                                'terms' => $municipality_id
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
            </section>
        </div>

        <!-- サイドバー（PC only） -->
        <aside class="yahoo-sidebar" role="complementary" aria-label="サイドバー">
            
            <!-- 広告枠1: サイドバー上部 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-top">
                <?php ji_display_ad('municipality_grant_sidebar_top', 'taxonomy-grant_municipality'); ?>
            </div>
            <?php endif; ?>

            <!-- 広告枠2: サイドバー中央 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-middle">
                <?php ji_display_ad('municipality_grant_sidebar_middle', 'taxonomy-grant_municipality'); ?>
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

            <!-- 新着トピックス（市町村内） -->
            <section class="sidebar-widget sidebar-topics">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/>
                        <line x1="10" y1="1" x2="10" y2="4"/>
                        <line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                    <?php echo esc_html($municipality_name); ?>の新着トピックス
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
                <?php ji_display_ad('municipality_grant_sidebar_bottom', 'taxonomy-grant_municipality'); ?>
            </div>
            <?php endif; ?>

            <!-- 関連地域（サイドバー版） -->
            <?php if ($parent_prefecture || !empty($related_municipalities)): ?>
            <section class="sidebar-widget sidebar-related-areas">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    関連する地域
                </h3>
                <div class="widget-content">
                    <ul class="related-areas-list">
                        <?php if ($parent_prefecture): ?>
                        <li class="related-area-item prefecture-item">
                            <a href="<?php echo esc_url(get_term_link($parent_prefecture['slug'], 'grant_prefecture')); ?>" class="related-area-link">
                                <?php echo esc_html($parent_prefecture['name']); ?>
                                <span class="area-label">都道府県</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($related_municipalities)): ?>
                        <?php $displayed = 0; foreach ($related_municipalities as $municipality): 
                            if ($displayed >= 5) break;
                            $displayed++;
                        ?>
                        <li class="related-area-item">
                            <a href="<?php echo esc_url(get_term_link($municipality['slug'], 'grant_municipality')); ?>" class="related-area-link">
                                <?php echo esc_html($municipality['name']); ?>
                                <span class="area-label">近隣</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
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
            fixedPrefecture: '',
            fixedMunicipality: '<?php echo esc_js($municipality_slug ?? ""); ?>',
            fixedPurpose: '',
            fixedTag: ''
        });
    }
});
</script>

<?php get_footer(); ?>
