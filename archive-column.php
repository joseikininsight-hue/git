<?php
/**
 * Archive Template for Column Post Type - Yahoo! JAPAN Inspired SEO Perfect Edition
 * コラム記事アーカイブページ - Yahoo!風デザイン・SEO完全最適化版
 * 
 * @package Grant_Insight_Perfect
 * @version 20.0.0 - Professional Icons & Fixed Filter Layout
 * 
 * === Changes from v19.0 ===
 * - 絵文字アイコンをSVGアイコンに変更（プロフェッショナル感向上）
 * - 絞り込みセクションの表示領域を修正
 * - archive-grant.phpとデザイン統一
 */

get_header();

// URLパラメータの取得と処理
$url_params = array(
    'category' => isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '',
    'tag' => isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '',
    'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '',
    'search' => isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '',
    'difficulty' => isset($_GET['difficulty']) ? sanitize_text_field($_GET['difficulty']) : '',
    'view' => isset($_GET['view']) ? sanitize_text_field($_GET['view']) : '',
);

// 各種データ取得
$current_category = get_queried_object();
$is_category_archive = is_tax('column_category');
$is_tag_archive = is_tax('column_tag');

// ページネーション
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$posts_per_page = 12;

// タイトル・説明文の生成
if (!empty($url_params['orderby']) && $url_params['orderby'] === 'popular') {
    $archive_title = '人気のコラム記事';
    $archive_description = '多くの読者に読まれている人気コラム記事をランキング形式でご紹介。補助金・助成金の活用ノウハウを学べます。';
} elseif (!empty($url_params['orderby']) && $url_params['orderby'] === 'new') {
    $archive_title = '新着コラム記事';
    $archive_description = '最新公開のコラム記事一覧。補助金・助成金に関する最新情報をいち早くお届けします。';
} elseif (!empty($url_params['difficulty'])) {
    $difficulty_labels = array(
        'beginner' => '初心者向け',
        'intermediate' => '中級者向け',
        'advanced' => '上級者向け'
    );
    $difficulty_label = isset($difficulty_labels[$url_params['difficulty']]) ? $difficulty_labels[$url_params['difficulty']] : '';
    if ($difficulty_label) {
        $archive_title = $difficulty_label . 'コラム記事';
        $archive_description = $difficulty_label . 'のコラム記事を厳選。レベルに合わせた補助金・助成金の情報を提供します。';
    } else {
        $archive_title = '補助金・助成金コラム';
        $archive_description = '補助金・助成金の活用ノウハウ、申請のコツ、最新情報をお届けする専門コラムです。毎日更新。';
    }
} elseif ($is_category_archive) {
    $archive_title = $current_category->name . 'のコラム記事';
    $archive_description = $current_category->description ?: $current_category->name . 'に関する補助金・助成金コラム記事一覧です。専門家監修の情報をお届けします。';
} elseif ($is_tag_archive) {
    $archive_title = '「' . $current_category->name . '」タグのコラム記事';
    $archive_description = $current_category->name . 'に関連する補助金・助成金コラム記事一覧です。';
} elseif (!empty($url_params['search'])) {
    $archive_title = '「' . $url_params['search'] . '」の検索結果';
    $archive_description = $url_params['search'] . 'に関する補助金・助成金コラム記事の検索結果です。';
} else {
    $archive_title = '補助金・助成金コラム';
    $archive_description = '補助金・助成金の活用ノウハウ、申請のコツ、最新情報をお届けする専門コラムです。初心者から上級者まで役立つ情報を毎日更新。';
}

// カテゴリデータの取得
$all_categories = get_terms([
    'taxonomy' => 'column_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
]);

if (is_wp_error($all_categories)) {
    $all_categories = array();
}

// タグデータの取得
$all_tags = get_terms([
    'taxonomy' => 'column_tag',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 20
]);

if (is_wp_error($all_tags)) {
    $all_tags = array();
}

// SEO対策データ
$current_year = date('Y');
$current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
$canonical_url = $current_url;

// 総件数
$total_columns = wp_count_posts('column')->publish;
$total_columns_formatted = number_format($total_columns);

// サイドバー用：新着トピックス
$recent_columns = new WP_Query([
    'post_type' => 'column',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
]);

// サイドバー用：人気記事ランキング
$has_view_count = get_posts(array(
    'post_type' => 'column',
    'posts_per_page' => 1,
    'meta_key' => 'view_count',
    'fields' => 'ids',
));

$ranking_args = array(
    'post_type' => 'column',
    'posts_per_page' => 10,
    'post_status' => 'publish',
);

if (!empty($has_view_count)) {
    $ranking_args['meta_key'] = 'view_count';
    $ranking_args['orderby'] = 'meta_value_num';
    $ranking_args['order'] = 'DESC';
} else {
    $ranking_args['orderby'] = 'date';
    $ranking_args['order'] = 'DESC';
}

$ranking_query = new WP_Query($ranking_args);

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => 'コラム', 'url' => get_post_type_archive_link('column')]
];

if ($is_category_archive || $is_tag_archive) {
    $breadcrumbs[] = ['name' => $current_category->name, 'url' => ''];
} elseif (!empty($url_params['search'])) {
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
    ],
    'mainEntity' => [
        '@type' => 'ItemList',
        'name' => $archive_title,
        'numberOfItems' => $total_columns,
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

// カテゴリアイコン取得関数（SVGアイコン版）
if (!function_exists('gi_get_column_category_svg_icon')) {
    function gi_get_column_category_svg_icon($slug) {
        $icons = array(
            'application-tips' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
            'system-explanation' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
            'news' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/></svg>',
            'success-stories' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>',
            'beginner' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
            'advanced' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
            'other' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
        );
        return isset($icons[$slug]) ? $icons[$slug] : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
    }
}

// 難易度ラベル取得関数
if (!function_exists('gi_get_column_difficulty_label')) {
    function gi_get_column_difficulty_label($difficulty) {
        $labels = array(
            'beginner' => '初心者向け',
            'intermediate' => '中級者向け',
            'advanced' => '上級者向け',
        );
        return isset($labels[$difficulty]) ? $labels[$difficulty] : '初心者向け';
    }
}

// 難易度カラー取得関数
if (!function_exists('gi_get_column_difficulty_color')) {
    function gi_get_column_difficulty_color($difficulty) {
        $colors = array(
            'beginner' => 'green',
            'intermediate' => 'orange',
            'advanced' => 'red',
        );
        return isset($colors[$difficulty]) ? $colors[$difficulty] : 'green';
    }
}

// 難易度SVGアイコン取得関数
if (!function_exists('gi_get_column_difficulty_svg_icon')) {
    function gi_get_column_difficulty_svg_icon($difficulty) {
        $icons = array(
            'beginner' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
            'intermediate' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
            'advanced' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
        );
        return isset($icons[$difficulty]) ? $icons[$difficulty] : $icons['beginner'];
    }
}
?>

<!-- 構造化データ: CollectionPage -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main class="column-archive-yahoo-style" 
      id="column-archive" 
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
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <span>補助金・助成金コラム</span>
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
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <strong itemprop="value"><?php echo $total_columns_formatted; ?></strong>
                        <span itemprop="unitText">記事</span>
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
                    <form method="get" action="<?php echo esc_url(get_post_type_archive_link('column')); ?>" class="search-form-wrapper">
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
                                   name="s"
                                   class="search-input" 
                                   placeholder="コラム記事を検索..."
                                   value="<?php echo esc_attr($url_params['search']); ?>"
                                   aria-label="コラムを検索"
                                   autocomplete="off">
                            <input type="hidden" name="post_type" value="column">
                            <?php if (!empty($url_params['search'])): ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('column')); ?>" 
                               class="search-clear-btn" 
                               aria-label="検索をクリア">×</a>
                            <?php endif; ?>
                            <button class="search-execute-btn" 
                                    type="submit"
                                    aria-label="検索を実行">検索</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- カテゴリータブ -->
            <?php if (!empty($all_categories)): ?>
            <nav class="yahoo-category-tabs" aria-label="カテゴリー選択">
                <ul class="category-tabs-list">
                    <li>
                        <a href="<?php echo esc_url(get_post_type_archive_link('column')); ?>" 
                           class="category-tab <?php echo (!$is_category_archive && !$is_tag_archive && empty($url_params['search'])) ? 'active' : ''; ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="8" y1="6" x2="21" y2="6"/>
                                <line x1="8" y1="12" x2="21" y2="12"/>
                                <line x1="8" y1="18" x2="21" y2="18"/>
                                <line x1="3" y1="6" x2="3.01" y2="6"/>
                                <line x1="3" y1="12" x2="3.01" y2="12"/>
                                <line x1="3" y1="18" x2="3.01" y2="18"/>
                            </svg>
                            すべて
                            <span class="tab-count"><?php echo number_format($total_columns); ?></span>
                        </a>
                    </li>
                    <?php foreach ($all_categories as $cat): 
                        $is_active = ($is_category_archive && $current_category && $current_category->term_id === $cat->term_id);
                    ?>
                        <li>
                            <a href="<?php echo esc_url(get_term_link($cat)); ?>" 
                               class="category-tab <?php echo $is_active ? 'active' : ''; ?>">
                                <span class="tab-icon"><?php echo gi_get_column_category_svg_icon($cat->slug); ?></span>
                                <?php echo esc_html($cat->name); ?>
                                <span class="tab-count"><?php echo number_format($cat->count); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <!-- モバイル用フィルター開閉ボタン -->
            <button class="mobile-filter-toggle" id="mobile-filter-toggle" type="button" aria-label="フィルターを開く">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span>絞り込み</span>
            </button>

            <!-- フィルターパネル背景オーバーレイ -->
            <div class="filter-panel-overlay" id="filter-panel-overlay"></div>

            <!-- フィルターセクション（修正版） -->
            <section class="yahoo-filter-section" id="filter-panel" 
                     role="search" 
                     aria-label="コラム検索フィルター">
                
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

                <!-- フィルターグリッド -->
                <div class="yahoo-filters-grid">
                    
                    <!-- カテゴリ選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="category-label">カテゴリ</label>
                        <div class="custom-select" 
                             id="category-select" 
                             role="combobox" 
                             aria-labelledby="category-label" 
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
                                <?php foreach ($all_categories as $category): ?>
                                    <div class="select-option" 
                                         data-value="<?php echo esc_attr($category->slug); ?>"
                                         role="option">
                                        <span class="option-icon"><?php echo gi_get_column_category_svg_icon($category->slug); ?></span>
                                        <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 難易度選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="difficulty-label">難易度</label>
                        <div class="custom-select" 
                             id="difficulty-select" 
                             role="combobox" 
                             aria-labelledby="difficulty-label" 
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
                                     data-value="beginner" 
                                     role="option">
                                    <span class="option-icon"><?php echo gi_get_column_difficulty_svg_icon('beginner'); ?></span>
                                    初心者向け
                                </div>
                                <div class="select-option" 
                                     data-value="intermediate" 
                                     role="option">
                                    <span class="option-icon"><?php echo gi_get_column_difficulty_svg_icon('intermediate'); ?></span>
                                    中級者向け
                                </div>
                                <div class="select-option" 
                                     data-value="advanced" 
                                     role="option">
                                    <span class="option-icon"><?php echo gi_get_column_difficulty_svg_icon('advanced'); ?></span>
                                    上級者向け
                                </div>
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
                                     data-value="popular_desc" 
                                     role="option">人気順</div>
                                <div class="select-option" 
                                     data-value="modified_desc" 
                                     role="option">更新順</div>
                                <div class="select-option" 
                                     data-value="title_asc" 
                                     role="option">タイトル順</div>
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
                        <h2 class="results-title">
                            <?php if (!empty($url_params['search'])): ?>
                                「<?php echo esc_html($url_params['search']); ?>」の検索結果
                            <?php else: ?>
                                コラム記事一覧
                            <?php endif; ?>
                        </h2>
                        <div class="results-meta">
                            <span class="total-count">
                                <strong id="current-count"><?php echo number_format($total_columns); ?></strong>件
                            </span>
                        </div>
                    </div>

                    <div class="view-controls">
                        <button class="view-btn active" 
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
                        <button class="view-btn" 
                                data-view="list" 
                                title="リスト表示" 
                                type="button">
                            <svg width="18" 
                                 height="18" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true">
                                <rect x="3" y="4" width="18" height="4"/>
                                <rect x="3" y="10" width="18" height="4"/>
                                <rect x="3" y="16" width="18" height="4"/>
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
                        <p class="loading-text">読み込み中...</p>
                    </div>
                </div>

                <!-- コラム表示エリア -->
                <div class="columns-container-yahoo" 
                     id="columns-container" 
                     data-view="grid">
                    <?php
                    // WP_Queryの引数を構築
                    $query_args = array(
                        'post_type' => 'column',
                        'posts_per_page' => $posts_per_page,
                        'post_status' => 'publish',
                        'paged' => $paged,
                    );
                    
                    // カテゴリーフィルタ
                    if ($is_category_archive && $current_category) {
                        $query_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'column_category',
                                'field' => 'term_id',
                                'terms' => $current_category->term_id,
                            ),
                        );
                    }
                    
                    // タグフィルタ
                    if ($is_tag_archive && $current_category) {
                        $query_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'column_tag',
                                'field' => 'term_id',
                                'terms' => $current_category->term_id,
                            ),
                        );
                    }
                    
                    // 検索キーワード
                    if (!empty($url_params['search'])) {
                        $query_args['s'] = $url_params['search'];
                    }
                    
                    // 難易度フィルタ
                    if (!empty($url_params['difficulty'])) {
                        $query_args['meta_query'] = array(
                            array(
                                'key' => 'difficulty_level',
                                'value' => $url_params['difficulty'],
                                'compare' => '='
                            )
                        );
                    }
                    
                    // ソート順の設定
                    if (!empty($url_params['orderby'])) {
                        switch ($url_params['orderby']) {
                            case 'popular':
                                $query_args['meta_key'] = 'view_count';
                                $query_args['orderby'] = 'meta_value_num';
                                $query_args['order'] = 'DESC';
                                break;
                            case 'modified':
                                $query_args['orderby'] = 'modified';
                                $query_args['order'] = 'DESC';
                                break;
                            case 'title':
                                $query_args['orderby'] = 'title';
                                $query_args['order'] = 'ASC';
                                break;
                            default:
                                $query_args['orderby'] = 'date';
                                $query_args['order'] = 'DESC';
                        }
                    } else {
                        $query_args['orderby'] = 'date';
                        $query_args['order'] = 'DESC';
                    }
                    
                    // クエリ実行
                    $columns_query = new WP_Query($query_args);
                    
                    if ($columns_query->have_posts()) :
                        while ($columns_query->have_posts()) : 
                            $columns_query->the_post();
                            $post_id = get_the_ID();
                            $view_count = get_post_meta($post_id, 'view_count', true) ?: 0;
                            $read_time = get_post_meta($post_id, 'estimated_read_time', true) ?: 5;
                            $difficulty = get_post_meta($post_id, 'difficulty_level', true) ?: 'beginner';
                            $column_status = get_post_meta($post_id, 'column_status', true);
                            $is_featured = ($column_status === 'featured');
                            $post_categories = get_the_terms($post_id, 'column_category');
                            ?>
                            <article class="column-card <?php echo $is_featured ? 'column-card-featured' : ''; ?>" 
                                     itemscope 
                                     itemtype="https://schema.org/Article">
                                
                                <!-- サムネイル -->
                                <a href="<?php the_permalink(); ?>" class="column-card-thumb" itemprop="url">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php the_post_thumbnail('medium', array(
                                            'loading' => 'lazy', 
                                            'alt' => get_the_title(),
                                            'itemprop' => 'image'
                                        )); ?>
                                    <?php else: ?>
                                        <div class="thumb-placeholder">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($is_featured): ?>
                                        <span class="featured-label">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            特集
                                        </span>
                                    <?php endif; ?>
                                </a>
                                
                                <!-- コンテンツ -->
                                <div class="column-card-body">
                                    <div class="column-card-header">
                                        <?php if ($post_categories && !is_wp_error($post_categories)): ?>
                                            <a href="<?php echo esc_url(get_term_link($post_categories[0])); ?>" class="column-card-cat">
                                                <span class="cat-icon"><?php echo gi_get_column_category_svg_icon($post_categories[0]->slug); ?></span>
                                                <?php echo esc_html($post_categories[0]->name); ?>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <span class="column-card-time">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <?php echo esc_html($read_time); ?>分
                                        </span>
                                    </div>
                                    
                                    <h3 class="column-card-title" itemprop="headline">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <p class="column-card-excerpt" itemprop="description">
                                        <?php echo wp_trim_words(get_the_excerpt(), 40, '...'); ?>
                                    </p>
                                    
                                    <div class="column-card-footer">
                                        <time class="column-card-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                            <?php echo get_the_date('Y.m.d'); ?>
                                        </time>
                                        
                                        <div class="column-card-stats">
                                            <?php if ($view_count > 0): ?>
                                                <span class="stat-views">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    <?php echo number_format($view_count); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <span class="stat-level stat-level-<?php echo esc_attr(gi_get_column_difficulty_color($difficulty)); ?>">
                                                <?php echo gi_get_column_difficulty_svg_icon($difficulty); ?>
                                                <?php echo gi_get_column_difficulty_label($difficulty); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="no-results-message">
                            <svg class="no-results-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <h3>該当するコラム記事が見つかりませんでした</h3>
                            <p>検索条件を変更して再度お試しください。</p>
                            <a href="<?php echo esc_url(get_post_type_archive_link('column')); ?>" class="back-to-all-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="19" y1="12" x2="5" y2="12"/>
                                    <polyline points="12 19 5 12 12 5"/>
                                </svg>
                                すべての記事を見る
                            </a>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>

                <!-- ページネーション -->
                <?php if (isset($columns_query) && $columns_query->max_num_pages > 1): ?>
                <nav class="pagination-wrapper" id="pagination-wrapper" aria-label="ページナビゲーション">
                    <?php
                    $big = 999999999;
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, $paged),
                        'total' => $columns_query->max_num_pages,
                        'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg> 前へ',
                        'next_text' => '次へ <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>',
                        'mid_size' => 2,
                        'end_size' => 1,
                        'type' => 'plain',
                    ));
                    ?>
                </nav>
                <?php endif; ?>
            </section>
        </div>

        <!-- サイドバー（PC only） -->
        <aside class="yahoo-sidebar" role="complementary" aria-label="サイドバー">
            
            <!-- 広告枠1: サイドバー上部 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-top">
                <?php ji_display_ad('archive_column_sidebar_top', 'archive-column'); ?>
            </div>
            <?php endif; ?>

            <!-- アクセスランキング -->
            <?php if ($ranking_query->have_posts()): ?>
            <section class="sidebar-widget sidebar-ranking">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        <polyline points="17 6 23 6 23 12"/>
                    </svg>
                    人気記事ランキング
                </h3>
                
                <div class="widget-content">
                    <ol class="ranking-list">
                        <?php 
                        $rank = 1;
                        while ($ranking_query->have_posts()): $ranking_query->the_post();
                            $views = get_post_meta(get_the_ID(), 'view_count', true) ?: 0;
                        ?>
                            <li class="ranking-item rank-<?php echo $rank; ?> <?php echo $rank <= 3 ? 'top-rank' : ''; ?>">
                                <a href="<?php the_permalink(); ?>" class="ranking-link">
                                    <span class="ranking-number"><?php echo $rank; ?></span>
                                    <div class="ranking-content">
                                        <?php if (has_post_thumbnail() && $rank <= 3): ?>
                                        <div class="ranking-thumb">
                                            <?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="ranking-text">
                                            <span class="ranking-title">
                                                <?php the_title(); ?>
                                            </span>
                                            <span class="ranking-meta">
                                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('n/j'); ?></time>
                                                <?php if ($views > 0): ?>
                                                    <span class="ranking-views">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                        <?php echo number_format($views); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php 
                            $rank++;
                        endwhile; 
                        wp_reset_postdata();
                        ?>
                    </ol>
                </div>
            </section>
            <?php endif; ?>

            <!-- 広告枠2: サイドバー中央 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-middle">
                <?php ji_display_ad('archive_column_sidebar_middle', 'archive-column'); ?>
            </div>
            <?php endif; ?>

            <!-- 新着トピックス -->
            <?php if ($recent_columns->have_posts()): ?>
            <section class="sidebar-widget sidebar-topics">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/>
                        <line x1="10" y1="1" x2="10" y2="4"/>
                        <line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                    新着コラム
                </h3>
                <div class="widget-content">
                    <ul class="topics-list">
                        <?php while ($recent_columns->have_posts()): $recent_columns->the_post(); ?>
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
                </div>
            </section>
            <?php endif; ?>

            <!-- トレンドタグ -->
            <?php if (!empty($all_tags)): ?>
            <section class="sidebar-widget sidebar-tags">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                    </svg>
                    人気タグ
                </h3>
                <div class="widget-content">
                    <div class="tags-cloud">
                        <?php foreach ($all_tags as $index => $tag): ?>
                            <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="tag-item">
                                <span class="tag-rank"><?php echo ($index + 1); ?></span>
                                <span class="tag-name"><?php echo esc_html($tag->name); ?></span>
                                <span class="tag-count"><?php echo number_format($tag->count); ?>件</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- 広告枠3: サイドバー下部 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-bottom">
                <?php ji_display_ad('archive_column_sidebar_bottom', 'archive-column'); ?>
            </div>
            <?php endif; ?>

            <!-- カテゴリ一覧 -->
            <?php if (!empty($all_categories)): ?>
            <section class="sidebar-widget sidebar-categories">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                    カテゴリ
                </h3>
                <div class="widget-content">
                    <ul class="categories-list">
                        <?php foreach ($all_categories as $category): ?>
                            <li class="categories-item">
                                <a href="<?php echo esc_url(get_term_link($category)); ?>" class="categories-link">
                                    <span class="category-icon"><?php echo gi_get_column_category_svg_icon($category->slug); ?></span>
                                    <span class="category-name"><?php echo esc_html($category->name); ?></span>
                                    <span class="categories-count">(<?php echo $category->count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
            <?php endif; ?>

            <!-- 助成金検索CTA -->
            <section class="sidebar-widget sidebar-cta">
                <h3 class="cta-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    助成金を探す
                </h3>
                <p class="cta-desc">コラムで学んだ知識を活かして、あなたに最適な助成金を見つけましょう。</p>
                <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="cta-btn">
                    助成金検索へ
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            </section>
        </aside>
    </div>

</main>

<!-- CSS（修正版：絞り込み表示領域 + SVGアイコン対応） -->
<style>
/* ==========================================================================
   Column Archive - Government Official Design v20.0
   コラムアーカイブページ - 官公庁風デザイン
   修正版：絞り込み表示領域 + SVGアイコン対応
   ========================================================================== */

/* ==========================================================================
   CSS Variables - 官公庁カラーパレット
   ========================================================================== */
:root {
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
    
    --gov-gold: #c9a227;
    --gov-gold-light: #d4b77a;
    --gov-gold-pale: #f0e6c8;
    
    --gov-green: #2e7d32;
    --gov-green-light: #e8f5e9;
    --gov-red: #c62828;
    --gov-red-light: #ffebee;
    --gov-blue: #1565c0;
    --gov-blue-light: #e3f2fd;
    --gov-orange: #ef6c00;
    --gov-orange-light: #fff3e0;
    
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
    
    --gov-font-serif: "Shippori Mincho", "Yu Mincho", serif;
    --gov-font-sans: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    --gov-font-mono: 'SF Mono', 'Monaco', monospace;
    
    --gov-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
}

/* ==========================================================================
   Base Styles
   ========================================================================== */
.column-archive-yahoo-style {
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    line-height: 1.7;
    position: relative;
}

.column-archive-yahoo-style::before {
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
   Breadcrumb
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
   Hero Section
   ========================================================================== */
.yahoo-hero-section {
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%);
    color: var(--gov-white);
    padding: 48px 0;
    border-bottom: 4px solid var(--gov-gold);
    position: relative;
    overflow: hidden;
}

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

.yahoo-main-title {
    font-family: var(--gov-font-serif);
    font-size: clamp(28px, 5vw, 44px);
    font-weight: 700;
    color: var(--gov-white);
    margin: 0 0 16px 0;
    line-height: 1.3;
}

.yahoo-lead-text {
    font-size: 15px;
    color: var(--gov-navy-200);
    margin: 0 0 24px 0;
    line-height: 1.9;
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
}

/* ==========================================================================
   Two Column Layout
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
   Search Section
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
    padding: 8px 12px;
    cursor: pointer;
    font-size: 20px;
    text-decoration: none;
    transition: color var(--gov-transition);
}

.search-clear-btn:hover {
    color: var(--gov-gray-700);
}

.search-execute-btn {
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

/* ==========================================================================
   Category Tabs
   ========================================================================== */
.yahoo-category-tabs {
    margin-bottom: 24px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.category-tabs-list {
    display: flex;
    gap: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
    border-bottom: 2px solid var(--gov-navy-800);
}

.category-tabs-list li {
    flex-shrink: 0;
}

.category-tab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--gov-gray-700);
    background: var(--gov-gray-100);
    border: 1px solid var(--gov-gray-200);
    border-bottom: none;
    text-decoration: none;
    transition: all var(--gov-transition);
    white-space: nowrap;
}

.category-tab:hover {
    background: var(--gov-white);
    color: var(--gov-navy-900);
}

.category-tab.active {
    color: var(--gov-navy-900);
    background: var(--gov-white);
    border-color: var(--gov-navy-800);
    position: relative;
}

.category-tab.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--gov-white);
}

/* SVGアイコン用スタイル */
.tab-icon,
.cat-icon,
.option-icon,
.category-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tab-icon svg,
.cat-icon svg,
.option-icon svg,
.category-icon svg {
    width: 16px;
    height: 16px;
}

.tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 700;
    color: var(--gov-white);
    background: var(--gov-gray-500);
    border-radius: 10px;
}

.category-tab.active .tab-count {
    background: var(--gov-navy-800);
}

/* ==========================================================================
   Filter Section - 修正版
   ========================================================================== */
.yahoo-filter-section {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    margin-bottom: 24px;
    box-shadow: var(--gov-shadow-sm);
    overflow: visible; /* 重要：ドロップダウンが見えるように */
    position: relative;
    z-index: 50;
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
}

.yahoo-filters-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    padding: 24px;
    position: relative;
    z-index: 51;
}

.filter-dropdown-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: relative;
    z-index: 52;
}

.filter-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--gov-navy-700);
}

/* ==========================================================================
   Custom Select - 修正版
   ========================================================================== */
.custom-select {
    position: relative;
    width: 100%;
    z-index: 53;
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
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000; /* 高いz-indexを設定 */
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

.select-option .option-icon {
    flex-shrink: 0;
    color: var(--gov-navy-600);
}

/* ==========================================================================
   Active Filters
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
    font-size: 14px;
    opacity: 0.7;
    transition: opacity var(--gov-transition);
}

.filter-tag-remove:hover {
    opacity: 1;
}

/* ==========================================================================
   Results Section
   ========================================================================== */
.yahoo-results-section {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    box-shadow: var(--gov-shadow-sm);
    overflow: hidden;
    position: relative;
    z-index: 1;
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
    font-size: 14px;
    color: var(--gov-gray-600);
}

.total-count strong {
    font-family: var(--gov-font-mono);
    font-size: 20px;
    font-weight: 900;
    color: var(--gov-navy-900);
}

/* View Controls */
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

/* ==========================================================================
   Columns Container
   ========================================================================== */
.columns-container-yahoo {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    padding: 24px;
    min-height: 400px;
}

.columns-container-yahoo[data-view="list"] {
    grid-template-columns: 1fr;
    gap: 16px;
}

/* ==========================================================================
   Column Card
   ========================================================================== */
.column-card {
    display: flex;
    flex-direction: column;
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    overflow: hidden;
    transition: all var(--gov-transition);
}

.column-card:hover {
    box-shadow: var(--gov-shadow);
    transform: translateY(-2px);
}

.column-card-featured {
    border-color: var(--gov-green);
    border-width: 2px;
}

.columns-container-yahoo[data-view="list"] .column-card {
    flex-direction: row;
}

.column-card-thumb {
    position: relative;
    display: block;
    aspect-ratio: 16 / 9;
    background: var(--gov-gray-100);
    overflow: hidden;
}

.columns-container-yahoo[data-view="list"] .column-card-thumb {
    width: 200px;
    flex-shrink: 0;
    aspect-ratio: auto;
    height: 100%;
}

.column-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--gov-transition);
}

.column-card:hover .column-card-thumb img {
    transform: scale(1.05);
}

.thumb-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: var(--gov-gray-300);
}

.featured-label {
    position: absolute;
    top: 10px;
    left: 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: var(--gov-green);
    color: var(--gov-white);
    font-size: 11px;
    font-weight: 700;
    border-radius: var(--gov-radius);
}

.column-card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 16px;
}

.column-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.column-card-cat {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    background: var(--gov-gray-100);
    color: var(--gov-gray-700);
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
}

.column-card-cat:hover {
    background: var(--gov-gray-200);
    color: var(--gov-navy-900);
}

.column-card-cat .cat-icon {
    display: inline-flex;
}

.column-card-cat .cat-icon svg {
    width: 12px;
    height: 12px;
}

.column-card-time {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 11px;
    color: var(--gov-gray-500);
}

.column-card-title {
    margin: 0 0 10px;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
}

.column-card-title a {
    color: var(--gov-navy-900);
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color var(--gov-transition);
}

.column-card-title a:hover {
    color: var(--gov-blue);
}

.column-card-excerpt {
    flex: 1;
    margin: 0 0 12px;
    font-size: 13px;
    color: var(--gov-gray-600);
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.column-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid var(--gov-gray-200);
}

.column-card-date {
    font-size: 12px;
    color: var(--gov-gray-500);
}

.column-card-stats {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stat-views {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 11px;
    color: var(--gov-gray-500);
}

.stat-level {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 600;
    border-radius: var(--gov-radius);
}

.stat-level svg {
    width: 12px;
    height: 12px;
}

.stat-level-green {
    background: var(--gov-green-light);
    color: var(--gov-green);
}

.stat-level-orange {
    background: var(--gov-orange-light);
    color: var(--gov-orange);
}

.stat-level-red {
    background: var(--gov-red-light);
    color: var(--gov-red);
}

/* ==========================================================================
   No Results
   ========================================================================== */
.no-results-message {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 24px;
    text-align: center;
}

.no-results-message .no-results-icon {
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

.no-results-message h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--gov-navy-900);
    margin: 0 0 12px 0;
}

.no-results-message p {
    font-size: 14px;
    color: var(--gov-gray-600);
    margin: 0 0 24px 0;
}

.back-to-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: var(--gov-navy-800);
    color: var(--gov-white);
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
}

.back-to-all-btn:hover {
    background: var(--gov-navy-900);
    transform: translateY(-2px);
}

/* ==========================================================================
   Loading
   ========================================================================== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--gov-gray-200);
    border-top-color: var(--gov-navy-800);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    font-size: 14px;
    color: var(--gov-gray-600);
    font-weight: 600;
    margin: 0;
}

/* ==========================================================================
   Pagination
   ========================================================================== */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    padding: 24px;
    border-top: 1px solid var(--gov-gray-200);
    background: var(--gov-navy-50);
    flex-wrap: wrap;
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
    gap: 4px;
}

.pagination-wrapper .page-numbers:hover {
    border-color: var(--gov-navy-400);
    color: var(--gov-navy-800);
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
}

/* ==========================================================================
   Sidebar
   ========================================================================== */
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

/* Ranking */
.ranking-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.ranking-item {
    border-bottom: 1px solid var(--gov-gray-100);
}

.ranking-item:last-child {
    border-bottom: none;
}

.ranking-link {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 0;
    text-decoration: none;
    color: inherit;
    transition: background var(--gov-transition);
}

.ranking-link:hover {
    background: var(--gov-navy-50);
    margin: 0 -20px;
    padding: 14px 20px;
}

.ranking-number {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--gov-font-mono);
    font-size: 13px;
    font-weight: 900;
    color: var(--gov-gray-500);
    background: var(--gov-gray-100);
    border-radius: 50%;
}

.rank-1 .ranking-number {
    background: linear-gradient(135deg, var(--gov-gold) 0%, var(--gov-gold-light) 100%);
    color: var(--gov-navy-900);
}

.rank-2 .ranking-number {
    background: var(--gov-navy-300);
    color: var(--gov-navy-900);
}

.rank-3 .ranking-number {
    background: var(--gov-navy-400);
    color: var(--gov-white);
}

.ranking-content {
    flex: 1;
    min-width: 0;
    display: flex;
    gap: 10px;
}

.ranking-thumb {
    width: 50px;
    height: 40px;
    flex-shrink: 0;
    border-radius: var(--gov-radius);
    overflow: hidden;
    background: var(--gov-gray-100);
}

.ranking-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ranking-text {
    flex: 1;
    min-width: 0;
}

.ranking-title {
    display: block;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.5;
    color: var(--gov-navy-900);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ranking-link:hover .ranking-title {
    color: var(--gov-blue);
}

.ranking-meta {
    display: flex;
    gap: 8px;
    font-size: 11px;
    color: var(--gov-gray-500);
    margin-top: 4px;
}

.ranking-views {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Topics */
.topics-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.topics-item {
    border-bottom: 1px solid var(--gov-gray-100);
    padding-bottom: 14px;
    margin-bottom: 14px;
}

.topics-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.topics-link {
    display: flex;
    flex-direction: column;
    gap: 4px;
    text-decoration: none;
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
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color var(--gov-transition);
}

/* Tags */
.tags-cloud {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.tag-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: var(--gov-gray-50);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius);
    text-decoration: none;
    transition: all var(--gov-transition);
}

.tag-item:hover {
    background: var(--gov-gray-100);
    border-color: var(--gov-navy-400);
    transform: translateX(4px);
}

.tag-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    font-size: 11px;
    font-weight: 800;
    color: var(--gov-white);
    background: var(--gov-gray-400);
    border-radius: 10px;
}

.tag-item:nth-child(-n+3) .tag-rank {
    background: var(--gov-navy-800);
}

.tag-name {
    flex: 1;
    font-size: 13px;
    font-weight: 600;
    color: var(--gov-navy-900);
}

.tag-count {
    font-size: 11px;
    color: var(--gov-gray-500);
}

/* Categories */
.categories-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.categories-item {
    border-bottom: 1px solid var(--gov-gray-100);
}

.categories-item:last-child {
    border-bottom: none;
}

.categories-link {
    display: flex;
    align-items: center;
    gap: 8px;
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

.categories-link .category-icon {
    display: inline-flex;
    color: var(--gov-navy-600);
}

.categories-link .category-icon svg {
    width: 16px;
    height: 16px;
}

.category-name {
    flex: 1;
}

.categories-count {
    font-size: 12px;
    font-family: var(--gov-font-mono);
    color: var(--gov-gray-500);
    background: var(--gov-gray-100);
    padding: 2px 8px;
    border-radius: 12px;
}

/* CTA */
.sidebar-cta {
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-900) 100%) !important;
    color: var(--gov-white);
    padding: 24px 20px;
    text-align: center;
    border: none !important;
}

.cta-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-family: var(--gov-font-serif);
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 12px;
    color: var(--gov-white);
    background: none !important;
    padding: 0 !important;
    border: none !important;
}

.cta-title svg {
    color: var(--gov-gold);
}

.cta-desc {
    font-size: 13px;
    opacity: 0.9;
    margin: 0 0 20px;
    line-height: 1.7;
}

.cta-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 14px 24px;
    background: var(--gov-white);
    color: var(--gov-navy-900);
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
}

.cta-btn:hover {
    background: var(--gov-gray-100);
    transform: translateY(-2px);
}

/* ==========================================================================
   Mobile Filter
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
}

/* ==========================================================================
   Responsive
   ========================================================================== */
@media (max-width: 1024px) {
    .yahoo-two-column-layout {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .yahoo-sidebar {
        display: none;
    }
    
    .columns-container-yahoo {
        grid-template-columns: 1fr;
    }
    
    .columns-container-yahoo[data-view="list"] .column-card {
        flex-direction: column;
    }
    
    .columns-container-yahoo[data-view="list"] .column-card-thumb {
        width: 100%;
        aspect-ratio: 16 / 9;
        height: auto;
    }
    
    .yahoo-filters-grid {
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
    }
    
    .yahoo-filters-grid {
        padding-bottom: 100px;
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
    
    .yahoo-category-tabs {
        margin: 0 -16px 24px -16px;
        padding: 0 16px;
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
    
    .columns-container-yahoo {
        padding: 16px;
    }
}

/* ==========================================================================
   Accessibility
   ========================================================================== */
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

.column-archive-yahoo-style a:focus-visible,
.column-archive-yahoo-style button:focus-visible,
.column-archive-yahoo-style input:focus-visible {
    outline: 3px solid var(--gov-gold);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}

/* ==========================================================================
   Print
   ========================================================================== */
@media print {
    .yahoo-search-section,
    .yahoo-filter-section,
    .yahoo-category-tabs,
    .view-controls,
    .pagination-wrapper,
    .yahoo-sidebar,
    .mobile-filter-toggle {
        display: none !important;
    }
    
    .yahoo-two-column-layout {
        grid-template-columns: 1fr;
    }
}

/* ==========================================================================
   Force Display
   ========================================================================== */
.column-archive-yahoo-style,
.yahoo-container,
.yahoo-two-column-layout,
.yahoo-main-content,
.yahoo-results-section,
.columns-container-yahoo {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.yahoo-two-column-layout {
    display: grid !important;
}

.columns-container-yahoo {
    display: grid !important;
}
</style>

<!-- JavaScript -->
<script>
(function() {
    'use strict';
    
    console.log('🚀 Column Archive v20.0 - Professional Icons Edition');
    
    // カスタムセレクト機能
    function setupCustomSelects() {
        const selects = document.querySelectorAll('.custom-select');
        
        selects.forEach(select => {
            const trigger = select.querySelector('.select-trigger');
            const dropdown = select.querySelector('.select-dropdown');
            const options = select.querySelectorAll('.select-option');
            const valueSpan = select.querySelector('.select-value');
            
            if (!trigger || !dropdown) return;
            
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isActive = select.classList.contains('active');
                
                // 他のセレクトを閉じる
                document.querySelectorAll('.custom-select.active').forEach(s => {
                    if (s !== select) {
                        s.classList.remove('active');
                        s.querySelector('.select-dropdown').style.display = 'none';
                    }
                });
                
                if (isActive) {
                    select.classList.remove('active');
                    dropdown.style.display = 'none';
                } else {
                    select.classList.add('active');
                    dropdown.style.display = 'block';
                }
            });
            
            options.forEach(option => {
                option.addEventListener('click', () => {
                    const value = option.dataset.value;
                    // アイコンを除いたテキストのみ取得
                    const textContent = option.cloneNode(true);
                    const iconElement = textContent.querySelector('.option-icon');
                    if (iconElement) iconElement.remove();
                    const text = textContent.textContent.trim();
                    
                    options.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    valueSpan.textContent = text;
                    
                    select.classList.remove('active');
                    dropdown.style.display = 'none';
                    
                    // PC時のみ自動適用
                    if (window.innerWidth > 768) {
                        applyFilters();
                    }
                });
            });
        });
        
        // ドキュメントクリックで閉じる
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-select')) {
                document.querySelectorAll('.custom-select.active').forEach(s => {
                    s.classList.remove('active');
                    s.querySelector('.select-dropdown').style.display = 'none';
                });
            }
        });
    }
    
    // 表示切替機能
    function setupViewToggle() {
        const viewBtns = document.querySelectorAll('.view-btn');
        const container = document.getElementById('columns-container');
        
        if (!container) return;
        
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                viewBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                container.setAttribute('data-view', this.dataset.view);
            });
        });
    }
    
    // モバイルフィルター機能
    function setupMobileFilter() {
        const toggle = document.getElementById('mobile-filter-toggle');
        const close = document.getElementById('mobile-filter-close');
        const panel = document.getElementById('filter-panel');
        const overlay = document.getElementById('filter-panel-overlay');
        const applyBtn = document.getElementById('mobile-apply-filters-btn');
        
        if (!toggle || !panel) return;
        
        function openFilter() {
            panel.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeFilter() {
            panel.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        toggle.addEventListener('click', openFilter);
        
        if (close) {
            close.addEventListener('click', closeFilter);
        }
        
        if (overlay) {
            overlay.addEventListener('click', closeFilter);
        }
        
        if (applyBtn) {
            applyBtn.addEventListener('click', () => {
                closeFilter();
                applyFilters();
            });
        }
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && panel.classList.contains('active')) {
                closeFilter();
            }
        });
    }
    
    // フィルター適用
    function applyFilters() {
        const categorySelect = document.querySelector('#category-select .select-option.active');
        const difficultySelect = document.querySelector('#difficulty-select .select-option.active');
        const sortSelect = document.querySelector('#sort-select .select-option.active');
        
        const params = new URLSearchParams(window.location.search);
        
        // 検索クエリを保持
        const searchQuery = document.querySelector('.search-input')?.value;
        if (searchQuery) {
            params.set('s', searchQuery);
        }
        
        // カテゴリ
        if (categorySelect && categorySelect.dataset.value) {
            params.set('category', categorySelect.dataset.value);
        } else {
            params.delete('category');
        }
        
        // 難易度
        if (difficultySelect && difficultySelect.dataset.value) {
            params.set('difficulty', difficultySelect.dataset.value);
        } else {
            params.delete('difficulty');
        }
        
        // ソート
        if (sortSelect && sortSelect.dataset.value) {
            const sortValue = sortSelect.dataset.value;
            if (sortValue === 'popular_desc') {
                params.set('orderby', 'popular');
            } else if (sortValue === 'modified_desc') {
                params.set('orderby', 'modified');
            } else if (sortValue === 'title_asc') {
                params.set('orderby', 'title');
            } else {
                params.delete('orderby');
            }
        }
        
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.location.href = newUrl;
    }
    
    // リセット機能
    function setupReset() {
        const resetBtn = document.getElementById('reset-all-filters-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                window.location.href = '<?php echo esc_url(get_post_type_archive_link('column')); ?>';
            });
        }
    }
    
    // アクティブフィルター表示管理
    function setupActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.has('category') || urlParams.has('difficulty') || urlParams.has('orderby') || urlParams.has('s');
        
        const activeFiltersContainer = document.getElementById('active-filters');
        const activeFilterTags = document.getElementById('active-filter-tags');
        const resetBtn = document.getElementById('reset-all-filters-btn');
        
        if (hasFilters && activeFiltersContainer && activeFilterTags) {
            activeFiltersContainer.style.display = 'flex';
            if (resetBtn) resetBtn.style.display = 'flex';
            
            let tagsHtml = '';
            
            // 検索キーワード
            if (urlParams.has('s') && urlParams.get('s')) {
                tagsHtml += `
                    <div class="filter-tag">
                        <span>検索: "${escapeHtml(urlParams.get('s'))}"</span>
                        <button class="filter-tag-remove" data-type="s" type="button">×</button>
                    </div>
                `;
            }
            
            // カテゴリ
            if (urlParams.has('category')) {
                const categorySlug = urlParams.get('category');
                const categoryOption = document.querySelector(`#category-select .select-option[data-value="${categorySlug}"]`);
                let categoryName = categorySlug;
                if (categoryOption) {
                    // アイコンを除いたテキストのみ取得
                    const cloned = categoryOption.cloneNode(true);
                    const iconEl = cloned.querySelector('.option-icon');
                    if (iconEl) iconEl.remove();
                    categoryName = cloned.textContent.trim().split('(')[0].trim();
                }
                tagsHtml += `
                    <div class="filter-tag">
                        <span>カテゴリ: ${escapeHtml(categoryName)}</span>
                        <button class="filter-tag-remove" data-type="category" type="button">×</button>
                    </div>
                `;
                
                // セレクトボックスの値も更新
                if (categoryOption) {
                    document.querySelectorAll('#category-select .select-option').forEach(opt => opt.classList.remove('active'));
                    categoryOption.classList.add('active');
                    const valueSpan = document.querySelector('#category-select .select-value');
                    if (valueSpan) valueSpan.textContent = categoryName;
                }
            }
            
            // 難易度
            if (urlParams.has('difficulty')) {
                const difficultyValue = urlParams.get('difficulty');
                const difficultyLabels = {
                    'beginner': '初心者向け',
                    'intermediate': '中級者向け',
                    'advanced': '上級者向け'
                };
                const difficultyLabel = difficultyLabels[difficultyValue] || difficultyValue;
                tagsHtml += `
                    <div class="filter-tag">
                        <span>難易度: ${escapeHtml(difficultyLabel)}</span>
                        <button class="filter-tag-remove" data-type="difficulty" type="button">×</button>
                    </div>
                `;
                
                // セレクトボックスの値も更新
                const difficultyOption = document.querySelector(`#difficulty-select .select-option[data-value="${difficultyValue}"]`);
                if (difficultyOption) {
                    document.querySelectorAll('#difficulty-select .select-option').forEach(opt => opt.classList.remove('active'));
                    difficultyOption.classList.add('active');
                    const valueSpan = document.querySelector('#difficulty-select .select-value');
                    if (valueSpan) valueSpan.textContent = difficultyLabel;
                }
            }
            
            // ソート順
            if (urlParams.has('orderby')) {
                const orderbyValue = urlParams.get('orderby');
                const orderbyLabels = {
                    'popular': '人気順',
                    'modified': '更新順',
                    'title': 'タイトル順'
                };
                const orderbyLabel = orderbyLabels[orderbyValue] || orderbyValue;
                tagsHtml += `
                    <div class="filter-tag">
                        <span>並び順: ${escapeHtml(orderbyLabel)}</span>
                        <button class="filter-tag-remove" data-type="orderby" type="button">×</button>
                    </div>
                `;
                
                // セレクトボックスの値も更新
                const sortMapping = {
                    'popular': 'popular_desc',
                    'modified': 'modified_desc',
                    'title': 'title_asc'
                };
                const sortValue = sortMapping[orderbyValue];
                if (sortValue) {
                    const sortOption = document.querySelector(`#sort-select .select-option[data-value="${sortValue}"]`);
                    if (sortOption) {
                        document.querySelectorAll('#sort-select .select-option').forEach(opt => opt.classList.remove('active'));
                        sortOption.classList.add('active');
                        const valueSpan = document.querySelector('#sort-select .select-value');
                        if (valueSpan) valueSpan.textContent = sortOption.textContent.trim();
                    }
                }
            }
            
            activeFilterTags.innerHTML = tagsHtml;
            
            // フィルタータグの削除ボタン
            activeFilterTags.querySelectorAll('.filter-tag-remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const params = new URLSearchParams(window.location.search);
                    params.delete(type);
                    
                    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    window.location.href = newUrl;
                });
            });
        }
    }
    
    // HTMLエスケープ関数
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
    
    // 初期化
    function init() {
        setupCustomSelects();
        setupViewToggle();
        setupMobileFilter();
        setupReset();
        setupActiveFilters();
        
        console.log('✅ Column Archive v20.0 - Fully Initialized');
    }
    
    // DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
</script>

<?php 
// モバイル検索モーダルを追加
if (function_exists('get_template_part')) {
    get_template_part('template-parts/sidebar/mobile-search-modal');
}

get_footer(); 
?>