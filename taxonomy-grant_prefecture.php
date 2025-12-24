<?php
/**
 * Prefecture Archive Template for Grant - 都道府県別助成金アーカイブ
 * 統一デザイン版 - archive-grant.phpベース
 * 
 * @package Grant_Insight_Perfect
 * @version 20.0.0 - Unified Design with card-zukan.php
 */

get_header();

// CSS/JS を直接出力
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
$current_term = get_queried_object();
$prefecture_name = $current_term->name;
$prefecture_slug = $current_term->slug;
$prefecture_description = $current_term->description;
$prefecture_count = $current_term->count;
$prefecture_id = $current_term->term_id;

// URLパラメータの取得と処理
$url_params = array(
    'application_status' => isset($_GET['application_status']) ? sanitize_text_field($_GET['application_status']) : '',
    'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '',
    'target' => isset($_GET['target']) ? sanitize_text_field($_GET['target']) : '',
    'search' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
    'category' => isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '',
);

// SEO用データ
$current_year = date('Y');

// タイトル・説明文の生成
$archive_title = $prefecture_name . 'の補助金・助成金';
$archive_description = $prefecture_description ?: 
    $prefecture_name . 'で利用できる助成金・補助金の最新情報。' . 
    number_format($prefecture_count) . '件の支援制度を掲載。地域別・業種別に検索可能。専門家による申請サポート完備。';

// 総件数
$total_grants = $prefecture_count;
$total_grants_formatted = number_format($total_grants);

// パンくずリスト
$breadcrumbs = array(
    array('name' => 'ホーム', 'url' => home_url('/')),
    array('name' => '補助金図鑑', 'url' => get_post_type_archive_link('grant')),
    array('name' => '都道府県一覧', 'url' => add_query_arg('view', 'prefectures', get_post_type_archive_link('grant'))),
    array('name' => $prefecture_name, 'url' => '')
);

// カテゴリデータの取得
$all_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
]);

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
?>

<!-- SEO メタデータ -->
<?php
// title タグ用
$seo_title = $prefecture_name . 'の補助金・助成金一覧【' . $current_year . '年度最新版】' . number_format($prefecture_count) . '件';

// Schema.org 構造化データ
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $seo_title,
    'description' => $archive_description,
    'url' => get_term_link($current_term),
    'mainEntity' => array(
        '@type' => 'ItemList',
        'numberOfItems' => $prefecture_count,
        'name' => $prefecture_name . 'の助成金・補助金一覧'
    )
);
?>
<script type="application/ld+json"><?php echo json_encode($schema_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

<!-- メインコンテンツ -->
<main id="main-content" class="site-main" role="main">

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

    <!-- ヒーローセクション -->
    <section class="zukan-hero zukan-hero-simple">
        <div class="yahoo-container">
            <div class="zukan-hero-content">
                <span class="zukan-hero-label">Prefecture Archive</span>
                <h1 class="zukan-hero-title">
                    <?php echo esc_html($archive_title); ?><br>
                    <span class="zukan-hero-subtitle-text">令和<?php echo date('Y') - 2018; ?>年度版 図鑑</span>
                </h1>
                <p class="zukan-hero-description">
                    <?php echo esc_html($archive_description); ?>
                </p>
                <div class="ornament-line"><span>&#10086;</span></div>
                <div class="zukan-hero-stats-simple">
                    <span class="zukan-hero-stat">収録制度数：<strong><?php echo $total_grants_formatted; ?></strong>件</span>
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
            <section class="editors-pick-section results-header" id="list">
                <div class="editors-pick-header">
                    <div class="flex items-center">
                        <span class="editors-pick-number">03</span>
                        <div class="editors-pick-title-wrap">
                            <h2><?php echo esc_html($prefecture_name); ?>の補助金図鑑一覧</h2>
                        </div>
                    </div>
                </div>
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

            <!-- Results Section -->
            <section class="yahoo-results-section" id="grants-results-section">
                
                <!-- List Container: Dictionary Layout -->
                <div id="grants-container">
                    <?php
                    // WP_Queryの引数を構築（都道府県固定）
                    $query_args = array(
                        'post_type' => 'grant',
                        'posts_per_page' => $posts_per_page,
                        'post_status' => 'publish',
                        'paged' => $current_page,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'grant_prefecture',
                                'field' => 'term_id',
                                'terms' => $prefecture_id
                            )
                        )
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
                    
                    // メタクエリを追加
                    if (count($meta_query) > 1) {
                        $query_args['meta_query'] = $meta_query;
                    }
                    
                    // カテゴリーフィルタ（URLパラメータ）
                    if (!empty($url_params['category'])) {
                        $query_args['tax_query'][] = array(
                            'taxonomy' => 'grant_category',
                            'field' => 'slug',
                            'terms' => $url_params['category']
                        );
                    }
                    
                    // 検索キーワード
                    if (!empty($url_params['search'])) {
                        $query_args['s'] = $url_params['search'];
                    }
                    
                    // ソート順の設定
                    if (!empty($url_params['orderby'])) {
                        switch ($url_params['orderby']) {
                            case 'deadline':
                                $query_args['meta_key'] = 'deadline_date';
                                $query_args['orderby'] = 'meta_value';
                                $query_args['order'] = 'ASC';
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
                                $query_args['orderby'] = 'date';
                                $query_args['order'] = 'DESC';
                                break;
                            case 'popular':
                                $query_args['meta_key'] = 'view_count';
                                $query_args['orderby'] = 'meta_value_num';
                                $query_args['order'] = 'DESC';
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
                    $grants_query = new WP_Query($query_args);
                    
                    if ($grants_query->have_posts()) :
                        $grant_count = 0;
                        while ($grants_query->have_posts()) : 
                            $grants_query->the_post();
                            
                            // 図鑑スタイルのカードを使用
                            include(get_template_directory() . '/template-parts/grant/card-zukan.php');
                            
                            $grant_count++;
                            
                            // 4件目と8件目の後にインフィード広告を挿入
                            if (($grant_count === 4 || $grant_count === 8) && function_exists('ji_display_ad')) : ?>
                                <div class="archive-infeed-ad" style="margin: 24px 0; padding: 15px; background: #f9f9f9; border-radius: 8px; text-align: center;">
                                    <span style="font-size: 10px; color: #999; display: block; text-align: left; margin-bottom: 8px;">スポンサーリンク</span>
                                    <?php ji_display_ad('archive_grant_infeed'); ?>
                                </div>
                            <?php endif;
                            
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<div class="zukan-empty-state">';
                        echo '該当する項目はこの巻には記されていないようだ...';
                        echo '</div>';
                    endif;
                    ?>
                </div>

                <!-- 結果なし（AJAX用） -->
                <div class="no-results" id="no-results" style="display: none;">
                    <svg class="no-results-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <h3 class="no-results-title">該当する助成金が見つかりませんでした</h3>
                    <p class="no-results-message">検索条件を変更して再度お試しください。</p>
                </div>

                <!-- ページネーション -->
                <div class="pagination-wrapper zukan-pagination" id="pagination-wrapper">
                    <?php
                    if (isset($grants_query) && $grants_query->max_num_pages > 1) {
                        $big = 999999999;
                        
                        $preserved_params = array();
                        foreach ($url_params as $key => $value) {
                            if (!empty($value) && $key !== 'paged') {
                                $preserved_params[$key] = $value;
                            }
                        }
                        
                        $base_url = add_query_arg($preserved_params, str_replace($big, '%#%', esc_url(get_pagenum_link($big))));
                        
                        $pagination_links = paginate_links(array(
                            'base' => $base_url,
                            'format' => '&paged=%#%',
                            'current' => max(1, get_query_var('paged')),
                            'total' => $grants_query->max_num_pages,
                            'type' => 'plain',
                            'prev_text' => '前へ',
                            'next_text' => '次へ',
                            'mid_size' => 2,
                            'end_size' => 1,
                        ));
                        
                        if ($pagination_links) {
                            $pagination_links = preg_replace('/href=["\']([^"\']+)["\']/i', 'href="$1#list"', $pagination_links);
                            echo $pagination_links;
                        }
                    }
                    ?>
                </div>
            </section>
            
            <?php 
            // アーカイブSEOコンテンツ: アウトロ
            if (function_exists('gi_output_archive_outro_content')) {
                gi_output_archive_outro_content();
            }
            ?>
            
            <!-- SEO解説記事セクション -->
            <section class="zukan-article-section" id="guide">
                <span class="ornament-center">&#10086;</span>

                <article class="zukan-article-content">
                    <div class="zukan-article-header">
                        <span class="label-text">Editorial Guide</span>
                        <?php echo esc_html($prefecture_name); ?>の助成金・補助金申請ガイド
                    </div>
                    
                    <div class="zukan-article-columns">
                        <div>
                            <h3>壱. 申請の傾向</h3>
                            <p><?php echo esc_html($prefecture_name); ?>では、地域の産業振興や中小企業支援を目的とした独自の助成金制度が充実しています。特に創業支援、事業承継、設備投資に関する支援が手厚く、申請件数も年々増加傾向にあります。審査では地域経済への貢献度や雇用創出効果が重視される傾向があります。</p>
                        </div>
                        <div>
                            <h3>弐. 採択のポイント</h3>
                            <p>採択率を高めるためには、事業計画の具体性と実現可能性が鍵となります。また、<?php echo esc_html($prefecture_name); ?>の産業政策との整合性を示すことも重要です。申請書類では、数値目標を明確に設定し、その達成に向けた具体的なアクションプランを提示することをお勧めします。</p>
                        </div>
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
            
        </div>

        <!-- サイドバー -->
        <?php include(get_template_directory() . '/template-parts/archive/sidebar-filters.php'); ?>

    </div>

</main>

<?php
// JavaScript 読み込み
$js_file = get_template_directory() . '/assets/js/archive-common.js';
$js_uri = get_template_directory_uri() . '/assets/js/archive-common.js';
?>
<?php if (file_exists($js_file) && !wp_script_is('gi-archive-common-js', 'done')): ?>
<script src="<?php echo esc_url($js_uri . '?ver=' . filemtime($js_file)); ?>"></script>
<?php endif; ?>

<?php get_footer(); ?>
