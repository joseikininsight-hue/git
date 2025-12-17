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

// CSS/JS直接読み込み（functions.phpのwp_enqueue_scriptsが既に実行されている場合のフォールバック）
if (!wp_style_is('gi-archive-common', 'done')) {
    echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/assets/css/archive-common.css?ver=' . filemtime(get_template_directory() . '/assets/css/archive-common.css') . '" type="text/css">';
}
?>

<?php
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
                            <input type="text" 
                                   id="keyword-search" 
                                   name="s"
                                   class="search-input" 
                                   placeholder="コラム記事を検索（スペース区切りでAND検索）..."
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