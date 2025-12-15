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
$current_category = get_queried_object();
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
$canonical_url = $current_url;

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

<!-- 構造化データ: CollectionPage -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main class="grant-archive-yahoo-style" 
      id="grant-archive" 
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

    <!-- ヒーローセクション -->
    <header class="yahoo-hero-section" 
            itemscope 
            itemtype="https://schema.org/WPHeader">
        <div class="yahoo-container">
            <div class="hero-content-wrapper">
                
                <!-- カテゴリーバッジ -->
                <div class="category-badge" role="status">
                    <svg class="badge-icon" 
                         width="16" 
                         height="16" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <span>助成金・補助金検索</span>
                </div>

                <!-- メインタイトル -->
                <h1 class="yahoo-main-title" itemprop="headline">
                    <?php echo esc_html($archive_title); ?>
                </h1>

                <!-- リード文 -->
                <p class="yahoo-lead-text" itemprop="description">
                    <?php echo esc_html($archive_description); ?>
                </p>

                <!-- メタ情報 -->
                <div class="yahoo-meta-info" role="group" aria-label="統計情報">
                    <div class="meta-item" itemscope itemtype="https://schema.org/QuantitativeValue">
                        <svg class="meta-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M9 11H7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V11h-2v8H9v-8z"/>
                            <path d="M13 7h2l-5-5-5 5h2v4h6V7z"/>
                        </svg>
                        <strong itemprop="value"><?php echo $total_grants_formatted; ?></strong>
                        <span itemprop="unitText">件</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <time datetime="<?php echo $current_year; ?>" itemprop="dateModified">
                            <?php echo $current_year; ?>年度版
                        </time>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="16" 
                             height="16" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>毎日更新</span>
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
                        <input type="search" 
                               id="keyword-search" 
                               class="search-input" 
                               placeholder="助成金名、実施機関、対象事業で検索..."
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
                        while ($initial_grants_query->have_posts()) : 
                            $initial_grants_query->the_post();
                            include(get_template_directory() . '/template-parts/grant-card-unified.php');
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
            
            <?php
            // デバッグ: 関数の存在確認
            error_log('🟣 archive-grant.php: ji_display_ad exists: ' . (function_exists('ji_display_ad') ? 'YES' : 'NO'));
            error_log('🟣 archive-grant.php: JI_Affiliate_Ad_Manager class exists: ' . (class_exists('JI_Affiliate_Ad_Manager') ? 'YES' : 'NO'));
            ?>
            
            <!-- 広告枠1: サイドバー上部 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-top">
                <?php ji_display_ad('archive_grant_sidebar_top', 'archive-grant'); ?>
            </div>
            <?php else: ?>
            <!-- デバッグ: ji_display_ad関数が存在しません -->
            <?php error_log('🔴 archive-grant.php: ji_display_ad function NOT FOUND at sidebar top'); ?>
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

<!-- Yahoo!風デザインCSS -->
<style>

/* ==========================================================================
   Grant Archive - Government Official Design v55.0
   助成金・補助金アーカイブページ - 官公庁風デザイン（濃紺×金カラースキーム）
   既存HTML構造（v19.0）対応版
   ========================================================================== */

/* ==========================================================================
   CSS Variables - 官公庁カラーパレット
   ========================================================================== */
:root {
    /* 官公庁カラーパレット - メイン */
    --gov-navy-900: #0d1b2a;
    --gov-navy-800: #1b263b;
    --gov-navy-700: #2c3e50;
    --gov-navy-600: #34495e;
    --gov-navy-500: #415a77;
    --gov-navy-400: #778da9;
    --gov-navy-300: #a3b1c6;
    --gov-navy-200: #cfd8e3;
    --gov-navy-100: #e8ecf1;
    --gov-navy-50: #f4f6f8;
    
    /* アクセントカラー - 金 */
    --gov-gold: #c9a227;
    --gov-gold-light: #d4b77a;
    --gov-gold-pale: #f0e6c8;
    
    /* セマンティックカラー */
    --gov-green: #2e7d32;
    --gov-green-light: #e8f5e9;
    --gov-red: #c62828;
    --gov-red-light: #ffebee;
    --gov-blue: #1565c0;
    --gov-blue-light: #e3f2fd;
    
    /* ニュートラル */
    --gov-white: #ffffff;
    --gov-black: #1a1a1a;
    --gov-gray-900: #212529;
    --gov-gray-800: #343a40;
    --gov-gray-700: #495057;
    --gov-gray-600: #6c757d;
    --gov-gray-500: #adb5bd;
    --gov-gray-400: #ced4da;
    --gov-gray-300: #dee2e6;
    --gov-gray-200: #e9ecef;
    --gov-gray-100: #f8f9fa;
    
    /* タイポグラフィ */
    --gov-font-serif: "Shippori Mincho", "Yu Mincho", "YuMincho", "Hiragino Mincho ProN", serif;
    --gov-font-sans: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --gov-font-mono: 'SF Mono', 'Monaco', 'Cascadia Code', monospace;
    
    /* エフェクト */
    --gov-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    --gov-transition-slow: 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --gov-shadow-xl: 0 20px 50px rgba(0, 0, 0, 0.15);
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
}

/* ==========================================================================
   Base Styles
   ========================================================================== */
.grant-archive-yahoo-style {
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    line-height: 1.7;
    position: relative;
}

/* 上部アクセントライン（官公庁風） */
.grant-archive-yahoo-style::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gov-navy-800) 0%, var(--gov-gold) 50%, var(--gov-navy-800) 100%);
    z-index: 9999;
}

.yahoo-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ==========================================================================
   Breadcrumb - パンくずリスト
   ========================================================================== */
.breadcrumb-nav {
    padding: 16px 0;
    background: var(--gov-white);
    border-bottom: 1px solid var(--gov-gray-200);
}

.breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 13px;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin-left: 8px;
    color: var(--gov-gray-400);
    font-weight: 300;
}

.breadcrumb-item a {
    color: var(--gov-navy-600);
    text-decoration: none;
    transition: color var(--gov-transition);
}

.breadcrumb-item a:hover {
    color: var(--gov-navy-900);
    text-decoration: underline;
}

.breadcrumb-item span {
    color: var(--gov-navy-900);
    font-weight: 600;
}

/* ==========================================================================
   Hero Section - ヒーローセクション
   ========================================================================== */
.yahoo-hero-section {
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    color: var(--gov-white);
    padding: 48px 0;
    border-bottom: 4px solid var(--gov-gold);
    position: relative;
    overflow: hidden;
}

/* 背景パターン */
.yahoo-hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}

.hero-content-wrapper {
    position: relative;
    z-index: 1;
    max-width: 800px;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(201, 162, 39, 0.15);
    border: 1px solid var(--gov-gold);
    border-radius: var(--gov-radius);
    font-size: 12px;
    font-weight: 700;
    color: var(--gov-gold);
    letter-spacing: 0.1em;
    margin-bottom: 20px;
    text-transform: uppercase;
}

.category-badge .badge-icon {
    color: var(--gov-gold);
}

.yahoo-main-title {
    font-family: var(--gov-font-serif);
    font-size: clamp(28px, 5vw, 44px);
    font-weight: 700;
    color: var(--gov-white);
    margin: 0 0 16px 0;
    line-height: 1.3;
    letter-spacing: 0.02em;
}

.yahoo-lead-text {
    font-size: 15px;
    color: var(--gov-navy-200);
    margin: 0 0 24px 0;
    line-height: 1.9;
    max-width: 700px;
}

.yahoo-meta-info {
    display: flex;
    align-items: center;
    gap: 28px;
    flex-wrap: wrap;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--gov-navy-200);
}

.meta-icon {
    color: var(--gov-gold);
}

.meta-item strong {
    font-family: var(--gov-font-mono);
    font-size: 28px;
    font-weight: 900;
    color: var(--gov-white);
    line-height: 1;
}

/* ==========================================================================
   Two Column Layout - 2カラムレイアウト
   ========================================================================== */
.yahoo-two-column-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 32px;
    padding: 32px 24px 80px;
    align-items: start;
}

.yahoo-main-content {
    min-width: 0;
}

.yahoo-sidebar {
    position: sticky;
    top: 24px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* ==========================================================================
   Search Section - 検索セクション
   ========================================================================== */
.yahoo-search-section {
    margin-bottom: 24px;
}

.search-bar-wrapper {
    background: var(--gov-white);
    padding: 20px;
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    box-shadow: var(--gov-shadow-sm);
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--gov-white);
    border: 2px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    overflow: hidden;
    transition: border-color var(--gov-transition), box-shadow var(--gov-transition);
}

.search-input-container:focus-within {
    border-color: var(--gov-navy-700);
    box-shadow: 0 0 0 3px rgba(27, 38, 59, 0.1);
}

.search-icon {
    position: absolute;
    left: 16px;
    color: var(--gov-gray-500);
    pointer-events: none;
}

.search-input {
    flex: 1;
    padding: 14px 16px 14px 48px;
    border: none;
    outline: none;
    font-family: var(--gov-font-sans);
    font-size: 15px;
    color: var(--gov-gray-900);
    background: transparent;
}

.search-input::placeholder {
    color: var(--gov-gray-500);
}

.search-clear-btn {
    background: none;
    border: none;
    color: var(--gov-gray-400);
    padding: 8px;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
    transition: color var(--gov-transition);
}

.search-clear-btn:hover {
    color: var(--gov-gray-700);
}

.search-execute-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    border: none;
    color: var(--gov-white);
    font-family: var(--gov-font-sans);
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--gov-transition);
}

.search-execute-btn:hover {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-800) 100%);
}

.search-execute-btn:focus-visible {
    outline: 3px solid var(--gov-gold);
    outline-offset: 2px;
}

/* ==========================================================================
   Filter Section - フィルターセクション
   ========================================================================== */
.yahoo-filter-section {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    margin-bottom: 24px;
    box-shadow: var(--gov-shadow-sm);
    overflow: visible; /* UX改善: hidden → visible（ドロップダウンが領域外に出ても表示される） */
    position: relative; /* ドロップダウンの基準位置 */
    z-index: 100; /* フィルターセクション自体を上位に */
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: var(--gov-navy-50);
    border-bottom: 2px solid var(--gov-navy-800);
    flex-wrap: wrap;
    gap: 12px;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: var(--gov-font-serif);
    font-size: 16px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
}

.filter-title .title-icon {
    color: var(--gov-navy-600);
}

.filter-reset-all {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    font-family: var(--gov-font-sans);
    font-size: 12px;
    font-weight: 600;
    color: var(--gov-gray-700);
    cursor: pointer;
    transition: all var(--gov-transition);
}

.filter-reset-all:hover {
    background: var(--gov-navy-50);
    border-color: var(--gov-navy-400);
    color: var(--gov-navy-800);
}

.yahoo-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* UX改善: 160px → 180px */
    gap: 20px; /* UX改善: 16px → 20px */
    padding: 24px;
    overflow: visible; /* ドロップダウン表示のため */
}

.filter-dropdown-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--gov-navy-700);
    letter-spacing: 0.02em;
}

.multi-select-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--gov-navy-800);
    color: var(--gov-white);
    font-size: 10px;
    font-weight: 700;
    border-radius: 10px;
}

.selected-prefecture-name {
    font-size: 11px;
    color: var(--gov-gray-500);
    font-weight: 400;
}

/* ==========================================================================
   Custom Select - カスタムセレクト
   ========================================================================== */
.custom-select {
    position: relative;
    width: 100%;
}

.select-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    font-family: var(--gov-font-sans);
    font-size: 14px;
    color: var(--gov-gray-700);
    cursor: pointer;
    text-align: left;
    transition: all var(--gov-transition);
}

.select-trigger:hover {
    border-color: var(--gov-navy-400);
}

.custom-select.active .select-trigger {
    border-color: var(--gov-navy-700);
    box-shadow: 0 0 0 3px rgba(27, 38, 59, 0.1);
}

.select-value {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.select-arrow {
    flex-shrink: 0;
    color: var(--gov-gray-500);
    transition: transform var(--gov-transition);
}

.custom-select.active .select-arrow {
    transform: rotate(180deg);
}

.select-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    box-shadow: var(--gov-shadow-lg);
    max-height: 400px; /* UX改善: 300px → 400px */
    overflow-y: auto;
    z-index: 9990; /* UX改善: 100 → 9990（他の要素に隠れないように） */
}

.select-option {
    padding: 12px 14px;
    cursor: pointer;
    font-size: 14px;
    color: var(--gov-gray-700);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all var(--gov-transition);
}

.select-option:hover {
    background: var(--gov-navy-50);
}

.select-option.active {
    background: var(--gov-navy-100);
    color: var(--gov-navy-900);
    font-weight: 600;
}

/* Multi Select */
.multi-select-dropdown {
    max-height: 450px; /* UX改善: 380px → 450px */
}

.select-search-wrapper {
    padding: 12px;
    border-bottom: 1px solid var(--gov-gray-200);
    position: sticky;
    top: 0;
    background: var(--gov-white);
    z-index: 1;
}

.select-search-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--gov-gray-300);
    border-radius: var(--gov-radius);
    font-family: var(--gov-font-sans);
    font-size: 13px;
    outline: none;
    transition: border-color var(--gov-transition);
}

.select-search-input:focus {
    border-color: var(--gov-navy-700);
}

.select-options-wrapper {
    max-height: 320px; /* UX改善: 240px → 320px */
    overflow-y: auto;
}

.select-option .option-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--gov-navy-800);
}

.select-option label {
    flex: 1;
    cursor: pointer;
}

.select-actions {
    display: flex;
    gap: 8px;
    padding: 12px;
    border-top: 1px solid var(--gov-gray-200);
    position: sticky;
    bottom: 0;
    background: var(--gov-gray-50);
}

.select-action-btn {
    flex: 1;
    padding: 10px 14px;
    border-radius: var(--gov-radius);
    font-family: var(--gov-font-sans);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--gov-transition);
}

.select-action-btn.clear-btn {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-300);
    color: var(--gov-gray-700);
}

.select-action-btn.clear-btn:hover {
    background: var(--gov-gray-100);
}

.select-action-btn.apply-btn {
    background: var(--gov-navy-800);
    border: 1px solid var(--gov-navy-800);
    color: var(--gov-white);
}

.select-action-btn.apply-btn:hover {
    background: var(--gov-navy-900);
}

/* ==========================================================================
   Active Filters - 選択中のフィルター
   ========================================================================== */
.active-filters-display {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 24px;
    background: var(--gov-gold-pale);
    border-top: 1px solid var(--gov-gold-light);
    flex-wrap: wrap;
}

.active-filters-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--gov-navy-800);
}

.active-filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex: 1;
}

.filter-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--gov-navy-800);
    color: var(--gov-white);
    font-size: 12px;
    font-weight: 500;
    border-radius: var(--gov-radius);
}

.filter-tag-remove {
    background: none;
    border: none;
    color: var(--gov-white);
    cursor: pointer;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    line-height: 1;
    opacity: 0.7;
    transition: opacity var(--gov-transition);
}

.filter-tag-remove:hover {
    opacity: 1;
}

/* ==========================================================================
   Results Section - 検索結果セクション
   ========================================================================== */
.yahoo-results-section {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    box-shadow: var(--gov-shadow-sm);
    overflow: hidden;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    background: var(--gov-navy-50);
    border-bottom: 2px solid var(--gov-navy-800);
    flex-wrap: wrap;
    gap: 16px;
}

.results-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.results-title {
    font-family: var(--gov-font-serif);
    font-size: 18px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
}

.results-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: var(--gov-gray-600);
}

.total-count strong {
    font-family: var(--gov-font-mono);
    font-size: 20px;
    font-weight: 900;
    color: var(--gov-navy-900);
}

/* View Controls - 表示切替 */
.view-controls {
    display: flex;
    gap: 4px;
    background: var(--gov-white);
    padding: 4px;
    border-radius: var(--gov-radius);
    border: 1px solid var(--gov-gray-300);
}

.view-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 14px;
    background: transparent;
    border: none;
    border-radius: var(--gov-radius);
    cursor: pointer;
    color: var(--gov-gray-600);
    transition: all var(--gov-transition);
}

.view-btn:hover {
    color: var(--gov-navy-800);
    background: var(--gov-navy-50);
}

.view-btn.active {
    background: var(--gov-navy-800);
    color: var(--gov-white);
}

.view-btn svg {
    width: 18px;
    height: 18px;
}

/* ==========================================================================
   Grants Container - 助成金表示エリア
   ========================================================================== */
.grants-container-yahoo {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 24px;
    min-height: 400px;
}

.grants-container-yahoo[data-view="grid"] {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.grants-container-yahoo[data-view="compact"] {
    gap: 0;
}

/* ==========================================================================
   Loading - ローディング
   ========================================================================== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--gov-gray-200);
    border-top-color: var(--gov-navy-800);
    border-radius: 50%;
    animation: govSpin 0.8s linear infinite;
    margin: 0 auto 16px;
}

@keyframes govSpin {
    to { transform: rotate(360deg); }
}

.loading-text {
    font-size: 14px;
    color: var(--gov-gray-600);
    font-weight: 600;
    margin: 0;
}

/* ==========================================================================
   No Results - 結果なし
   ========================================================================== */
.no-results {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 24px;
    text-align: center;
}

.no-results-icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gov-navy-50);
    border-radius: 50%;
    color: var(--gov-navy-300);
    margin-bottom: 24px;
}

.no-results-title {
    font-family: var(--gov-font-serif);
    font-size: 20px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0 0 12px 0;
}

.no-results-message {
    font-size: 14px;
    color: var(--gov-gray-600);
    margin: 0;
}

/* ==========================================================================
   Pagination - ページネーション
   ========================================================================== */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    padding: 24px;
    border-top: 1px solid var(--gov-gray-200);
    background: var(--gov-navy-50);
}

.pagination-wrapper .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 14px;
    border: 1px solid var(--gov-gray-300);
    background: var(--gov-white);
    border-radius: var(--gov-radius);
    color: var(--gov-gray-700);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--gov-transition);
}

.pagination-wrapper .page-numbers:hover {
    border-color: var(--gov-navy-400);
    color: var(--gov-navy-800);
    background: var(--gov-navy-50);
}

.pagination-wrapper .page-numbers.current {
    background: var(--gov-navy-800);
    border-color: var(--gov-navy-800);
    color: var(--gov-white);
}

.pagination-wrapper .page-numbers.dots {
    border: none;
    background: transparent;
    color: var(--gov-gray-500);
    pointer-events: none;
}

/* ==========================================================================
   Sidebar - サイドバー
   ========================================================================== */
.yahoo-sidebar {
    background: transparent;
}

.sidebar-widget {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    box-shadow: var(--gov-shadow-sm);
    overflow: hidden;
}

.widget-title {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px;
    background: var(--gov-navy-50);
    border-bottom: 2px solid var(--gov-navy-800);
    font-family: var(--gov-font-serif);
    font-size: 15px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0;
}

.widget-title svg {
    color: var(--gov-navy-600);
    flex-shrink: 0;
}

.widget-content {
    padding: 20px;
}

.no-data {
    text-align: center;
    padding: 24px;
    color: var(--gov-gray-500);
    font-size: 13px;
}

/* Sidebar Ad Space */
.sidebar-ad-space {
    background: var(--gov-gray-100);
    border: 1px dashed var(--gov-gray-300);
    border-radius: var(--gov-radius-lg);
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gov-gray-400);
    font-size: 12px;
}

/* ==========================================================================
   Ranking Widget - ランキングウィジェット（CSS維持設計）
   ========================================================================== */
.sidebar-ranking {
    background: var(--gov-white) !important;
    border: 1px solid var(--gov-gray-200) !important;
    border-radius: var(--gov-radius-lg) !important;
    overflow: hidden !important;
}

.sidebar-ranking .widget-title {
    background: var(--gov-navy-50) !important;
    padding: 16px 20px !important;
    border-bottom: 2px solid var(--gov-navy-800) !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin: 0 !important;
    font-family: var(--gov-font-serif) !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    color: var(--gov-navy-900) !important;
}

.sidebar-ranking .widget-title svg {
    flex-shrink: 0 !important;
    color: var(--gov-navy-600) !important;
}

.ranking-tabs {
    display: flex !important;
    border-bottom: 1px solid var(--gov-gray-200) !important;
    background: var(--gov-gray-50) !important;
}

.ranking-tab {
    flex: 1 !important;
    padding: 12px 16px !important;
    border: none !important;
    background: transparent !important;
    color: var(--gov-gray-600) !important;
    font-family: var(--gov-font-sans) !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all var(--gov-transition) !important;
    position: relative !important;
}

.ranking-tab:hover {
    background: var(--gov-gray-100) !important;
    color: var(--gov-gray-800) !important;
}

.ranking-tab.active {
    background: var(--gov-white) !important;
    color: var(--gov-navy-900) !important;
    font-weight: 700 !important;
}

.ranking-tab.active::after {
    content: '' !important;
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 2px !important;
    background: var(--gov-gold) !important;
}

.ranking-content {
    display: none !important;
    padding: 0 !important;
}

.ranking-content.active {
    display: block !important;
}

.ranking-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.ranking-item {
    border-bottom: 1px solid var(--gov-gray-100) !important;
    transition: background var(--gov-transition) !important;
}

.ranking-item:last-child {
    border-bottom: none !important;
}

.ranking-item:hover {
    background: var(--gov-navy-50) !important;
}

.ranking-link {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 14px 16px !important;
    text-decoration: none !important;
    color: var(--gov-gray-800) !important;
}

.ranking-number {
    flex-shrink: 0 !important;
    width: 28px !important;
    height: 28px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-family: var(--gov-font-mono) !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    color: var(--gov-gray-500) !important;
    background: var(--gov-gray-100) !important;
    border-radius: 50% !important;
}

.rank-1 .ranking-number {
    background: linear-gradient(135deg, var(--gov-gold) 0%, var(--gov-gold-light) 100%) !important;
    color: var(--gov-navy-900) !important;
    box-shadow: 0 2px 6px rgba(201, 162, 39, 0.3) !important;
}

.rank-2 .ranking-number {
    background: var(--gov-navy-300) !important;
    color: var(--gov-navy-900) !important;
}

.rank-3 .ranking-number {
    background: var(--gov-navy-400) !important;
    color: var(--gov-white) !important;
}

.ranking-title {
    flex: 1 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 1.5 !important;
    color: var(--gov-navy-900) !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
}

.ranking-link:hover .ranking-title {
    color: var(--gov-blue) !important;
}

.ranking-views {
    flex-shrink: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 11px !important;
    font-family: var(--gov-font-mono) !important;
    color: var(--gov-gray-500) !important;
}

.ranking-views svg {
    flex-shrink: 0 !important;
}

.ranking-loading {
    padding: 40px 20px !important;
    text-align: center !important;
    color: var(--gov-gray-500) !important;
    font-size: 13px !important;
}

.ranking-empty {
    text-align: center !important;
    padding: 32px 20px !important;
    color: var(--gov-gray-600) !important;
}

.ranking-empty svg {
    margin: 0 auto 12px !important;
    opacity: 0.3 !important;
    display: block !important;
    color: var(--gov-gray-400) !important;
}

.ranking-empty p {
    margin: 0 !important;
}

.ranking-empty p:first-of-type {
    font-size: 14px !important;
    font-weight: 500 !important;
}

.ranking-empty p:last-of-type {
    font-size: 12px !important;
    opacity: 0.7 !important;
    margin-top: 6px !important;
}

.ranking-error {
    text-align: center !important;
    padding: 32px 20px !important;
    color: var(--gov-gray-500) !important;
}

/* ==========================================================================
   Topics Widget - 新着トピックス
   ========================================================================== */
.topics-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.topics-item {
    border-bottom: 1px solid var(--gov-gray-100);
    padding-bottom: 14px;
}

.topics-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.topics-link {
    display: flex;
    flex-direction: column;
    gap: 4px;
    text-decoration: none;
    color: inherit;
}

.topics-link:hover .topics-title {
    color: var(--gov-blue);
}

.topics-date {
    font-size: 11px;
    font-family: var(--gov-font-mono);
    color: var(--gov-gray-500);
}

.topics-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--gov-navy-900);
    line-height: 1.5;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    transition: color var(--gov-transition);
}

/* ==========================================================================
   Categories Widget - カテゴリ一覧
   ========================================================================== */
.categories-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0;
}

.categories-item {
    border-bottom: 1px solid var(--gov-gray-100);
}

.categories-item:last-child {
    border-bottom: none;
}

.categories-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    text-decoration: none;
    color: var(--gov-navy-800);
    font-size: 14px;
    font-weight: 500;
    transition: all var(--gov-transition);
}

.categories-link:hover {
    color: var(--gov-blue);
    padding-left: 8px;
}

.categories-count {
    font-size: 12px;
    font-family: var(--gov-font-mono);
    color: var(--gov-gray-500);
    background: var(--gov-gray-100);
    padding: 2px 8px;
    border-radius: 12px;
}

/* ==========================================================================
   Mobile Filter - モバイルフィルター
   ========================================================================== */
.mobile-filter-toggle {
    display: none;
    position: fixed;
    bottom: 80px;
    left: 16px;
    z-index: 1000;
    padding: 14px 24px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    color: var(--gov-white);
    border: none;
    border-radius: 28px;
    box-shadow: var(--gov-shadow-lg);
    cursor: pointer;
    font-family: var(--gov-font-sans);
    font-size: 14px;
    font-weight: 700;
    align-items: center;
    gap: 8px;
}

.mobile-filter-toggle svg {
    color: var(--gov-gold);
}

.mobile-filter-close {
    display: none;
    background: none;
    border: none;
    font-size: 28px;
    line-height: 1;
    color: var(--gov-gray-600);
    cursor: pointer;
    padding: 8px;
    margin-left: auto;
}

.filter-panel-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 997;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.filter-panel-overlay.active {
    display: block;
    opacity: 1;
}

.mobile-filter-apply-section {
    display: none;
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px 24px;
    background: var(--gov-white);
    border-top: 2px solid var(--gov-gray-200);
    z-index: 20;
}

.mobile-apply-filters-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px 24px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    color: var(--gov-white);
    border: none;
    border-radius: var(--gov-radius);
    font-family: var(--gov-font-sans);
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: var(--gov-shadow);
    transition: all var(--gov-transition);
}

.mobile-apply-filters-btn:active {
    background: var(--gov-navy-900);
}

/* ==========================================================================
   Responsive - レスポンシブ
   ========================================================================== */
@media (max-width: 1024px) {
    .yahoo-two-column-layout {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .yahoo-sidebar {
        display: none;
    }
    
    .grants-container-yahoo[data-view="grid"] {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .yahoo-container {
        padding: 0 16px;
    }
    
    .yahoo-two-column-layout {
        padding: 24px 16px 80px;
    }
    
    .mobile-filter-toggle {
        display: flex !important;
    }
    
    .mobile-filter-close {
        display: block !important;
    }
    
    .mobile-filter-apply-section {
        display: block !important;
    }
    
    .yahoo-filter-section {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--gov-white);
        z-index: 998;
        margin: 0;
        border-radius: 0;
        padding: 0 !important;
        overflow-y: auto !important;
        transform: translateX(100%);
        box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }
    
    .yahoo-filter-section.active {
        transform: translateX(0) !important;
    }
    
    .filter-header {
        position: sticky;
        top: 0;
        background: var(--gov-white);
        z-index: 10;
        padding: 16px 20px !important;
        margin-bottom: 0 !important;
        border-bottom: 2px solid var(--gov-navy-800);
    }
    
    .yahoo-filters-grid {
        grid-template-columns: 1fr;
        padding: 20px;
        padding-bottom: 100px;
        gap: 24px; /* UX改善: モバイルでは間隔を広げる */
    }
    
    /* UX改善: モバイルではドロップダウンを大きく表示 */
    .select-dropdown {
        max-height: 50vh !important;
    }
    
    .multi-select-dropdown {
        max-height: 55vh !important;
    }
    
    .yahoo-hero-section {
        padding: 32px 0;
    }
    
    .yahoo-main-title {
        font-size: 24px;
    }
    
    .yahoo-meta-info {
        gap: 16px;
    }
    
    .meta-item strong {
        font-size: 22px;
    }
    
    .search-bar-wrapper {
        padding: 16px;
    }
    
    .search-input-container {
        flex-wrap: wrap;
    }
    
    .search-execute-btn {
        width: 100%;
        margin-top: 12px;
        justify-content: center;
    }
    
    .results-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 16px 20px;
    }
    
    .view-controls {
        width: 100%;
        justify-content: center;
    }
    
    .grants-container-yahoo {
        padding: 16px;
    }
}

/* ==========================================================================
   Accessibility - アクセシビリティ
   ========================================================================== */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}

.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.grant-archive-yahoo-style a:focus-visible,
.grant-archive-yahoo-style button:focus-visible,
.grant-archive-yahoo-style input:focus-visible,
.grant-archive-yahoo-style select:focus-visible {
    outline: 3px solid var(--gov-gold);
    outline-offset: 2px;
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .filter-tag,
    .multi-select-badge,
    .select-action-btn.apply-btn,
    .search-execute-btn,
    .mobile-apply-filters-btn {
        border: 2px solid var(--gov-white);
    }
    
    .ranking-tab.active::after {
        height: 3px !important;
    }
}

/* ==========================================================================
   Print Styles - 印刷スタイル
   ========================================================================== */
@media print {
    .grant-archive-yahoo-style::before,
    .yahoo-search-section,
    .yahoo-filter-section,
    .view-controls,
    .pagination-wrapper,
    .yahoo-sidebar,
    .mobile-filter-toggle,
    .loading-overlay {
        display: none !important;
    }
    
    .yahoo-two-column-layout {
        grid-template-columns: 1fr;
    }
    
    .yahoo-hero-section {
        background: white !important;
        color: black !important;
        border-bottom: 2px solid black !important;
    }
    
    .yahoo-main-title,
    .category-badge {
        color: black !important;
    }
    
    .grants-container-yahoo {
        display: block !important;
    }
}

/* ==========================================================================
   強制表示 - レイアウト維持
   ========================================================================== */
.grant-archive-yahoo-style,
.yahoo-container,
.yahoo-two-column-layout,
.yahoo-main-content,
.yahoo-results-section,
.grants-container-yahoo {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.yahoo-two-column-layout {
    display: grid !important;
}

.grants-container-yahoo {
    display: flex !important;
}

.grants-container-yahoo[data-view="grid"] {
    display: grid !important;
}

</style>

<!-- JavaScript（すべての機能を保持） -->
<script>
(function() {
    'use strict';
    
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>';
    
    const state = {
        currentPage: 1,
        perPage: 12,
        view: 'single',
        filters: {
            search: '',
            category: [],
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc',
            tag: ''
        },
        isLoading: false,
        tempCategories: [],
        tempPrefectures: [],
        currentMunicipalities: []
    };
    
    const elements = {};
    
    function init() {
        console.log('🚀 Yahoo! Style Archive v19.0 Initialized');
        
        initializeElements();
        initializeFromUrlParams();
        setupCustomSelects();
        setupEventListeners();
        loadGrants();
    }
    
    function initializeElements() {
        elements.grantsContainer = document.getElementById('grants-container');
        elements.loadingOverlay = document.getElementById('loading-overlay');
        elements.noResults = document.getElementById('no-results');
        elements.resultsCount = document.getElementById('current-count');
        elements.showingFrom = document.getElementById('showing-from');
        elements.showingTo = document.getElementById('showing-to');
        elements.paginationWrapper = document.getElementById('pagination-wrapper');
        elements.activeFilters = document.getElementById('active-filters');
        elements.activeFilterTags = document.getElementById('active-filter-tags');
        
        elements.keywordSearch = document.getElementById('keyword-search');
        elements.searchBtn = document.getElementById('search-btn');
        elements.searchClearBtn = document.getElementById('search-clear-btn');
        
        elements.categorySelect = document.getElementById('category-select');
        elements.categorySearch = document.getElementById('category-search');
        elements.categoryOptions = document.getElementById('category-options');
        elements.clearCategoryBtn = document.getElementById('clear-category-btn');
        elements.applyCategoryBtn = document.getElementById('apply-category-btn');
        elements.categoryCountBadge = document.getElementById('category-count-badge');
        
        elements.regionSelect = document.getElementById('region-select');
        
        elements.prefectureSelect = document.getElementById('prefecture-select');
        elements.prefectureSearch = document.getElementById('prefecture-search');
        elements.prefectureOptions = document.getElementById('prefecture-options');
        elements.clearPrefectureBtn = document.getElementById('clear-prefecture-btn');
        elements.applyPrefectureBtn = document.getElementById('apply-prefecture-btn');
        elements.prefectureCountBadge = document.getElementById('prefecture-count-badge');
        
        elements.municipalitySelect = document.getElementById('municipality-select');
        elements.municipalityWrapper = document.getElementById('municipality-wrapper');
        elements.municipalitySearch = document.getElementById('municipality-search');
        elements.municipalityOptions = document.getElementById('municipality-options');
        elements.selectedPrefectureName = document.getElementById('selected-prefecture-name');
        
        elements.amountSelect = document.getElementById('amount-select');
        elements.statusSelect = document.getElementById('status-select');
        elements.sortSelect = document.getElementById('sort-select');
        
        elements.viewBtns = document.querySelectorAll('.view-btn');
        elements.resetAllFiltersBtn = document.getElementById('reset-all-filters-btn');
        
        elements.mobileFilterToggle = document.getElementById('mobile-filter-toggle');
        elements.mobileFilterClose = document.getElementById('mobile-filter-close');
        elements.filterPanel = document.getElementById('filter-panel');
        elements.mobileFilterCount = document.getElementById('mobile-filter-count');
        elements.filterPanelOverlay = document.getElementById('filter-panel-overlay');
    }
    
    function initializeFromUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        
        const searchParam = urlParams.get('search');
        if (searchParam) {
            state.filters.search = searchParam;
            if (elements.keywordSearch) {
                elements.keywordSearch.value = searchParam;
                elements.searchClearBtn.style.display = 'flex';
            }
        }
        
        const categoryParam = urlParams.get('category');
        if (categoryParam) {
            state.filters.category = [categoryParam];
        }
        
        const prefectureParam = urlParams.get('prefecture');
        if (prefectureParam) {
            state.filters.prefecture = [prefectureParam];
        }
        
        const municipalityParam = urlParams.get('municipality');
        if (municipalityParam) {
            state.filters.municipality = municipalityParam;
        }
        
        const tagParam = urlParams.get('grant_tag');
        if (tagParam) {
            state.filters.tag = tagParam;
        }
        
        // 募集状況フィルター（application_status）
        const statusParam = urlParams.get('application_status');
        if (statusParam) {
            // URLパラメータを内部state値にマッピング
            // open -> active（UIのdata-valueと一致させる）
            const statusMapping = {
                'open': 'active',
                'recruiting': 'active',
                '募集中': 'active',
                'upcoming': 'upcoming',
                '募集予定': 'upcoming',
                'closed': 'closed',
                '終了': 'closed'
            };
            const mappedStatus = statusMapping[statusParam] || statusParam;
            state.filters.status = mappedStatus;
            
            // UIのセレクトボックスも更新
            if (elements.statusSelect) {
                const statusValueSpan = elements.statusSelect.querySelector('.select-value');
                const statusOptions = elements.statusSelect.querySelectorAll('.select-option');
                statusOptions.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                    if (opt.dataset.value === mappedStatus) {
                        opt.classList.add('active');
                        opt.setAttribute('aria-selected', 'true');
                        if (statusValueSpan) {
                            statusValueSpan.textContent = opt.textContent.trim();
                        }
                    }
                });
            }
        }
        
        // ソート順フィルター（orderby）
        const orderbyParam = urlParams.get('orderby');
        if (orderbyParam) {
            // orderbyパラメータをsortに変換
            let sortValue = 'date_desc'; // デフォルト
            switch (orderbyParam) {
                case 'deadline':
                    sortValue = 'deadline_asc';
                    break;
                case 'new':
                    sortValue = 'date_desc';
                    break;
                case 'popular':
                    sortValue = 'popular_desc';
                    break;
                case 'amount':
                    sortValue = 'amount_desc';
                    break;
            }
            state.filters.sort = sortValue;
            // UIのセレクトボックスも更新
            if (elements.sortSelect) {
                const sortValueSpan = elements.sortSelect.querySelector('.select-value');
                const sortOptions = elements.sortSelect.querySelectorAll('.select-option');
                sortOptions.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                    if (opt.dataset.value === sortValue) {
                        opt.classList.add('active');
                        opt.setAttribute('aria-selected', 'true');
                        if (sortValueSpan) {
                            sortValueSpan.textContent = opt.textContent.trim();
                        }
                    }
                });
            }
        }
        
        // 初期化後にアクティブフィルターを表示
        updateActiveFiltersDisplay();
    }
    
    function setupCustomSelects() {
        setupMultiSelectCategory();
        setupSingleSelect(elements.regionSelect, (value) => {
            state.filters.region = value;
            filterPrefecturesByRegion(value);
            state.currentPage = 1;
            loadGrants();
        });
        setupMultiSelectPrefecture();
        setupMunicipalitySelect();
        setupSingleSelect(elements.amountSelect, (value) => {
            state.filters.amount = value;
            state.currentPage = 1;
            loadGrants();
        });
        setupSingleSelect(elements.statusSelect, (value) => {
            state.filters.status = value;
            state.currentPage = 1;
            loadGrants();
        });
        setupSingleSelect(elements.sortSelect, (value) => {
            state.filters.sort = value;
            state.currentPage = 1;
            loadGrants();
        });
    }
    
    function setupSingleSelect(selectElement, onChange) {
        if (!selectElement) return;
        
        const trigger = selectElement.querySelector('.select-trigger');
        const dropdown = selectElement.querySelector('.select-dropdown');
        const options = selectElement.querySelectorAll('.select-option');
        const valueSpan = selectElement.querySelector('.select-value');
        
        trigger.addEventListener('click', () => {
            const isActive = selectElement.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                selectElement.classList.add('active');
                selectElement.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
            }
        });
        
        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                const text = option.textContent.trim();
                
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                option.classList.add('active');
                option.setAttribute('aria-selected', 'true');
                
                valueSpan.textContent = text;
                
                selectElement.classList.remove('active');
                selectElement.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                // モバイルではフィルターパネルを閉じない（適用ボタンで一括適用）
                if (window.innerWidth > 768) {
                    onChange(value);
                } else {
                    // モバイルでは値だけ更新して、loadGrantsは呼ばない
                    const filterName = selectElement.id.replace('-select', '');
                    if (filterName === 'region') {
                        state.filters.region = value;
                        filterPrefecturesByRegion(value);
                    } else if (filterName === 'amount') {
                        state.filters.amount = value;
                    } else if (filterName === 'status') {
                        state.filters.status = value;
                    } else if (filterName === 'sort') {
                        state.filters.sort = value;
                    }
                }
            });
        });
    }
    
    function setupMultiSelectCategory() {
        if (!elements.categorySelect) return;
        
        const trigger = elements.categorySelect.querySelector('.select-trigger');
        const dropdown = elements.categorySelect.querySelector('.select-dropdown');
        const valueSpan = elements.categorySelect.querySelector('.select-value');
        const checkboxes = elements.categoryOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('cat-all');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.categorySelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.categorySelect.classList.add('active');
                elements.categorySelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
                state.tempCategories = [...state.filters.category];
                updateCategoryCheckboxes();
            }
        });
        
        if (elements.categorySearch) {
            elements.categorySearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.categoryOptions.querySelectorAll('.select-option:not(.all-option)');
                
                options.forEach(option => {
                    const name = option.dataset.name.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
        
        if (allCheckbox) {
            allCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    state.tempCategories = [];
                    checkboxes.forEach(cb => {
                        if (cb !== allCheckbox) {
                            cb.checked = false;
                        }
                    });
                }
            });
        }
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    
                    if (e.target.checked) {
                        if (!state.tempCategories.includes(value)) {
                            state.tempCategories.push(value);
                        }
                        allCheckbox.checked = false;
                    } else {
                        const index = state.tempCategories.indexOf(value);
                        if (index > -1) {
                            state.tempCategories.splice(index, 1);
                        }
                        if (state.tempCategories.length === 0) {
                            allCheckbox.checked = true;
                        }
                    }
                });
            }
        });
        
        if (elements.clearCategoryBtn) {
            elements.clearCategoryBtn.addEventListener('click', () => {
                state.tempCategories = [];
                updateCategoryCheckboxes();
                allCheckbox.checked = true;
            });
        }
        
        if (elements.applyCategoryBtn) {
            elements.applyCategoryBtn.addEventListener('click', () => {
                state.filters.category = [...state.tempCategories];
                updateCategoryDisplay();
                elements.categorySelect.classList.remove('active');
                elements.categorySelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                // モバイルではフィルターパネルを閉じない（適用ボタンで一括適用）
                if (window.innerWidth > 768) {
                    state.currentPage = 1;
                    loadGrants();
                }
            });
        }
    }
    
    function setupMultiSelectPrefecture() {
        if (!elements.prefectureSelect) return;
        
        const trigger = elements.prefectureSelect.querySelector('.select-trigger');
        const dropdown = elements.prefectureSelect.querySelector('.select-dropdown');
        const valueSpan = elements.prefectureSelect.querySelector('.select-value');
        const checkboxes = elements.prefectureOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('pref-all');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.prefectureSelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.prefectureSelect.classList.add('active');
                elements.prefectureSelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
                state.tempPrefectures = [...state.filters.prefecture];
                updatePrefectureCheckboxes();
            }
        });
        
        if (elements.prefectureSearch) {
            elements.prefectureSearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
                
                options.forEach(option => {
                    const name = option.dataset.name.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
        
        if (allCheckbox) {
            allCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    state.tempPrefectures = [];
                    checkboxes.forEach(cb => {
                        if (cb !== allCheckbox) {
                            cb.checked = false;
                        }
                    });
                }
            });
        }
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    
                    if (e.target.checked) {
                        if (!state.tempPrefectures.includes(value)) {
                            state.tempPrefectures.push(value);
                        }
                        allCheckbox.checked = false;
                    } else {
                        const index = state.tempPrefectures.indexOf(value);
                        if (index > -1) {
                            state.tempPrefectures.splice(index, 1);
                        }
                        if (state.tempPrefectures.length === 0) {
                            allCheckbox.checked = true;
                        }
                    }
                });
            }
        });
        
        if (elements.clearPrefectureBtn) {
            elements.clearPrefectureBtn.addEventListener('click', () => {
                state.tempPrefectures = [];
                updatePrefectureCheckboxes();
                allCheckbox.checked = true;
            });
        }
        
        if (elements.applyPrefectureBtn) {
            elements.applyPrefectureBtn.addEventListener('click', () => {
                state.filters.prefecture = [...state.tempPrefectures];
                updatePrefectureDisplay();
                elements.prefectureSelect.classList.remove('active');
                elements.prefectureSelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                if (state.filters.prefecture.length === 1) {
                    const prefectureSlug = state.filters.prefecture[0];
                    const prefectureOption = document.querySelector(`.select-option[data-value="${prefectureSlug}"]`);
                    const prefectureName = prefectureOption ? prefectureOption.dataset.name : '';
                    loadMunicipalities(prefectureSlug, prefectureName);
                } else {
                    hideMunicipalityFilter();
                    state.filters.municipality = '';
                }
                
                // モバイルではフィルターパネルを閉じない（適用ボタンで一括適用）
                if (window.innerWidth > 768) {
                    state.currentPage = 1;
                    loadGrants();
                }
            });
        }
    }
    
    function setupMunicipalitySelect() {
        if (!elements.municipalitySelect) return;
        
        const trigger = elements.municipalitySelect.querySelector('.select-trigger');
        const dropdown = elements.municipalitySelect.querySelector('.select-dropdown');
        const valueSpan = elements.municipalitySelect.querySelector('.select-value');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.municipalitySelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.municipalitySelect.classList.add('active');
                elements.municipalitySelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
            }
        });
        
        if (elements.municipalitySearch) {
            elements.municipalitySearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.municipalityOptions.querySelectorAll('.select-option');
                
                options.forEach(option => {
                    const name = option.textContent.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
    }
    
    function updateCategoryCheckboxes() {
        const checkboxes = elements.categoryOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('cat-all');
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.checked = state.tempCategories.includes(checkbox.value);
            }
        });
        
        allCheckbox.checked = state.tempCategories.length === 0;
    }
    
    function updateCategoryDisplay() {
        const valueSpan = elements.categorySelect.querySelector('.select-value');
        const count = state.filters.category.length;
        
        if (count === 0) {
            valueSpan.textContent = '選択';
            elements.categoryCountBadge.style.display = 'none';
        } else {
            valueSpan.textContent = `${count}件選択`;
            elements.categoryCountBadge.textContent = count;
            elements.categoryCountBadge.style.display = 'inline-flex';
        }
    }
    
    function updatePrefectureCheckboxes() {
        const checkboxes = elements.prefectureOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('pref-all');
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.checked = state.tempPrefectures.includes(checkbox.value);
            }
        });
        
        allCheckbox.checked = state.tempPrefectures.length === 0;
    }
    
    function updatePrefectureDisplay() {
        const valueSpan = elements.prefectureSelect.querySelector('.select-value');
        const count = state.filters.prefecture.length;
        
        if (count === 0) {
            valueSpan.textContent = '選択';
            elements.prefectureCountBadge.style.display = 'none';
        } else {
            valueSpan.textContent = `${count}件選択`;
            elements.prefectureCountBadge.textContent = count;
            elements.prefectureCountBadge.style.display = 'inline-flex';
        }
    }
    
    function filterPrefecturesByRegion(region) {
        if (!elements.prefectureOptions) return;
        
        const options = elements.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
        
        options.forEach(option => {
            const optionRegion = option.dataset.region;
            if (!region || optionRegion === region) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
        });
    }
    
    function closeAllSelects() {
        document.querySelectorAll('.custom-select').forEach(select => {
            select.classList.remove('active');
            select.setAttribute('aria-expanded', 'false');
            const dropdown = select.querySelector('.select-dropdown');
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        });
    }
    
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-select')) {
            closeAllSelects();
        }
    });
    
    function loadMunicipalities(prefectureSlug, prefectureName) {
        if (!prefectureSlug) {
            hideMunicipalityFilter();
            return;
        }
        
        if (elements.municipalityWrapper) {
            elements.municipalityWrapper.style.display = 'block';
        }
        
        if (elements.selectedPrefectureName) {
            elements.selectedPrefectureName.textContent = `（${prefectureName}）`;
        }
        
        // Show loading state
        if (elements.municipalityOptions) {
            elements.municipalityOptions.innerHTML = '<div class="select-option loading-option" role="option">読み込み中...</div>';
        }
        
        const formData = new FormData();
        formData.append('action', 'gi_get_municipalities_for_prefecture');
        formData.append('prefecture_slug', prefectureSlug);
        formData.append('nonce', NONCE);
        
        // Timeout fallback to prevent infinite loading
        const timeoutId = setTimeout(() => {
            console.warn('⏱️ Municipality AJAX timeout - request took too long');
            renderMunicipalityOptions([]);
        }, 10000); // 10 second timeout
        
        fetch(AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            clearTimeout(timeoutId); // Clear timeout on response
            return response.json();
        })
        .then(data => {
            console.log('🏘️ Municipality AJAX Response:', data);
            let municipalities = [];
            
            if (data.success) {
                // Log the structure to understand what we received
                console.log('✅ Success response - data structure:', {
                    hasData: !!data.data,
                    hasDataData: !!(data.data && data.data.data),
                    hasDataMunicipalities: !!(data.data && data.data.municipalities),
                    hasMunicipalities: !!data.municipalities
                });
                
                if (data.data && data.data.data && Array.isArray(data.data.data.municipalities)) {
                    municipalities = data.data.data.municipalities;
                    console.log('📍 Found municipalities at: data.data.data.municipalities');
                } else if (data.data && Array.isArray(data.data.municipalities)) {
                    municipalities = data.data.municipalities;
                    console.log('📍 Found municipalities at: data.data.municipalities');
                } else if (Array.isArray(data.municipalities)) {
                    municipalities = data.municipalities;
                    console.log('📍 Found municipalities at: data.municipalities');
                } else if (Array.isArray(data.data)) {
                    municipalities = data.data;
                    console.log('📍 Found municipalities at: data.data');
                } else {
                    console.warn('⚠️ Municipality data not found in expected locations');
                }
            } else {
                console.error('❌ AJAX request failed:', data.data || data.message);
            }
            
            console.log(`✅ Rendering ${municipalities.length} municipalities`);
            if (municipalities.length > 0) {
                state.currentMunicipalities = municipalities;
                renderMunicipalityOptions(municipalities);
            } else {
                renderMunicipalityOptions([]);
            }
        })
        .catch(error => {
            clearTimeout(timeoutId); // Clear timeout on error
            console.error('❌ Municipality fetch error:', error);
            renderMunicipalityOptions([]);
        });
    }
    
    function renderMunicipalityOptions(municipalities) {
        if (!elements.municipalityOptions) return;
        
        let html = '<div class="select-option active" data-value="" role="option">すべて</div>';
        
        municipalities.forEach(municipality => {
            html += `<div class="select-option" data-value="${municipality.slug}" role="option">${municipality.name}</div>`;
        });
        
        elements.municipalityOptions.innerHTML = html;
        
        const options = elements.municipalityOptions.querySelectorAll('.select-option');
        const valueSpan = elements.municipalitySelect.querySelector('.select-value');
        const dropdown = elements.municipalitySelect.querySelector('.select-dropdown');
        
        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                const text = option.textContent.trim();
                
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                option.classList.add('active');
                option.setAttribute('aria-selected', 'true');
                
                valueSpan.textContent = text;
                
                elements.municipalitySelect.classList.remove('active');
                elements.municipalitySelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                state.filters.municipality = value;
                
                // モバイルではフィルターパネルを閉じない（適用ボタンで一括適用）
                if (window.innerWidth > 768) {
                    state.currentPage = 1;
                    loadGrants();
                }
            });
        });
    }
    
    function hideMunicipalityFilter() {
        if (elements.municipalityWrapper) {
            elements.municipalityWrapper.style.display = 'none';
        }
        
        state.filters.municipality = '';
        if (elements.municipalitySelect) {
            const valueSpan = elements.municipalitySelect.querySelector('.select-value');
            if (valueSpan) {
                valueSpan.textContent = 'すべて';
            }
        }
    }
    
    function setupEventListeners() {
        if (elements.keywordSearch) {
            elements.keywordSearch.addEventListener('input', debounce(handleSearchInput, 300));
            elements.keywordSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
        }
        
        if (elements.searchBtn) {
            elements.searchBtn.addEventListener('click', handleSearch);
        }
        
        if (elements.searchClearBtn) {
            elements.searchClearBtn.addEventListener('click', clearSearch);
        }
        
        elements.viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                elements.viewBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                state.view = this.dataset.view;
                elements.grantsContainer.setAttribute('data-view', state.view);
            });
        });
        
        if (elements.resetAllFiltersBtn) {
            elements.resetAllFiltersBtn.addEventListener('click', resetAllFilters);
        }
        
        // モバイル用フィルター適用ボタン
        const mobileApplyFiltersBtn = document.getElementById('mobile-apply-filters-btn');
        if (mobileApplyFiltersBtn) {
            mobileApplyFiltersBtn.addEventListener('click', function() {
                state.currentPage = 1;
                loadGrants();
                closeMobileFilter();
            });
        }
        
        // モバイルフィルター
        if (elements.mobileFilterToggle) {
            elements.mobileFilterToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (elements.filterPanel && elements.filterPanel.classList.contains('active')) {
                    closeMobileFilter();
                } else {
                    openMobileFilter();
                }
            }, false);
        }
        
        if (elements.mobileFilterClose) {
            elements.mobileFilterClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileFilter();
            }, false);
        }
        
        if (elements.filterPanelOverlay) {
            elements.filterPanelOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileFilter();
            }, false);
        }
        
        if (elements.filterPanel) {
            elements.filterPanel.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth <= 768) {
                if (elements.filterPanel && elements.filterPanel.classList.contains('active')) {
                    closeMobileFilter();
                }
            }
        });
    }
    
    function openMobileFilter() {
        if (elements.filterPanel) {
            elements.filterPanel.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            if (elements.filterPanelOverlay) {
                elements.filterPanelOverlay.classList.add('active');
            }
            if (elements.mobileFilterToggle) {
                elements.mobileFilterToggle.setAttribute('aria-expanded', 'true');
            }
        }
    }
    
    function closeMobileFilter() {
        if (elements.filterPanel) {
            elements.filterPanel.classList.remove('active');
            document.body.style.overflow = '';
            
            if (elements.filterPanelOverlay) {
                elements.filterPanelOverlay.classList.remove('active');
            }
            if (elements.mobileFilterToggle) {
                elements.mobileFilterToggle.setAttribute('aria-expanded', 'false');
            }
        }
    }
    
    function handleSearchInput() {
        const query = elements.keywordSearch.value.trim();
        if (query.length > 0) {
            elements.searchClearBtn.style.display = 'flex';
        } else {
            elements.searchClearBtn.style.display = 'none';
        }
    }
    
    function handleSearch() {
        const query = elements.keywordSearch.value.trim();
        state.filters.search = query;
        state.currentPage = 1;
        loadGrants();
    }
    
    function clearSearch() {
        elements.keywordSearch.value = '';
        state.filters.search = '';
        elements.searchClearBtn.style.display = 'none';
        state.currentPage = 1;
        loadGrants();
    }
    
    function resetAllFilters() {
        state.filters = {
            search: '',
            category: [],
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc',
            tag: ''
        };
        state.tempCategories = [];
        state.tempPrefectures = [];
        state.currentPage = 1;
        
        elements.keywordSearch.value = '';
        elements.searchClearBtn.style.display = 'none';
        
        resetCustomSelect(elements.regionSelect, '全国');
        resetCustomSelect(elements.amountSelect, '指定なし');
        resetCustomSelect(elements.statusSelect, 'すべて');
        resetCustomSelect(elements.sortSelect, '新着順');
        
        updateCategoryDisplay();
        updateCategoryCheckboxes();
        updatePrefectureDisplay();
        updatePrefectureCheckboxes();
        
        filterPrefecturesByRegion('');
        hideMunicipalityFilter();
        
        loadGrants();
    }
    
    function resetCustomSelect(selectElement, defaultText) {
        if (!selectElement) return;
        
        const valueSpan = selectElement.querySelector('.select-value');
        const options = selectElement.querySelectorAll('.select-option');
        
        valueSpan.textContent = defaultText;
        options.forEach(opt => {
            opt.classList.remove('active');
            opt.setAttribute('aria-selected', 'false');
        });
        options[0].classList.add('active');
        options[0].setAttribute('aria-selected', 'true');
    }
    
    function loadGrants() {
        if (state.isLoading) return;
        
        state.isLoading = true;
        showLoading(true);
        
        const formData = new FormData();
        formData.append('action', 'gi_ajax_load_grants');
        formData.append('nonce', NONCE);
        formData.append('page', state.currentPage);
        formData.append('posts_per_page', state.perPage);
        formData.append('view', state.view);
        
        if (state.filters.search) {
            formData.append('search', state.filters.search);
        }
        
        if (state.filters.category.length > 0) {
            formData.append('categories', JSON.stringify(state.filters.category));
        }
        
        if (state.filters.prefecture.length > 0) {
            formData.append('prefectures', JSON.stringify(state.filters.prefecture));
        }
        
        if (state.filters.municipality && state.filters.municipality !== '') {
            formData.append('municipalities', JSON.stringify([state.filters.municipality]));
        }
        
        if (state.filters.region) {
            formData.append('region', state.filters.region);
        }
        
        if (state.filters.amount) {
            formData.append('amount', state.filters.amount);
        }
        
        if (state.filters.status) {
            formData.append('status', JSON.stringify([state.filters.status]));
        }
        
        if (state.filters.tag) {
            formData.append('tag', state.filters.tag);
        }
        
        formData.append('sort', state.filters.sort);
        
        fetch(AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayGrants(data.data.grants);
                updateStats(data.data.stats);
                updatePagination(data.data.pagination);
                updateActiveFiltersDisplay();
            } else {
                showError('データの読み込みに失敗しました。');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showError('通信エラーが発生しました。');
        })
        .finally(() => {
            state.isLoading = false;
            showLoading(false);
        });
    }
    
    function displayGrants(grants) {
        if (!elements.grantsContainer) return;
        
        if (!grants || grants.length === 0) {
            elements.grantsContainer.innerHTML = '';
            elements.grantsContainer.style.display = 'none';
            if (elements.noResults) {
                elements.noResults.style.display = 'block';
            }
            return;
        }
        
        elements.grantsContainer.style.display = state.view === 'single' ? 'flex' : 'grid';
        if (elements.noResults) {
            elements.noResults.style.display = 'none';
        }
        
        const fragment = document.createDocumentFragment();
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = grants.map(grant => grant.html).join('');
        
        while (tempDiv.firstChild) {
            fragment.appendChild(tempDiv.firstChild);
        }
        
        elements.grantsContainer.innerHTML = '';
        elements.grantsContainer.appendChild(fragment);
    }
    
    function updateStats(stats) {
        if (elements.resultsCount) {
            elements.resultsCount.textContent = (stats.total_found || 0).toLocaleString();
        }
        if (elements.showingFrom) {
            elements.showingFrom.textContent = (stats.showing_from || 0).toLocaleString();
        }
        if (elements.showingTo) {
            elements.showingTo.textContent = (stats.showing_to || 0).toLocaleString();
        }
    }
    
    function updatePagination(pagination) {
        if (!elements.paginationWrapper) return;
        
        if (!pagination || pagination.total_pages <= 1) {
            elements.paginationWrapper.innerHTML = '';
            return;
        }
        
        const currentPage = pagination.current_page || 1;
        const totalPages = pagination.total_pages || 1;
        
        let html = '<div class="page-numbers">';
        
        // Previous button
        if (currentPage > 1) {
            html += `<a href="#" class="page-numbers prev" data-page="${currentPage - 1}">前へ</a>`;
        }
        
        // Page numbers logic
        const range = 2; // Show 2 pages on each side of current
        let startPage = Math.max(1, currentPage - range);
        let endPage = Math.min(totalPages, currentPage + range);
        
        // First page
        if (startPage > 1) {
            html += `<a href="#" class="page-numbers" data-page="1">1</a>`;
            if (startPage > 2) {
                html += '<span class="page-numbers dots">…</span>';
            }
        }
        
        // Page number buttons
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += `<span class="page-numbers current">${i}</span>`;
            } else {
                html += `<a href="#" class="page-numbers" data-page="${i}">${i}</a>`;
            }
        }
        
        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += '<span class="page-numbers dots">…</span>';
            }
            html += `<a href="#" class="page-numbers" data-page="${totalPages}">${totalPages}</a>`;
        }
        
        // Next button
        if (currentPage < totalPages) {
            html += `<a href="#" class="page-numbers next" data-page="${currentPage + 1}">次へ</a>`;
        }
        
        html += '</div>';
        
        elements.paginationWrapper.innerHTML = html;
        
        // Add click handlers to pagination links
        elements.paginationWrapper.querySelectorAll('a.page-numbers').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page && page !== currentPage) {
                    state.currentPage = page;
                    loadGrants();
                    
                    // UX改善: ページ遷移時に検索結果ヘッダーにスクロール
                    // 「検索結果 13,978件」が見えるようにする
                    const resultsHeader = document.querySelector('.results-header');
                    if (resultsHeader) {
                        const headerHeight = 80; // 固定ヘッダーの高さ分オフセット
                        const elementPosition = resultsHeader.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                        window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                    } else if (elements.grantsContainer) {
                        elements.grantsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    }
    
    function updateActiveFiltersDisplay() {
        if (!elements.activeFilters || !elements.activeFilterTags) return;
        
        const tags = [];
        
        if (state.filters.search) {
            tags.push({
                type: 'search',
                label: `検索: "${state.filters.search}"`,
                value: state.filters.search
            });
        }
        
        if (state.filters.category.length > 0) {
            state.filters.category.forEach(catSlug => {
                const option = document.querySelector(`.select-option[data-value="${catSlug}"]`);
                if (option) {
                    tags.push({
                        type: 'category',
                        label: option.dataset.name || option.textContent.trim(),
                        value: catSlug
                    });
                }
            });
        }
        
        if (state.filters.prefecture.length > 0) {
            state.filters.prefecture.forEach(prefSlug => {
                const option = document.querySelector(`.select-option[data-value="${prefSlug}"]`);
                if (option) {
                    tags.push({
                        type: 'prefecture',
                        label: option.dataset.name || option.textContent.trim(),
                        value: prefSlug
                    });
                }
            });
        }
        
        if (state.filters.municipality) {
            const municipalityOption = Array.from(elements.municipalityOptions.querySelectorAll('.select-option')).find(opt => opt.dataset.value === state.filters.municipality);
            if (municipalityOption) {
                tags.push({
                    type: 'municipality',
                    label: `市町村: ${municipalityOption.textContent.trim()}`,
                    value: state.filters.municipality
                });
            }
        }
        
        if (state.filters.amount) {
            const labels = {
                '0-100': '〜100万円',
                '100-500': '100万円〜500万円',
                '500-1000': '500万円〜1000万円',
                '1000-3000': '1000万円〜3000万円',
                '3000+': '3000万円以上'
            };
            tags.push({
                type: 'amount',
                label: `金額: ${labels[state.filters.amount]}`,
                value: state.filters.amount
            });
        }
        
        if (state.filters.status) {
            const labels = {
                'open': '募集中',
                'active': '募集中',
                'recruiting': '募集中',
                'upcoming': '募集予定',
                'closed': '募集終了'
            };
            const statusLabel = labels[state.filters.status] || state.filters.status;
            tags.push({
                type: 'status',
                label: `状況: ${statusLabel}`,
                value: state.filters.status
            });
        }
        
        // ソート順の表示（デフォルト以外の場合）
        if (state.filters.sort && state.filters.sort !== 'date_desc') {
            const sortLabels = {
                'deadline_asc': '締切間近順',
                'popular_desc': '人気順',
                'amount_desc': '金額順',
                'featured_first': '注目順',
                'date_asc': '古い順'
            };
            if (sortLabels[state.filters.sort]) {
                tags.push({
                    type: 'sort',
                    label: `並び順: ${sortLabels[state.filters.sort]}`,
                    value: state.filters.sort
                });
            }
        }
        
        if (state.filters.tag) {
            tags.push({
                type: 'tag',
                label: `#${state.filters.tag}`,
                value: state.filters.tag
            });
        }
        
        if (tags.length === 0) {
            elements.activeFilters.style.display = 'none';
            elements.resetAllFiltersBtn.style.display = 'none';
            if (elements.mobileFilterCount) {
                elements.mobileFilterCount.style.display = 'none';
            }
            return;
        }
        
        elements.activeFilters.style.display = 'flex';
        elements.resetAllFiltersBtn.style.display = 'flex';
        
        if (elements.mobileFilterCount) {
            elements.mobileFilterCount.textContent = tags.length;
            elements.mobileFilterCount.style.display = 'flex';
        }
        
        elements.activeFilterTags.innerHTML = tags.map(tag => `
            <div class="filter-tag">
                <span>${escapeHtml(tag.label)}</span>
                <button class="filter-tag-remove" 
                        data-type="${tag.type}" 
                        data-value="${escapeHtml(tag.value)}"
                        type="button">×</button>
            </div>
        `).join('');
        
        elements.activeFilterTags.querySelectorAll('.filter-tag-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                removeFilter(this.dataset.type, this.dataset.value);
            });
        });
    }
    
    function removeFilter(type, value) {
        switch(type) {
            case 'search':
                clearSearch();
                break;
            case 'category':
                const catIndex = state.filters.category.indexOf(value);
                if (catIndex > -1) {
                    state.filters.category.splice(catIndex, 1);
                }
                state.tempCategories = [...state.filters.category];
                updateCategoryDisplay();
                updateCategoryCheckboxes();
                break;
            case 'prefecture':
                const prefIndex = state.filters.prefecture.indexOf(value);
                if (prefIndex > -1) {
                    state.filters.prefecture.splice(prefIndex, 1);
                }
                state.tempPrefectures = [...state.filters.prefecture];
                updatePrefectureDisplay();
                updatePrefectureCheckboxes();
                
                if (state.filters.prefecture.length !== 1) {
                    hideMunicipalityFilter();
                }
                break;
            case 'municipality':
                state.filters.municipality = '';
                const valueSpan = elements.municipalitySelect.querySelector('.select-value');
                if (valueSpan) {
                    valueSpan.textContent = 'すべて';
                }
                const options = elements.municipalityOptions.querySelectorAll('.select-option');
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                options[0].classList.add('active');
                options[0].setAttribute('aria-selected', 'true');
                break;
            case 'amount':
                state.filters.amount = '';
                resetCustomSelect(elements.amountSelect, '指定なし');
                break;
            case 'status':
                state.filters.status = '';
                resetCustomSelect(elements.statusSelect, 'すべて');
                break;
            case 'tag':
                state.filters.tag = '';
                break;
        }
        
        state.currentPage = 1;
        loadGrants();
    }
    
    function showLoading(show) {
        if (elements.loadingOverlay) {
            elements.loadingOverlay.style.display = show ? 'flex' : 'none';
        }
        if (elements.grantsContainer) {
            elements.grantsContainer.style.opacity = show ? '0.5' : '1';
        }
    }
    
    function showError(message) {
        console.error('Error:', message);
        alert(message);
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    console.log('✅ Yahoo! Style Archive v19.0 - Fully Loaded');
    
})();

// アクセスランキング タブ切り替え
(function() {
    'use strict';
    
    const tabs = document.querySelectorAll('.ranking-tab');
    const contents = document.querySelectorAll('.ranking-content');
    
    if (tabs.length === 0) return;
    
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            const targetId = this.getAttribute('data-target');
            
            console.log('📊 Tab clicked - Period:', period, 'Target:', targetId);
            
            // タブのアクティブ切り替え
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // コンテンツの表示切り替え
            contents.forEach(c => c.classList.remove('active'));
            const targetContent = document.querySelector(targetId);
            
            if (targetContent) {
                targetContent.classList.add('active');
                console.log('✅ Target content found:', targetId);
                
                // データがまだ読み込まれていない場合はAJAXで取得
                const hasLoadingDiv = targetContent.querySelector('.ranking-loading');
                console.log('🔍 Has loading div:', hasLoadingDiv !== null);
                
                if (hasLoadingDiv) {
                    loadRankingData(period, targetContent);
                } else {
                    console.log('ℹ️ Data already loaded for this period');
                }
            } else {
                console.error('❌ Target content not found:', targetId);
            }
        });
    });
    
    function loadRankingData(period, container) {
        console.log('🔄 Loading ranking data for period:', period);
        
        // ローディング表示
        container.innerHTML = '<div class="ranking-loading">読み込み中...</div>';
        
        // ネイティブJavaScript fetch APIを使用
        const formData = new FormData();
        formData.append('action', 'get_ranking_data');
        formData.append('period', period);
        formData.append('post_type', 'grant');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('📡 Response received:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Data parsed:', data);
            if (data.success && data.data) {
                container.innerHTML = data.data;
                console.log('✅ Ranking loaded successfully');
            } else {
                console.warn('⚠️ No data in response');
                container.innerHTML = '<div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;"><p style="margin: 0; font-size: 14px;">データがありません</p></div>';
            }
        })
        .catch(error => {
            console.error('❌ Fetch Error:', error);
            container.innerHTML = '<div class="ranking-error" style="text-align: center; padding: 30px 20px; color: #999;"><p style="margin: 0; font-size: 14px;">エラーが発生しました</p><p style="margin: 5px 0 0; font-size: 12px; opacity: 0.7;">しばらくしてから再度お試しください</p></div>';
        });
    }
    
    console.log('✅ Ranking tabs initialized');
})();

// デバッグ: PHP関数の存在確認をコンソールに出力
(function() {
    console.log('\n🔍 === Ad Function Debug Info ===');
    console.log('📍 Page: archive-grant.php');
    
    // PHPからの情報を出力
    <?php
    echo "console.log('🔵 PHP Debug Info:');";
    echo "console.log('  - ji_display_ad exists: " . (function_exists('ji_display_ad') ? 'YES ✅' : 'NO ❌') . "');";
    echo "console.log('  - JI_Affiliate_Ad_Manager class exists: " . (class_exists('JI_Affiliate_Ad_Manager') ? 'YES ✅' : 'NO ❌') . "');";
    echo "console.log('  - Theme directory: " . esc_js(get_template_directory()) . "');";
    
    // 広告スペースのHTML確認
    echo "console.log('\\n🔍 Ad Space Elements:');";
    ?>
    
    const adSpaces = document.querySelectorAll('.sidebar-ad-space');
    console.log(`  - Total ad spaces found: ${adSpaces.length}`);
    
    adSpaces.forEach((space, index) => {
        const className = space.className;
        const hasContent = space.innerHTML.trim().length > 0;
        console.log(`  - Ad space #${index + 1}: ${className}`);
        console.log(`    Content: ${hasContent ? 'YES ✅' : 'EMPTY ❌'}`);
        if (hasContent) {
            console.log(`    HTML length: ${space.innerHTML.trim().length} chars`);
            console.log(`    Preview: ${space.innerHTML.trim().substring(0, 100)}...`);
        }
    });
    
    console.log('\n💡 Next steps:');
    console.log('  1. Check check-debug-status.php for detailed diagnostics');
    console.log('  2. Check WordPress error logs for emoji-prefixed messages');
    console.log('  3. Verify inc/affiliate-ad-manager.php is loaded');
    console.log('🔍 ================================\n');
})();
</script>

<?php 
// モバイル検索モーダルを追加（一覧ページ用）
get_template_part('template-parts/sidebar/mobile-search-modal'); 

get_footer(); 
?>