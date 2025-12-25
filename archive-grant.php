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
// archive-zukan.css は archive-common.css に統合済み
$template_dir = get_template_directory();
$template_uri = get_template_directory_uri();
$css_file = $template_dir . '/assets/css/archive-common.css';
$js_file = $template_dir . '/assets/js/archive-common.js';
?>
<?php if (file_exists($css_file) && !wp_style_is('gi-archive-common', 'done')): ?>
<link rel="stylesheet" href="<?php echo esc_url($template_uri . '/assets/css/archive-common.css?ver=' . filemtime($css_file)); ?>" media="all">
<?php endif; ?>
<?php

// URLパラメータの取得と処理 - v40.0 Archive Integration
$url_params = array(
    'application_status' => isset($_GET['application_status']) ? sanitize_text_field($_GET['application_status']) : '',
    'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '',
    'target' => isset($_GET['target']) ? sanitize_text_field($_GET['target']) : '',
    'view' => isset($_GET['view']) ? sanitize_text_field($_GET['view']) : '',
    'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
    'category' => isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '',
    'prefecture' => isset($_GET['prefecture']) ? sanitize_text_field($_GET['prefecture']) : '',
    'municipality' => isset($_GET['municipality']) ? sanitize_text_field($_GET['municipality']) : '',
    'tag' => isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '',
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
} elseif (!empty($url_params['tag'])) {
    // URLパラメータのタグでフィルタリング
    $tag_term = get_term_by('slug', $url_params['tag'], 'grant_tag');
    if ($tag_term && !is_wp_error($tag_term)) {
        $archive_title = $tag_term->name . 'の助成金・補助金';
        $archive_description = $tag_term->name . 'に関連する助成金・補助金の一覧。最新の募集情報を毎日更新。';
    } else {
        $archive_title = '助成金・補助金総合検索';
        $archive_description = '全国の助成金・補助金情報を網羅的に検索。都道府県・市町村・業種・金額で詳細に絞り込み可能。専門家による申請サポート完備。毎日更新。';
    }
} elseif (!empty($url_params['municipality'])) {
    // URLパラメータの市町村でフィルタリング
    $municipality_term = get_term_by('slug', $url_params['municipality'], 'grant_municipality');
    if ($municipality_term && !is_wp_error($municipality_term)) {
        $archive_title = $municipality_term->name . 'の助成金・補助金';
        $archive_description = $municipality_term->name . 'の地域密着型助成金・補助金情報。市町村独自の支援制度から国の制度まで幅広く掲載。';
    } else {
        $archive_title = '助成金・補助金総合検索';
        $archive_description = '全国の助成金・補助金情報を網羅的に検索。都道府県・市町村・業種・金額で詳細に絞り込み可能。専門家による申請サポート完備。毎日更新。';
    }
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

<main class="grant-archive-yahoo-style zukan-archive" 
      id="grant-archive" 
      role="main"
      itemscope 
      itemtype="https://schema.org/CollectionPage">

    <!-- モバイル用フィルター -->
    <?php include(get_template_directory() . '/template-parts/archive/mobile-filter.php'); ?>

    <!-- シンプルなパンくずリスト -->
    <nav class="breadcrumb-nav book-breadcrumb" 
         aria-label="パンくずリスト" 
         itemscope 
         itemtype="https://schema.org/BreadcrumbList">
        <div class="yahoo-container">
            <div class="book-breadcrumb-inner">
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
                                <span itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                            </a>
                            <span class="book-breadcrumb-sep" aria-hidden="true">&gt;</span>
                        <?php else: ?>
                            <span class="book-breadcrumb-current" itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                        <?php endif; ?>
                        <meta itemprop="position" content="<?php echo $index + 1; ?>">
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </nav>

    <!-- ヒーローセクション - タイトルページ風 -->
    <section class="zukan-hero zukan-hero-simple">
        <div class="yahoo-container">
            <div class="zukan-hero-content">
                <span class="zukan-hero-label">Subsidy & Grant Archive</span>
                <h1 class="zukan-hero-title">
                    <?php echo esc_html($archive_title); ?><br>
                    <span class="zukan-hero-subtitle-text">令和<?php echo date('Y') - 2018; ?>年度版 図鑑</span>
                </h1>
                <p class="zukan-hero-description">
                    <?php echo esc_html($archive_description); ?>
                </p>
                <div class="ornament-line"><span>&#10086;</span></div>
                <div class="zukan-hero-stats-simple">
                    <span class="zukan-hero-stat">収録制度数：<strong><?php echo $total_grants_formatted; ?></strong>件以上</span>
                    <span class="zukan-hero-stat-divider">|</span>
                    <span class="zukan-hero-stat">毎日更新</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 2カラムレイアウト -->
    <div class="yahoo-container yahoo-two-column-layout zukan-two-column">
        
        <!-- メインコンテンツ -->
        <div class="yahoo-main-content zukan-main-content">
            
            <?php 
            // アーカイブSEOコンテンツ: イントロ（01傾向と対策）を先に表示
            if (function_exists('gi_output_archive_intro_content')) {
                gi_output_archive_intro_content();
            }
            
            // アーカイブSEOコンテンツ: おすすめ記事（02編集部選定）
            if (function_exists('gi_output_archive_featured_posts')) {
                gi_output_archive_featured_posts();
            }
            ?>
            
            <!-- 統合された検索結果ヘッダー -->
            <section class="gi-section results-header" id="list">
                <header class="gi-section-numbered-header">
                    <div class="gi-section-number-box">
                        <div class="gi-section-number-inner">
                            <span class="gi-section-number-label">Section</span>
                            <span class="gi-section-number-value">03</span>
                        </div>
                    </div>
                    <div class="gi-section-title-box">
                        <svg class="gi-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <h2 class="gi-section-title">補助金図鑑一覧</h2>
                        <span class="gi-section-en">Grant Archive</span>
                    </div>
                </header>
                <?php
                // ページネーション用の件数計算
                $current_page = get_query_var('paged') ? get_query_var('paged') : 1;
                $posts_per_page = 12;
                $showing_from = (($current_page - 1) * $posts_per_page) + 1;
                $showing_to = min($current_page * $posts_per_page, $total_grants);
                ?>
                <div class="results-range-display">
                    <div class="results-range-text">
                        <span class="total-count" id="current-count"><?php echo $total_grants_formatted; ?></span> 件中 
                        <span class="range-numbers" id="showing-from"><?php echo number_format($showing_from); ?></span>〜<span class="range-numbers" id="showing-to"><?php echo number_format($showing_to); ?></span> 件を表示
                    </div>
                    <div class="results-sort-select">
                        <label for="archive-sort-select">並び替え:</label>
                        <select id="archive-sort-select" onchange="window.location.href=this.value">
                            <?php
                            $current_orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
                            $sort_options = array(
                                '' => 'おすすめ順',
                                'new' => '新着順',
                                'deadline' => '締切が近い順',
                                'popular' => '人気順',
                            );
                            foreach ($sort_options as $value => $label) {
                                $url = add_query_arg('orderby', $value, remove_query_arg('orderby'));
                                if (empty($value)) {
                                    $url = remove_query_arg('orderby');
                                }
                                $selected = ($current_orderby === $value || (empty($current_orderby) && empty($value))) ? 'selected' : '';
                                echo '<option value="' . esc_url($url) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </section>
            
            <!-- カテゴリ絞り込みセクションは削除済み -->

            <!-- Results Section -->
            <section class="yahoo-results-section" id="grants-results-section">
                
                <?php if (!empty($url_params['view']) && $url_params['view'] === 'prefectures'): ?>
                <div class="prefectures-grid-container" style="padding: 40px 0;">
                    <h2 class="text-xl font-bold mb-6 text-ink-primary font-serif">都道府県から助成金を探す</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                                echo '<a href="' . esc_url($pref_link) . '" class="block p-4 bg-white border border-gray-200 rounded hover:shadow-md transition-shadow text-ink-primary">';
                                echo '<span class="font-bold">' . esc_html($pref->name) . '</span>';
                                echo '<span class="text-gray-500 text-xs ml-2">(' . $pref->count . ')</span>';
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php else: ?>
            
                <!-- List Container: Dictionary Layout -->
                <div id="grants-container" class="grid gap-0 border-t border-gray-200 mb-20">
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
                    
                    // 市町村フィルタ（URLパラメータ）- v40.0追加
                    if (!empty($url_params['municipality'])) {
                        $tax_query[] = array(
                            'taxonomy' => 'grant_municipality',
                            'field' => 'slug',
                            'terms' => $url_params['municipality']
                        );
                    }
                    
                    // タグフィルタ（URLパラメータ）- v40.0追加
                    if (!empty($url_params['tag'])) {
                        $tax_query[] = array(
                            'taxonomy' => 'grant_tag',
                            'field' => 'slug',
                            'terms' => $url_params['tag']
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
                            // 図鑑スタイルのカードを使用
                            include(get_template_directory() . '/template-parts/grant/card-zukan.php');
                            
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
                        // 結果なしの場合（図鑑スタイル）
                        echo '<div class="zukan-empty-state">';
                        echo '該当する項目はこの巻には記されていないようだ...';
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

                <!-- ページネーション（都道府県一覧以外）- 図鑑スタイル -->
                <?php if (empty($url_params['view']) || $url_params['view'] !== 'prefectures'): ?>
                <div class="pagination-wrapper zukan-pagination" 
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
                        
                        // ページネーションリンクに#listアンカーを追加
                        $pagination_links = paginate_links( array(
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
                        // 各リンクに#listアンカーを追加
                        if ($pagination_links) {
                            echo preg_replace('/href="([^"]+)"/', 'href="$1#list"', $pagination_links);
                        }
                    }
                    ?>
                </div>
                <?php endif; ?>
            </section>
            
            <?php 
            // アーカイブSEOコンテンツ: アウトロ
            if (function_exists('gi_output_archive_outro_content')) {
                gi_output_archive_outro_content();
            }
            ?>
            
            <!-- SEO解説記事セクション -->
            <?php 
            // SEOコンテンツがある場合はカスタム内容を表示、なければデフォルト記事を表示
            $show_default_article = true;
            if (function_exists('gi_has_archive_seo_content')) {
                $show_default_article = !gi_has_archive_seo_content();
            }
            
            if ($show_default_article && ($is_category_archive || $is_prefecture_archive || is_post_type_archive('grant'))): 
            ?>
            <section class="zukan-article-section" id="guide">
                <span class="ornament-center">&#10086;</span>

                <article class="zukan-article-content">
                    <div class="zukan-article-header">
                        <span class="label-text">Editorial Guide</span>
                        <?php if ($is_prefecture_archive): ?>
                        <?php echo esc_html($current_category->name); ?>の助成金・補助金申請ガイド
                        <?php elseif ($is_category_archive): ?>
                        <?php echo esc_html($current_category->name); ?>の申請傾向と採択のポイント
                        <?php else: ?>
                        採択率を上げるための「三つの鉄則」
                        <?php endif; ?>
                    </div>
                    
                    <div class="zukan-article-columns">
                        <?php if ($is_prefecture_archive): ?>
                        <div>
                            <h3>壱. 申請の傾向</h3>
                            <p><?php echo esc_html($current_category->name); ?>では、地域の産業振興や中小企業支援を目的とした独自の助成金制度が充実しています。特に創業支援、事業承継、設備投資に関する支援が手厚く、申請件数も年々増加傾向にあります。審査では地域経済への貢献度や雇用創出効果が重視される傾向があります。</p>
                        </div>
                        <div>
                            <h3>弐. 採択のポイント</h3>
                            <p>採択率を高めるためには、事業計画の具体性と実現可能性が鍵となります。また、<?php echo esc_html($current_category->name); ?>の産業政策との整合性を示すことも重要です。申請書類では、数値目標を明確に設定し、その達成に向けた具体的なアクションプランを提示することをお勧めします。</p>
                        </div>
                        <?php elseif ($is_category_archive): ?>
                        <div>
                            <h3>壱. この分野の特徴</h3>
                            <p><?php echo esc_html($current_category->name); ?>関連の助成金は、技術革新や事業効率化を促進することを目的としています。近年は特にデジタル化やサステナビリティへの取り組みに対する支援が拡充されており、申請の機会が広がっています。補助率も比較的高く設定されているケースが多いのが特徴です。</p>
                        </div>
                        <div>
                            <h3>弐. 申請時の注意点</h3>
                            <p>この分野では、導入する技術や設備の先進性・革新性を明確に示すことが求められます。また、投資対効果（ROI）を具体的な数値で示し、事業の持続可能性についても説明することが採択への近道です。専門家のサポートを受けながら申請することをお勧めします。</p>
                        </div>
                        <?php else: ?>
                        <div>
                            <h3>壱. 具体性の徹底</h3>
                            <p>審査員は貴社の業界については素人であると心得るべし。「売上が上がります」という予言ではなく、「なぜ上がるのか」という論理的帰結を記さねばならない。顧客ターゲットの属性、市場規模の数値的根拠（EBPM）、競合優位性を明確なデータで示すことこそが、採択への近道である。</p>
                        </div>
                        <div>
                            <h3>弐. 加点の全取得</h3>
                            <p>補助金審査は減点方式ではなく加点方式である。「経営革新計画」の承認、「パートナーシップ構築宣言」の登録。これらは面倒ではあるが、確実に点数を積み上げられる要素だ。ボーダーライン上で合否を分けるのは、こうした地道な努力の差に他ならない。</p>
                        </div>
                        <div>
                            <h3>参. 資金計画の現実性</h3>
                            <p>多くの補助金は「後払い」である。採択決定から入金まで、1年以上を要することも珍しくない。その間のつなぎ融資は確保できているか?資金ショートによる事業断念は、最も避けねばならない結末である。金融機関との事前調整を怠るべからず。</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="zukan-article-note">
                        <p class="note-title">※ 注意書き</p>
                        <p class="note-text">
                            本図鑑の記述は<?php echo date('Y'); ?>年時点の情報に基づく。制度は生き物であり、常に変化する。<br>
                            最新の公募要領は、必ず公式の布告（公式サイト）にて確認されたし。
                        </p>
                    </div>
                </article>
            </section>
            <?php endif; ?>
            
        </div>

        <!-- サイドバー（PC only） - 図鑑スタイル -->
        <aside class="yahoo-sidebar zukan-sidebar" role="complementary" aria-label="サイドバー">
            
            <!-- サイドバー検索ウィジェット -->
            <section class="sidebar-widget sidebar-search-widget">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    キーワード検索
                </h3>
                <div class="widget-content">
                    <div class="sidebar-search-form">
                        <input type="text" 
                               id="sidebar-keyword-search" 
                               class="sidebar-search-input" 
                               placeholder="助成金名・キーワードで検索"
                               aria-label="キーワード検索">
                        <button type="button" 
                                id="sidebar-search-btn" 
                                class="sidebar-search-btn"
                                aria-label="検索">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </section>

            <!-- サイドバー絞り込みウィジェット -->
            <section class="sidebar-widget sidebar-filter-widget">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    絞り込み
                </h3>
                <div class="widget-content">
                    
                    <!-- カテゴリフィルター -->
                    <div class="sidebar-filter-group" id="sidebar-category-filter">
                        <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                            <span class="filter-group-label">カテゴリ</span>
                            <span class="filter-selected-count" id="category-selected-count" style="display: none;">0</span>
                            <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="sidebar-filter-options" style="display: none;">
                            <?php foreach (array_slice($all_categories, 0, 8) as $index => $category): ?>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" 
                                       name="sidebar_category[]" 
                                       value="<?php echo esc_attr($category->slug); ?>">
                                <span class="checkbox-custom"></span>
                                <span class="option-label"><?php echo esc_html($category->name); ?></span>
                                <span class="option-count"><?php echo $category->count; ?></span>
                            </label>
                            <?php endforeach; ?>
                            <?php if (count($all_categories) > 8): ?>
                            <button type="button" class="sidebar-filter-more" data-target="category">さらに表示</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 都道府県フィルター -->
                    <div class="sidebar-filter-group" id="sidebar-prefecture-filter">
                        <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                            <span class="filter-group-label">都道府県</span>
                            <span class="filter-selected-count" id="prefecture-selected-count" style="display: none;">0</span>
                            <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="sidebar-filter-options" style="display: none;">
                            <?php 
                            $sidebar_prefectures = get_terms(array(
                                'taxonomy' => 'grant_prefecture',
                                'hide_empty' => true,
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 15
                            ));
                            if ($sidebar_prefectures && !is_wp_error($sidebar_prefectures)):
                                foreach ($sidebar_prefectures as $pref): ?>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" 
                                       name="sidebar_prefecture[]" 
                                       value="<?php echo esc_attr($pref->slug); ?>">
                                <span class="checkbox-custom"></span>
                                <span class="option-label"><?php echo esc_html($pref->name); ?></span>
                                <span class="option-count"><?php echo $pref->count; ?></span>
                            </label>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- 地域フィルター -->
                    <div class="sidebar-filter-group" id="sidebar-region-filter">
                        <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                            <span class="filter-group-label">地域</span>
                            <span class="filter-selected-count" id="region-selected-count" style="display: none;">0</span>
                            <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="sidebar-filter-options" style="display: none;">
                            <?php foreach ($region_groups as $region_slug => $region_name): ?>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" 
                                       name="sidebar_region[]" 
                                       value="<?php echo esc_attr($region_slug); ?>">
                                <span class="checkbox-custom"></span>
                                <span class="option-label"><?php echo esc_html($region_name); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 助成金額フィルター -->
                    <div class="sidebar-filter-group" id="sidebar-amount-filter">
                        <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                            <span class="filter-group-label">助成金額</span>
                            <span class="filter-selected-count" id="amount-selected-count" style="display: none;">0</span>
                            <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="sidebar-filter-options" style="display: none;">
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_amount[]" value="0-100">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">〜100万円</span>
                            </label>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_amount[]" value="100-500">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">100万円〜500万円</span>
                            </label>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_amount[]" value="500-1000">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">500万円〜1000万円</span>
                            </label>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_amount[]" value="1000-3000">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">1000万円〜3000万円</span>
                            </label>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_amount[]" value="3000+">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">3000万円以上</span>
                            </label>
                        </div>
                    </div>

                    <!-- 募集状況フィルター -->
                    <div class="sidebar-filter-group" id="sidebar-status-filter">
                        <button type="button" class="sidebar-filter-toggle" aria-expanded="false">
                            <span class="filter-group-label">募集状況</span>
                            <span class="filter-selected-count" id="status-selected-count" style="display: none;">0</span>
                            <svg class="toggle-arrow" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="sidebar-filter-options" style="display: none;">
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_status[]" value="active">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">募集中</span>
                            </label>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_status[]" value="upcoming">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">募集予定</span>
                            </label>
                            <label class="sidebar-filter-option">
                                <input type="checkbox" name="sidebar_status[]" value="closed">
                                <span class="checkbox-custom"></span>
                                <span class="option-label">募集終了</span>
                            </label>
                        </div>
                    </div>

                    <!-- フィルター適用ボタン -->
                    <div class="sidebar-filter-actions">
                        <button type="button" id="sidebar-apply-filter" class="sidebar-apply-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            この条件で検索
                        </button>
                        <button type="button" id="sidebar-reset-filter" class="sidebar-reset-btn">
                            リセット
                        </button>
                    </div>

                </div>
            </section>

            <?php 
            // アーカイブSEOコンテンツ: サイドバー追加コンテンツ
            if (function_exists('gi_output_archive_sidebar_content')) {
                gi_output_archive_sidebar_content();
            }
            ?>
            
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

<!-- 初期化スクリプト（最小限） -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ArchiveCommon の初期化（サイドバーフィルター等は archive-common.js で処理）
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