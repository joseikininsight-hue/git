<?php
/**
 * 補助金図鑑 - Perfect Header (ZUKAN Style)
 * 書斎・図鑑風デザイン - 完全統合版
 * 外部CSS・JS読み込み版
 * スキップリンクなしバージョン
 * * @package Joseikin_Insight_Header
 * @version 16.0.0 (Zukan Edition - External Assets)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO Meta Tag Generation Function
 * Phase 1 SEO Fix: Generate complete meta tags for all page types
 * * @since 11.0.3
 * @return array SEO meta data array
 */
if (!function_exists('ji_generate_seo_meta')) {
    function ji_generate_seo_meta() {
        $site_name = get_bloginfo('name');
        $site_description = get_bloginfo('description');
        $default_image = 'https://joseikin-insight.com/wp-content/uploads/2025/05/og-default.jpg';
        
        // Default values
        $seo = array(
            'title' => $site_name,
            'description' => $site_description ?: '中小企業・個人事業主のための補助金・助成金検索サイト。最新の補助金情報を専門家監修のもとわかりやすく解説。',
            'canonical' => home_url(remove_query_arg(array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'fbclid', 'gclid'))),
            'og_type' => 'website',
            'og_title' => $site_name,
            'og_description' => '',
            'og_image' => $default_image,
            'robots' => '',
            'keywords' => '補助金, 助成金, 中小企業, 小規模事業者, 創業支援, IT導入補助金, ものづくり補助金',
        );
        
        // Front Page
        if (is_front_page() || is_home()) {
            $seo['title'] = $site_name . ' | 補助金・助成金検索サイト';
            $seo['description'] = '全国の補助金・助成金を簡単検索。' . date('Y') . '年最新の補助金情報を中小企業診断士監修のもと毎日更新。IT導入補助金、ものづくり補助金、小規模事業者持続化補助金など幅広く対応。';
            $seo['og_title'] = $seo['title'];
            $seo['og_description'] = $seo['description'];
        }
        
        // Single Grant Page
        elseif (is_singular('grant')) {
            $post_id = get_the_ID();
            $title = get_the_title();
            
            // Get grant-specific data
            $organization = function_exists('get_field') ? get_field('organization', $post_id) : get_post_meta($post_id, 'organization', true);
            $max_amount = function_exists('get_field') ? get_field('max_amount', $post_id) : get_post_meta($post_id, 'max_amount', true);
            $deadline = function_exists('get_field') ? get_field('deadline', $post_id) : get_post_meta($post_id, 'deadline', true);
            $ai_summary = function_exists('get_field') ? get_field('ai_summary', $post_id) : get_post_meta($post_id, 'ai_summary', true);
            
            // Generate description
            $desc_parts = array();
            if ($organization) $desc_parts[] = $organization . 'の補助金';
            if ($max_amount) $desc_parts[] = '最大' . $max_amount;
            if ($deadline) $desc_parts[] = '締切:' . $deadline;
            
            $meta_desc = '';
            if ($ai_summary) {
                $meta_desc = mb_substr(wp_strip_all_tags($ai_summary), 0, 120, 'UTF-8');
            } elseif (has_excerpt()) {
                $meta_desc = mb_substr(wp_strip_all_tags(get_the_excerpt()), 0, 120, 'UTF-8');
            } else {
                $meta_desc = mb_substr(wp_strip_all_tags(get_the_content()), 0, 120, 'UTF-8');
            }
            
            if (empty($meta_desc) && !empty($desc_parts)) {
                $meta_desc = implode('。', $desc_parts);
            }
            
            $seo['title'] = $title . ' | ' . $site_name;
            $seo['description'] = $meta_desc . '。対象者、申請方法、必要書類を詳しく解説。';
            $seo['canonical'] = get_permalink($post_id);
            $seo['og_type'] = 'article';
            $seo['og_title'] = $title;
            $seo['og_description'] = $seo['description'];
            
            // Featured image
            if (has_post_thumbnail($post_id)) {
                $seo['og_image'] = get_the_post_thumbnail_url($post_id, 'large');
            }
            
            // Keywords from taxonomies
            $categories = wp_get_post_terms($post_id, 'grant_category', array('fields' => 'names'));
            $prefectures = wp_get_post_terms($post_id, 'grant_prefecture', array('fields' => 'names'));
            if (!is_wp_error($categories) && !empty($categories)) {
                $seo['keywords'] = implode(', ', array_merge($categories, is_wp_error($prefectures) ? array() : $prefectures));
            }
        }
        
        // Single Column/Post Page
        elseif (is_singular('column') || is_singular('post')) {
            $post_id = get_the_ID();
            $title = get_the_title();
            
            $meta_desc = '';
            if (has_excerpt()) {
                $meta_desc = mb_substr(wp_strip_all_tags(get_the_excerpt()), 0, 120, 'UTF-8');
            } else {
                $meta_desc = mb_substr(wp_strip_all_tags(get_the_content()), 0, 120, 'UTF-8');
            }
            
            $seo['title'] = $title . ' | コラム | ' . $site_name;
            $seo['description'] = $meta_desc;
            $seo['canonical'] = get_permalink($post_id);
            $seo['og_type'] = 'article';
            $seo['og_title'] = $title;
            $seo['og_description'] = $meta_desc;
            
            if (has_post_thumbnail($post_id)) {
                $seo['og_image'] = get_the_post_thumbnail_url($post_id, 'large');
            }
        }
        
        // Grant Archive Page
        elseif (is_post_type_archive('grant')) {
            $seo['title'] = '補助金・助成金一覧 | ' . $site_name;
            $seo['description'] = '全国の補助金・助成金を一覧で検索。募集中の補助金、締切間近の助成金、新着情報を毎日更新。カテゴリー・都道府県・対象者から簡単に絞り込み検索。';
            $seo['canonical'] = get_post_type_archive_link('grant');
            $seo['og_title'] = $seo['title'];
            $seo['og_description'] = $seo['description'];
        }
        
        // Taxonomy Archive (Category, Prefecture, etc.)
        elseif (is_tax()) {
            $term = get_queried_object();
            if ($term) {
                $taxonomy = get_taxonomy($term->taxonomy);
                $tax_label = $taxonomy ? $taxonomy->labels->singular_name : '';
                
                $seo['title'] = $term->name . 'の補助金・助成金一覧 | ' . $site_name;
                $seo['description'] = $term->name . 'に関する補助金・助成金情報。' . ($term->count > 0 ? '現在' . $term->count . '件の補助金情報を掲載。' : '') . '対象者・金額・締切情報を詳しく解説。';
                $seo['canonical'] = get_term_link($term);
                $seo['og_title'] = $seo['title'];
                $seo['og_description'] = $seo['description'];
            }
        }
        
        // Search Results
        elseif (is_search()) {
            $search_query = get_search_query();
            $seo['title'] = '「' . $search_query . '」の検索結果 | ' . $site_name;
            $seo['description'] = '「' . $search_query . '」に関連する補助金・助成金の検索結果。';
            $seo['canonical'] = home_url('/') . '?s=' . urlencode($search_query);
            $seo['robots'] = 'noindex, follow';
            $seo['og_title'] = $seo['title'];
            $seo['og_description'] = $seo['description'];
        }
        
        // 404 Page
        elseif (is_404()) {
            $seo['title'] = 'ページが見つかりません | ' . $site_name;
            $seo['description'] = 'お探しのページは見つかりませんでした。';
            $seo['robots'] = 'noindex, follow';
        }
        
        // Regular Page
        elseif (is_page()) {
            $post_id = get_the_ID();
            $title = get_the_title();
            
            $meta_desc = '';
            if (has_excerpt()) {
                $meta_desc = wp_strip_all_tags(get_the_excerpt());
            } else {
                $meta_desc = mb_substr(wp_strip_all_tags(get_the_content()), 0, 120, 'UTF-8');
            }
            
            $seo['title'] = $title . ' | ' . $site_name;
            $seo['description'] = $meta_desc ?: $site_description;
            $seo['canonical'] = get_permalink($post_id);
            $seo['og_title'] = $title;
            $seo['og_description'] = $seo['description'];
        }
        
        // Set og_description from description if not set
        if (empty($seo['og_description'])) {
            $seo['og_description'] = $seo['description'];
        }
        
        // Truncate descriptions to recommended length
        $seo['description'] = mb_substr($seo['description'], 0, 160, 'UTF-8');
        $seo['og_description'] = mb_substr($seo['og_description'], 0, 200, 'UTF-8');
        
        return $seo;
    }
}

// ヘッダー用データ取得
if (!function_exists('ji_get_header_data')) {
    function ji_get_header_data() {
        $cached = wp_cache_get('ji_header_data', 'joseikin');
        if ($cached !== false) return $cached;
        
        $data = [
            'last_updated' => get_option('ji_last_data_update', current_time('Y-m-d')),
            'categories' => [],
            'prefectures' => [],
            'popular_searches' => ['IT導入補助金', '小規模事業者持続化補助金', 'ものづくり補助金']
        ];
        
        $data['categories'] = get_terms(['taxonomy' => 'grant_category', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 10]);
        $data['prefectures'] = get_terms(['taxonomy' => 'grant_prefecture', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC']);
        
        wp_cache_set('ji_header_data', $data, 'joseikin', 3600);
        return $data;
    }
}

$header_data = ji_get_header_data();
$grants_url = get_post_type_archive_link('grant');

// 外部CSS/JSファイルパス
$header_css_path = get_template_directory() . '/assets/css/header.css';
$header_js_path = get_template_directory() . '/assets/js/header.js';
$header_css_url = get_template_directory_uri() . '/assets/css/header.css';
$header_js_url = get_template_directory_uri() . '/assets/js/header.js';

// キャッシュバスティング用のバージョン
$header_css_version = file_exists($header_css_path) ? filemtime($header_css_path) : '16.0.0';
$header_js_version = file_exists($header_js_path) ? filemtime($header_js_path) : '16.0.0';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="format-detection" content="telephone=no, email=no, address=no">
    <meta name="theme-color" content="#fdfbf7">
    <meta name="mobile-web-app-capable" content="yes">
    
    <?php
    /**
     * SEO Meta Tags - Dynamic Generation (Conditional)
     */
    if (function_exists('gi_should_output_theme_seo') && gi_should_output_theme_seo()):
        $ji_seo = ji_generate_seo_meta();
    ?>
    
    <!-- SEO Meta Tags (Theme Generated) -->
    <meta name="description" content="<?php echo esc_attr($ji_seo['description']); ?>">
    <link rel="canonical" href="<?php echo esc_url($ji_seo['canonical']); ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:type" content="<?php echo esc_attr($ji_seo['og_type']); ?>">
    <meta property="og:title" content="<?php echo esc_attr($ji_seo['og_title']); ?>">
    <meta property="og:description" content="<?php echo esc_attr($ji_seo['og_description']); ?>">
    <meta property="og:url" content="<?php echo esc_url($ji_seo['canonical']); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <meta property="og:locale" content="ja_JP">
    <?php if (!empty($ji_seo['og_image'])): ?>
    <meta property="og:image" content="<?php echo esc_url($ji_seo['og_image']); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($ji_seo['og_title']); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($ji_seo['og_description']); ?>">
    <?php if (!empty($ji_seo['og_image'])): ?>
    <meta name="twitter:image" content="<?php echo esc_url($ji_seo['og_image']); ?>">
    <?php endif; ?>
    
    <?php if (!empty($ji_seo['robots'])): ?>
    <meta name="robots" content="<?php echo esc_attr($ji_seo['robots']); ?>">
    <?php endif; ?>
    <?php if (!empty($ji_seo['keywords'])): ?>
    <meta name="keywords" content="<?php echo esc_attr($ji_seo['keywords']); ?>">
    <?php endif; ?>
    
    <?php else: ?>
    <!-- SEO Meta Tags: Handled by SEO Plugin -->
    <?php endif; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    
    <!-- LiteSpeed Cache Optimization Hints -->
    <meta name="litespeed-cache" content="cache-control: max-age=3600">
    <?php if (is_front_page()): ?>
    <meta name="litespeed-vary" content="is_mobile">
    <?php endif; ?>
    
    <!-- Fonts for Zukan Design -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@400;500;600;700;800&family=Noto+Sans+JP:wght@400;500;700&family=La+Belle+Aurore&family=Zen+Old+Mincho:wght@700;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@400;500;600;700;800&family=Noto+Sans+JP:wght@400;500;700&family=La+Belle+Aurore&family=Zen+Old+Mincho:wght@700;900&display=swap" rel="stylesheet"></noscript>
    
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'" crossorigin="anonymous">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous"></noscript>
    
    <!-- Header CSS (External) -->
    <link rel="stylesheet" href="<?php echo esc_url($header_css_url); ?>?v=<?php echo esc_attr($header_css_version); ?>" id="ji-header-styles">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-grants-url="<?php echo esc_url($grants_url); ?>">
<?php wp_body_open(); ?>

<header id="ji-header" class="ji-header" role="banner">
    <div class="ji-header-main">
        <div class="ji-header-inner">
            <!-- Logo (Zukan Style) -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="ji-logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> - ホームへ">
                <div class="ji-logo-icon">
                    <span>図</span>
                </div>
                <div class="ji-logo-text-wrapper">
                    <div class="ji-logo-title"><?php echo esc_html(get_bloginfo('name')); ?></div>
                    <div class="ji-logo-subtitle">Subsidy Archive</div>
                </div>
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="ji-nav" role="navigation" aria-label="メインナビゲーション">
                <div class="ji-nav-item" data-menu="services">
                    <button type="button" class="ji-nav-link" aria-haspopup="true" aria-expanded="false">
                        <span>補助金を探す</span>
                        <i class="fas fa-chevron-down ji-chevron" aria-hidden="true"></i>
                    </button>
                    
                    <div class="ji-mega-menu" role="menu" aria-label="補助金検索メニュー">
                        <div class="ji-mega-menu-inner">
                            <div class="ji-mega-menu-header">
                                <div class="ji-mega-menu-title">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                    検索条件を指定する
                                </div>
                            </div>
                            
                            <div class="ji-mega-menu-grid">
                                <div class="ji-mega-column">
                                    <div class="ji-mega-column-title">
                                        <i class="fas fa-folder" aria-hidden="true"></i>
                                        目的から探す
                                    </div>
                                    <div class="ji-mega-links">
                                        <a href="<?php echo esc_url($grants_url); ?>" class="ji-mega-link" role="menuitem"><span class="ji-mega-link-text">すべての補助金</span></a>
                                        <a href="<?php echo esc_url(add_query_arg('application_status', 'open', $grants_url)); ?>" class="ji-mega-link" role="menuitem"><span class="ji-mega-link-text">募集中のみ</span></a>
                                        <a href="<?php echo esc_url(add_query_arg('orderby', 'new', $grants_url)); ?>" class="ji-mega-link" role="menuitem"><span class="ji-mega-link-text">新着順</span></a>
                                        <a href="<?php echo esc_url(add_query_arg('orderby', 'deadline', $grants_url)); ?>" class="ji-mega-link" role="menuitem"><span class="ji-mega-link-text">締切順</span></a>
                                    </div>
                                </div>
                                
                                <div class="ji-mega-column">
                                    <div class="ji-mega-column-title">
                                        <i class="fas fa-tag" aria-hidden="true"></i>
                                        カテゴリー
                                    </div>
                                    <div class="ji-mega-links">
                                        <?php
                                        if ($header_data['categories'] && !is_wp_error($header_data['categories'])) {
                                            foreach (array_slice($header_data['categories'], 0, 6) as $category) {
                                                echo '<a href="' . esc_url(get_term_link($category)) . '" class="ji-mega-link" role="menuitem"><span class="ji-mega-link-text">' . esc_html($category->name) . '</span></a>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="ji-mega-column ji-mega-column--prefectures" style="grid-column: span 2;">
                                    <div class="ji-mega-column-title">
                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                        都道府県
                                    </div>
                                    <div class="ji-prefecture-grid">
                                        <?php
                                        $prefectures_order = ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県', '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'];
                                        $prefecture_terms = [];
                                        if ($header_data['prefectures'] && !is_wp_error($header_data['prefectures'])) {
                                            foreach ($header_data['prefectures'] as $pref) {
                                                $prefecture_terms[$pref->name] = $pref;
                                            }
                                        }
                                        foreach ($prefectures_order as $pref_name) {
                                            if (isset($prefecture_terms[$pref_name])) {
                                                $pref = $prefecture_terms[$pref_name];
                                                echo '<a href="' . esc_url(get_term_link($pref)) . '" class="ji-prefecture-link" role="menuitem">' . esc_html($pref->name) . '</a>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="<?php echo esc_url(home_url('/about/')); ?>" class="ji-nav-link">
                    <span>当サイトについて</span>
                </a>
                
                <a href="<?php echo esc_url(home_url('/column/')); ?>" class="ji-nav-link">
                    <span>コラム</span>
                </a>

                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="ji-nav-link">
                    <span>お問い合わせ</span>
                </a>
                
                <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="ji-nav-btn">
                    <i class="fas fa-check" aria-hidden="true"></i>
                    無料診断
                </a>
            </nav>
            
            <div class="ji-actions">
                <button type="button" id="ji-search-toggle" class="ji-btn-icon" aria-label="検索を開く" aria-expanded="false" aria-controls="ji-search-panel">
                    <i class="fas fa-search" aria-hidden="true"></i>
                </button>
                
                <button type="button" id="ji-mobile-toggle" class="ji-mobile-toggle" aria-label="メニューを開く" aria-expanded="false" aria-controls="ji-mobile-menu">
                    <span class="ji-hamburger"></span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Search Panel (Card Style) -->
    <div id="ji-search-panel" class="ji-search-panel" role="search" aria-label="サイト内検索">
        <div class="ji-search-panel-inner">
            <form id="ji-search-form" class="ji-search-form" action="<?php echo esc_url($grants_url); ?>" method="get">
                <div class="ji-search-main">
                    <div class="ji-search-input-wrapper">
                        <i class="fas fa-search ji-search-icon" aria-hidden="true"></i>
                        <input type="search" id="ji-search-input" name="s" class="ji-search-input" placeholder="補助金名、キーワードで検索..." autocomplete="off" aria-label="検索キーワード">
                        <input type="hidden" name="post_type" value="grant">
                    </div>
                    
                    <div class="ji-search-suggestions" role="group" aria-label="人気の検索キーワード">
                        <span class="ji-search-suggestion-label">人気:</span>
                        <?php foreach ($header_data['popular_searches'] as $search): ?>
                        <a href="<?php echo esc_url(add_query_arg('s', $search, $grants_url)); ?>" class="ji-search-suggestion"><?php echo esc_html($search); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div id="ji-mobile-menu" class="ji-mobile-menu" role="dialog" aria-modal="true" aria-label="モバイルメニュー">
    <div class="ji-mobile-menu-header">
        <div class="ji-mobile-logo">
            <div class="ji-mobile-logo-icon">図</div>
            <span class="ji-mobile-logo-text">補助金図鑑</span>
        </div>
        <button type="button" id="ji-mobile-close" class="ji-mobile-close" aria-label="メニューを閉じる">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>
    
    <div class="ji-mobile-search">
        <div class="ji-mobile-search-wrapper">
            <i class="fas fa-search ji-mobile-search-icon" aria-hidden="true"></i>
            <input type="search" id="ji-mobile-search-input" class="ji-mobile-search-input" placeholder="補助金を検索..." aria-label="補助金を検索">
        </div>
    </div>
    
    <div class="ji-mobile-content">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="ji-mobile-single-link">
            <span><i class="fas fa-home" aria-hidden="true"></i> ホーム</span>
        </a>
        
        <div class="ji-mobile-accordion">
            <button type="button" class="ji-mobile-accordion-trigger" aria-expanded="false" aria-controls="accordion-services">
                <span><i class="fas fa-search" aria-hidden="true"></i> 補助金を探す</span>
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </button>
            <div id="accordion-services" class="ji-mobile-accordion-content">
                <a href="<?php echo esc_url($grants_url); ?>" class="ji-mobile-link">すべての補助金</a>
                <a href="<?php echo esc_url(add_query_arg('application_status', 'open', $grants_url)); ?>" class="ji-mobile-link">募集中のみ</a>
                <a href="<?php echo esc_url(add_query_arg('orderby', 'new', $grants_url)); ?>" class="ji-mobile-link">新着順</a>
                <a href="<?php echo esc_url(home_url('/categories/')); ?>" class="ji-mobile-link">カテゴリーから探す</a>
                <a href="<?php echo esc_url(home_url('/prefectures/')); ?>" class="ji-mobile-link">都道府県から探す</a>
            </div>
        </div>
        
        <a href="<?php echo esc_url(home_url('/about/')); ?>" class="ji-mobile-single-link">
            <span><i class="fas fa-info-circle" aria-hidden="true"></i> 当サイトについて</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/column/')); ?>" class="ji-mobile-single-link">
            <span><i class="fas fa-newspaper" aria-hidden="true"></i> コラム</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="ji-mobile-single-link">
            <span><i class="fas fa-stethoscope" aria-hidden="true"></i> 補助金診断</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="ji-mobile-single-link">
            <span><i class="fas fa-envelope" aria-hidden="true"></i> お問い合わせ</span>
        </a>
    </div>
    
    <a href="<?php echo esc_url($grants_url); ?>" class="ji-mobile-cta">
        <i class="fas fa-search" aria-hidden="true"></i>
        <span>補助金・助成金を探す</span>
    </a>
    
    <div class="ji-mobile-footer">
        <div class="ji-mobile-copyright">&copy; <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?></div>
    </div>
</div>

<div class="ji-header-placeholder" aria-hidden="true"></div>

<main id="main-content" role="main">

<!-- Header JS (External) -->
<script src="<?php echo esc_url($header_js_url); ?>?v=<?php echo esc_attr($header_js_version); ?>" defer></script>
