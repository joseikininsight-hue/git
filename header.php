<?php
/**
 * 補助金図鑑 - Perfect Header
 * 官公庁風デザイン - 完全統合版
 * CSS・PHP・JavaScript一体型ファイル
 * スキップリンクなしバージョン
 * 
 * @package Joseikin_Insight_Header
 * @version 14.1.0 (Government Official Edition - No Skip Link)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO Meta Tag Generation Function
 * Phase 1 SEO Fix: Generate complete meta tags for all page types
 * 
 * @since 11.0.3
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
        
        // ⚠️ REMOVED: Paged archives noindex
        // 以前は is_paged() で noindex を設定していましたが、
        // これは大規模データベースサイトでは悪影響があります。
        // 2ページ目以降もインデックスさせ、canonical で正規化するのがベストプラクティス。
        // Googleは「ページネーションの2ページ目以降もインデックスさせるべき」と推奨。
        // 
        // if (is_paged()) {
        //     $seo['robots'] = 'noindex, follow';
        // }
        
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
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="format-detection" content="telephone=no, email=no, address=no">
    <meta name="theme-color" content="#1b263b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <?php
    /**
     * SEO Meta Tags - Dynamic Generation (Conditional)
     * Phase 1 SEO Fix: Complete meta tags for all page types
     * 
     * ⚠️ CRITICAL FIX: SEOプラグイン（Rank Math等）が有効な場合は
     * テーマ独自のメタタグ出力をスキップし、重複を防止
     * 
     * @since 11.0.3 - SEOプラグイン検出機能追加
     */
    
    // SEOプラグインがアクティブでない場合のみテーマ独自のSEOタグを出力
    if (function_exists('gi_should_output_theme_seo') && gi_should_output_theme_seo()):
        // Get current page information for SEO
        $ji_seo = ji_generate_seo_meta();
    ?>
    
    <!-- SEO Meta Tags (Theme Generated - No SEO Plugin Detected) -->
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
    
    <!-- Additional SEO Tags -->
    <?php if (!empty($ji_seo['robots'])): ?>
    <meta name="robots" content="<?php echo esc_attr($ji_seo['robots']); ?>">
    <?php endif; ?>
    <?php if (!empty($ji_seo['keywords'])): ?>
    <meta name="keywords" content="<?php echo esc_attr($ji_seo['keywords']); ?>">
    <?php endif; ?>
    
    <?php else: ?>
    <!-- SEO Meta Tags: Handled by SEO Plugin (Rank Math, Yoast, etc.) -->
    <?php endif; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <!-- LiteSpeed Cache Optimization Hints -->
    <meta name="litespeed-cache" content="cache-control: max-age=3600">
    <?php if (is_front_page()): ?>
    <meta name="litespeed-vary" content="is_mobile">
    <?php endif; ?>
    
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&family=Shippori+Mincho:wght@500;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&family=Shippori+Mincho:wght@500;700&display=swap" rel="stylesheet"></noscript>
    
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'" crossorigin="anonymous">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous"></noscript>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@400;500;700;800&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
    
    <style id="ji-header-styles">
    :root {
        --h-gov-navy-900: #0d1b2a;
        --h-gov-navy-800: #1b263b;
        --h-gov-navy-700: #2c3e50;
        --h-gov-navy-600: #34495e;
        --h-gov-navy-500: #415a77;
        --h-gov-navy-400: #778da9;
        --h-gov-navy-300: #a3b1c6;
        --h-gov-navy-200: #cfd8e3;
        --h-gov-navy-100: #e8ecf1;
        --h-gov-navy-50: #f4f6f8;
        
        --h-gov-gold: #c9a227;
        --h-gov-gold-light: #d4b77a;
        --h-gov-gold-pale: #f0e6c8;
        
        --h-gov-green: #2e7d32;
        --h-gov-green-light: #e8f5e9;
        
        --h-white: #ffffff;
        --h-black: #1a1a1a;
        --h-gray-900: #212529;
        --h-gray-800: #343a40;
        --h-gray-700: #495057;
        --h-gray-600: #6c757d;
        --h-gray-500: #adb5bd;
        --h-gray-400: #ced4da;
        --h-gray-300: #dee2e6;
        --h-gray-200: #e9ecef;
        --h-gray-100: #f8f9fa;
        
        --h-font-serif: "Shippori Mincho", "Yu Mincho", "YuMincho", "Hiragino Mincho ProN", serif;
        --h-font-sans: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        --h-font-mono: 'SF Mono', 'Monaco', 'Consolas', monospace;
        
        --h-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        --h-transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        --h-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
        --h-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        --h-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
        --h-shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.15);
        --h-radius: 4px;
        --h-radius-lg: 8px;
        
        --h-header-height: 64px;
        --h-max-width: 1200px;
    }

    *, *::before, *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
        -webkit-text-size-adjust: 100%;
    }

    body {
        font-family: var(--h-font-sans);
        font-size: 16px;
        line-height: 1.7;
        color: var(--h-gray-900);
        background: var(--h-gov-navy-50);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    img {
        max-width: 100%;
        height: auto;
        display: block;
    }

    a {
        color: inherit;
        text-decoration: none;
    }
    
    /* ヘッダーナビゲーションのaタグは濃紺色（官公庁スタイル） */
    .ji-header a.ji-nav-link {
        color: var(--h-gov-navy-800) !important;
    }

    button {
        font-family: inherit;
        cursor: pointer;
        border: none;
        background: none;
    }
    
    /* ヘッダーナビゲーションのbuttonタグも濃紺色（官公庁スタイル） */
    .ji-header button.ji-nav-link {
        color: var(--h-gov-navy-800) !important;
    }

    .ji-header-placeholder {
        height: var(--h-header-height);
        display: block;
    }

    .ji-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        background: var(--h-white);
        height: var(--h-header-height);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-bottom: 3px solid var(--h-gov-gold);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .ji-header.scrolled {
        box-shadow: var(--h-shadow-lg);
    }

    .ji-header.hidden {
        transform: translateY(-100%);
    }

    .ji-header-main {
        height: var(--h-header-height);
        display: flex;
        align-items: center;
    }

    .ji-header-inner {
        max-width: var(--h-max-width);
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        width: 100%;
    }

    @media (max-width: 640px) {
        .ji-header-inner {
            padding: 0 16px;
        }
    }

    .ji-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        padding: 8px 12px;
        margin: -8px -12px;
        border-radius: var(--h-radius-lg);
        transition: all var(--h-transition);
        text-decoration: none;
    }

    .ji-logo:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .ji-logo:focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: 2px;
    }

    .ji-logo-image-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ji-logo-image {
        height: 36px;
        width: auto;
        max-width: 220px;
        object-fit: contain;
        display: block;
        filter: brightness(1.1);
    }

    @media (max-width: 767px) {
        .ji-logo-image {
            height: 28px;
            max-width: 160px;
        }
    }

    .ji-nav {
        display: none;
        align-items: center;
        gap: 0;
        margin: 0 24px;
        flex: 1;
        justify-content: center;
        height: 100%;
    }

    @media (min-width: 1024px) {
        .ji-nav {
            display: flex;
        }
    }

    .ji-nav-item {
        position: static;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .ji-nav-link {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 14px 18px;
        color: var(--h-gov-navy-800) !important;
        font-size: 14px;
        font-weight: 600;
        transition: all var(--h-transition);
        white-space: nowrap;
        cursor: pointer;
        position: relative;
        min-height: 48px;
        text-decoration: none;
        letter-spacing: 0.02em;
        border: none;
        background: transparent;
    }

    .ji-nav-link:hover,
    .ji-nav-link:focus-visible {
        background: var(--h-gov-navy-50);
        color: var(--h-gov-navy-900) !important;
    }

    .ji-nav-link:focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: -3px;
    }

    .ji-nav-link--active,
    .ji-nav-link[aria-current="page"] {
        background: var(--h-gov-navy-100);
        color: var(--h-gov-navy-900) !important;
    }

    .ji-nav-link--active::after,
    .ji-nav-link[aria-current="page"]::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 20%;
        right: 20%;
        height: 3px;
        background: linear-gradient(90deg, var(--h-gov-gold) 0%, var(--h-gov-gold-light) 100%);
        border-radius: 3px 3px 0 0;
    }

    .ji-nav-link .ji-icon {
        font-size: 14px;
        color: var(--h-gov-gold) !important;
        opacity: 0.9;
    }
    
    /* ナビゲーションリンクのテキスト色を確実に濃紺に（官公庁スタイル） */
    .ji-nav .ji-nav-link span,
    .ji-nav-item .ji-nav-link span {
        color: var(--h-gov-navy-800) !important;
    }
    
    /* buttonとaタグ両方に対応 */
    a.ji-nav-link span,
    button.ji-nav-link span {
        color: var(--h-gov-navy-800) !important;
    }

    .ji-nav-link .ji-chevron {
        font-size: 10px;
        margin-left: 4px;
        transition: transform var(--h-transition);
        color: var(--h-gov-navy-600);
    }

    .ji-nav-item.menu-active .ji-chevron {
        transform: rotate(180deg);
    }

    .ji-mega-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        width: 100vw;
        background: var(--h-white);
        padding: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: opacity var(--h-transition), visibility var(--h-transition), transform var(--h-transition);
        pointer-events: none;
        box-shadow: var(--h-shadow-xl);
        z-index: 9998;
        border-top: none;
    }

    .ji-nav-item.menu-active .ji-mega-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }

    .ji-mega-menu-inner {
        max-width: var(--h-max-width);
        margin: 0 auto;
        padding: 0;
    }

    .ji-mega-menu-header {
        background: linear-gradient(135deg, var(--h-gov-navy-800) 0%, var(--h-gov-navy-900) 100%);
        padding: 20px 32px;
        border-bottom: 3px solid var(--h-gov-gold);
    }

    .ji-mega-menu-title {
        font-family: var(--h-font-serif);
        font-size: 18px;
        font-weight: 700;
        color: var(--h-white);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        letter-spacing: 0.02em;
    }

    .ji-mega-menu-title i {
        color: var(--h-gov-gold);
        font-size: 20px;
    }

    .ji-mega-menu-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        background: var(--h-white);
    }

    .ji-mega-column {
        display: flex;
        flex-direction: column;
        padding: 28px 24px;
        border-right: 1px solid var(--h-gray-200);
        background: var(--h-white);
    }

    .ji-mega-column:last-child {
        border-right: none;
    }

    .ji-mega-column--prefectures {
        background: var(--h-gov-navy-50);
    }

    .ji-mega-column-title {
        font-family: var(--h-font-mono);
        font-size: 12px; /* SEO: 11px → 12px for legible font sizes */
        font-weight: 700;
        letter-spacing: 0.1em;
        color: var(--h-gov-navy-500);
        margin-bottom: 16px;
        padding: 10px 14px;
        background: var(--h-gov-navy-50);
        border-radius: var(--h-radius);
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 3px solid var(--h-gov-gold);
        text-transform: uppercase;
    }

    .ji-mega-column-title i {
        color: var(--h-gov-navy-600);
        font-size: 14px;
    }

    .ji-mega-links {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ji-mega-link {
        display: flex;
        align-items: center;
        color: var(--h-gray-700);
        font-size: 14px;
        font-weight: 500;
        padding: 12px 14px;
        border-radius: var(--h-radius);
        transition: all var(--h-transition);
        min-height: 44px;
        text-decoration: none;
        border: 1px solid transparent;
        background: transparent;
    }

    .ji-mega-link:hover,
    .ji-mega-link:focus-visible {
        color: var(--h-gov-navy-900);
        background: var(--h-gov-navy-50);
        border-color: var(--h-gov-navy-100);
        padding-left: 18px;
    }

    .ji-mega-link:focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: -3px;
    }

    .ji-mega-link-text {
        position: relative;
        display: flex;
        align-items: center;
    }

    .ji-mega-link-text::before {
        content: '›';
        color: var(--h-gov-gold);
        font-size: 16px;
        font-weight: 700;
        margin-right: 10px;
        transition: transform var(--h-transition);
    }

    .ji-mega-link:hover .ji-mega-link-text::before {
        transform: translateX(3px);
        color: var(--h-gov-navy-800);
    }

    .ji-prefecture-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        max-height: 300px;
        overflow-y: auto;
        padding: 4px;
    }

    .ji-prefecture-grid::-webkit-scrollbar {
        width: 6px;
    }

    .ji-prefecture-grid::-webkit-scrollbar-track {
        background: var(--h-gov-navy-100);
        border-radius: 9999px;
    }

    .ji-prefecture-grid::-webkit-scrollbar-thumb {
        background: var(--h-gov-navy-300);
        border-radius: 9999px;
    }

    .ji-prefecture-grid::-webkit-scrollbar-thumb:hover {
        background: var(--h-gov-navy-400);
    }

    .ji-prefecture-link {
        color: var(--h-gray-700);
        font-size: 12px;
        font-weight: 600;
        padding: 10px 8px;
        border-radius: var(--h-radius);
        transition: all var(--h-transition);
        text-align: center;
        min-height: 44px; /* SEO: 40px → 44px for tap target size */
        min-width: 44px; /* SEO: Added for tap target size */
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: 1px solid var(--h-gov-navy-100);
        background: var(--h-white);
    }

    .ji-prefecture-link:hover,
    .ji-prefecture-link:focus-visible {
        color: var(--h-white);
        background: var(--h-gov-navy-800);
        border-color: var(--h-gov-navy-800);
        transform: translateY(-2px);
        box-shadow: var(--h-shadow);
    }

    .ji-prefecture-link:focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: 1px;
    }

    @media (max-width: 1279px) {
        .ji-mega-menu-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .ji-mega-column--prefectures {
            grid-column: span 2;
        }
        
        .ji-prefecture-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    }

    @media (max-width: 1023px) {
        .ji-mega-menu {
            display: none;
        }
    }

    .ji-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .ji-actions {
            gap: 12px;
        }
    }

    .ji-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: var(--h-radius);
        font-size: 14px;
        font-weight: 700;
        transition: all var(--h-transition);
        white-space: nowrap;
        min-height: 44px;
        text-decoration: none;
        border: 2px solid transparent;
    }

    .ji-btn:focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: 2px;
    }

    .ji-btn-icon {
        width: 44px;
        height: 44px;
        padding: 0;
        color: var(--h-gov-navy-800);
        background: transparent;
        border-color: var(--h-gov-navy-300);
    }

    .ji-btn-icon:hover {
        background: var(--h-gov-navy-50);
        border-color: var(--h-gov-navy-600);
    }

    .ji-btn-primary {
        background: var(--h-gov-navy-800);
        color: var(--h-white);
        border-color: var(--h-gov-navy-800);
        display: none;
        box-shadow: var(--h-shadow-sm);
    }

    @media (min-width: 768px) {
        .ji-btn-primary {
            display: inline-flex;
        }
    }

    .ji-btn-primary:hover {
        background: var(--h-gov-navy-900);
        border-color: var(--h-gov-navy-900);
        transform: translateY(-2px);
        box-shadow: var(--h-shadow-lg);
    }

    .ji-mobile-toggle {
        display: flex;
        width: 44px;
        height: 44px;
        color: var(--h-gov-navy-800);
        background: transparent;
        border: 2px solid var(--h-gov-navy-300);
        border-radius: var(--h-radius);
        align-items: center;
        justify-content: center;
        transition: all var(--h-transition);
    }

    @media (min-width: 1024px) {
        .ji-mobile-toggle {
            display: none;
        }
    }

    .ji-mobile-toggle:hover {
        background: var(--h-gov-navy-50);
        border-color: var(--h-gov-navy-600);
    }

    .ji-mobile-toggle:focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: 2px;
    }

    .ji-hamburger {
        width: 20px;
        height: 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .ji-hamburger-line {
        width: 100%;
        height: 2px;
        background: currentColor;
        border-radius: 9999px;
        transition: all var(--h-transition);
        transform-origin: center;
    }

    .ji-mobile-toggle[aria-expanded="true"] .ji-hamburger-line:nth-child(1) {
        transform: translateY(6px) rotate(45deg);
    }

    .ji-mobile-toggle[aria-expanded="true"] .ji-hamburger-line:nth-child(2) {
        opacity: 0;
        transform: scaleX(0);
    }

    .ji-mobile-toggle[aria-expanded="true"] .ji-hamburger-line:nth-child(3) {
        transform: translateY(-6px) rotate(-45deg);
    }

    .ji-search-panel {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--h-white);
        border-top: 3px solid var(--h-gov-gold);
        box-shadow: var(--h-shadow-xl);
        padding: 28px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all var(--h-transition);
        pointer-events: none;
        z-index: 9997;
    }

    .ji-search-panel.open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }

    .ji-search-panel-inner {
        max-width: var(--h-max-width);
        margin: 0 auto;
        padding: 0 24px;
    }

    @media (max-width: 640px) {
        .ji-search-panel-inner {
            padding: 0 16px;
        }
    }

    .ji-search-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    @media (min-width: 768px) {
        .ji-search-form {
            flex-direction: row;
            align-items: flex-start;
        }
    }

    .ji-search-main {
        flex: 1;
    }

    .ji-search-input-wrapper {
        position: relative;
    }

    .ji-search-input {
        width: 100%;
        padding: 16px 20px 16px 52px;
        border: 1px solid var(--h-gray-300);
        border-radius: var(--h-radius);
        font-family: var(--h-font-sans);
        font-size: 15px;
        font-weight: 500;
        color: var(--h-gray-900);
        background: var(--h-gov-navy-50);
        transition: all var(--h-transition);
        min-height: 56px;
    }

    .ji-search-input:hover {
        background: var(--h-white);
        border-color: var(--h-gov-navy-400);
    }

    .ji-search-input:focus {
        outline: none;
        background: var(--h-white);
        border-color: var(--h-gov-navy-700);
        box-shadow: 0 0 0 3px rgba(27, 38, 59, 0.1);
    }

    .ji-search-input::placeholder {
        color: var(--h-gray-600);
    }

    .ji-search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--h-gov-navy-500);
        font-size: 18px;
        pointer-events: none;
    }

    .ji-search-clear {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--h-gray-500);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transition: all var(--h-transition);
        border-radius: var(--h-radius);
    }

    .ji-search-input:not(:placeholder-shown) ~ .ji-search-clear {
        opacity: 1;
        visibility: visible;
    }

    .ji-search-clear:hover {
        color: var(--h-gray-700);
        background: var(--h-gray-100);
    }

    .ji-search-suggestions {
        margin-top: 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .ji-search-suggestion-label {
        font-family: var(--h-font-mono);
        font-size: 12px; /* SEO: 11px → 12px for legible font sizes */
        font-weight: 700;
        letter-spacing: 0.1em;
        color: var(--h-gov-navy-500);
        text-transform: uppercase;
    }

    .ji-search-suggestion {
        background: var(--h-white);
        color: var(--h-gray-700);
        padding: 8px 16px;
        border-radius: var(--h-radius);
        font-size: 13px;
        font-weight: 600;
        transition: all var(--h-transition);
        cursor: pointer;
        min-height: 44px; /* SEO: 36px → 44px for tap target size */
        border: 1px solid var(--h-gray-200);
        text-decoration: none; /* SEO: Added for link styling */
        display: inline-flex; /* SEO: Added for proper alignment */
        align-items: center; /* SEO: Added for vertical centering */
    }

    .ji-search-suggestion:hover {
        background: var(--h-gov-navy-800);
        border-color: var(--h-gov-navy-800);
        color: var(--h-white);
    }

    .ji-search-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .ji-search-select {
        padding: 14px 40px 14px 16px;
        border: 1px solid var(--h-gray-300);
        border-radius: var(--h-radius);
        font-family: var(--h-font-sans);
        font-size: 14px;
        font-weight: 600;
        color: var(--h-gray-700);
        background: var(--h-white) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23495057' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 14px center;
        appearance: none;
        min-width: 160px;
        cursor: pointer;
        transition: all var(--h-transition);
        min-height: 52px;
    }

    .ji-search-select:hover {
        border-color: var(--h-gov-navy-400);
    }

    .ji-search-select:focus {
        outline: none;
        border-color: var(--h-gov-navy-700);
        box-shadow: 0 0 0 3px rgba(27, 38, 59, 0.1);
    }

    .ji-search-submit {
        background: linear-gradient(135deg, var(--h-gov-navy-800) 0%, var(--h-gov-navy-900) 100%);
        color: var(--h-white);
        padding: 14px 28px;
        border-radius: var(--h-radius);
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all var(--h-transition);
        min-height: 52px;
        border: none;
        cursor: pointer;
    }

    .ji-search-submit:hover {
        background: linear-gradient(135deg, var(--h-gov-navy-900) 0%, var(--h-gov-navy-800) 100%);
        transform: translateY(-2px);
        box-shadow: var(--h-shadow-lg);
    }

    .ji-search-submit:focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: 2px;
    }

    .ji-mobile-menu {
        position: fixed;
        inset: 0;
        background: linear-gradient(135deg, var(--h-gov-navy-800) 0%, var(--h-gov-navy-900) 100%);
        z-index: 99999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .ji-mobile-menu.open {
        opacity: 1;
        visibility: visible;
    }

    .ji-mobile-menu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: sticky;
        top: 0;
        background: linear-gradient(135deg, var(--h-gov-navy-800) 0%, var(--h-gov-navy-900) 100%);
        z-index: 10;
    }

    .ji-mobile-logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ji-mobile-logo-icon {
        width: 32px;
        height: 32px;
        border-radius: var(--h-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .ji-mobile-logo-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .ji-mobile-logo-text {
        color: var(--h-white);
        font-family: var(--h-font-serif);
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .ji-mobile-close {
        width: 44px;
        height: 44px;
        color: var(--h-white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        border-radius: var(--h-radius);
        transition: all var(--h-transition);
        border: 2px solid var(--h-gov-navy-400);
    }

    .ji-mobile-close:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--h-white);
    }

    .ji-mobile-search {
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .ji-mobile-search-wrapper {
        position: relative;
    }

    .ji-mobile-search-input {
        width: 100%;
        padding: 14px 18px 14px 48px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid var(--h-gov-navy-400);
        border-radius: var(--h-radius);
        color: var(--h-white);
        font-family: var(--h-font-sans);
        font-size: 15px;
        font-weight: 500;
        min-height: 52px;
        transition: all var(--h-transition);
    }

    .ji-mobile-search-input::placeholder {
        color: var(--h-gov-navy-300);
    }

    .ji-mobile-search-input:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.12);
        border-color: var(--h-gov-gold);
    }

    .ji-mobile-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--h-gov-navy-300);
        font-size: 16px;
        pointer-events: none;
    }

    .ji-mobile-content {
        padding: 20px;
    }

    .ji-mobile-section {
        margin-bottom: 24px;
    }

    .ji-mobile-accordion {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .ji-mobile-accordion-trigger {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 0;
        color: var(--h-white);
        font-size: 15px;
        font-weight: 600;
        text-align: left;
        min-height: 60px;
        transition: all var(--h-transition);
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .ji-mobile-accordion-trigger span {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ji-mobile-accordion-trigger span i:first-child {
        color: var(--h-gov-gold);
        width: 20px;
        font-size: 16px;
    }

    .ji-mobile-accordion-trigger:hover {
        color: var(--h-gov-navy-200);
    }

    .ji-mobile-accordion-trigger i:last-child {
        color: var(--h-gov-gold);
        font-size: 12px;
        transition: transform var(--h-transition);
    }

    .ji-mobile-accordion-trigger[aria-expanded="true"] i:last-child {
        transform: rotate(180deg);
    }

    .ji-mobile-accordion-content {
        display: none;
        padding-bottom: 16px;
    }

    .ji-mobile-accordion-content.open {
        display: block;
        animation: mobileSlideDown 0.25s ease-out;
    }

    @keyframes mobileSlideDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ji-mobile-link {
        display: flex;
        align-items: center;
        color: var(--h-gov-navy-200);
        font-size: 14px;
        font-weight: 500;
        padding: 14px 16px 14px 40px;
        margin: 0 -16px;
        border-radius: var(--h-radius);
        transition: all var(--h-transition);
        min-height: 52px;
        text-decoration: none;
    }

    .ji-mobile-link::before {
        content: '›';
        color: var(--h-gov-gold);
        font-size: 16px;
        font-weight: 700;
        margin-right: 10px;
    }

    .ji-mobile-link:hover {
        color: var(--h-white);
        background: rgba(255, 255, 255, 0.08);
    }

    .ji-mobile-single-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 0;
        color: var(--h-white);
        font-size: 15px;
        font-weight: 600;
        text-align: left;
        min-height: 60px;
        transition: all var(--h-transition);
        text-decoration: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .ji-mobile-single-link span {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ji-mobile-single-link span i:first-child {
        color: var(--h-gov-gold);
        width: 20px;
        font-size: 16px;
    }

    .ji-mobile-single-link:hover {
        color: var(--h-gov-navy-200);
    }

    .ji-mobile-single-link i:last-child {
        color: var(--h-gov-gold);
        font-size: 12px;
    }

    .ji-mobile-cta {
        background: var(--h-white);
        color: var(--h-gov-navy-900);
        padding: 18px;
        border-radius: var(--h-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 700;
        font-size: 15px;
        margin-top: 24px;
        transition: all var(--h-transition);
        min-height: 60px;
        text-decoration: none;
        border: 2px solid var(--h-white);
    }

    .ji-mobile-cta:hover {
        background: var(--h-gov-gold-pale);
        border-color: var(--h-gov-gold);
        transform: translateY(-2px);
        box-shadow: var(--h-shadow-lg);
    }

    .ji-mobile-footer {
        padding: 28px 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .ji-mobile-social {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .ji-mobile-social-link {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.08);
        color: var(--h-white);
        border-radius: var(--h-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all var(--h-transition);
        text-decoration: none;
        border: 1px solid var(--h-gov-navy-400);
    }

    .ji-mobile-social-link:hover {
        background: var(--h-gov-gold);
        color: var(--h-gov-navy-900);
        border-color: var(--h-gov-gold);
        transform: translateY(-2px);
    }

    .ji-mobile-trust {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .ji-mobile-trust-badge {
        background: rgba(255, 255, 255, 0.08);
        color: var(--h-white);
        padding: 8px 14px;
        border-radius: var(--h-radius);
        font-family: var(--h-font-mono);
        font-size: 12px; /* SEO: 11px → 12px for legible font sizes */
        font-weight: 700;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .ji-mobile-trust-badge i {
        color: var(--h-gov-gold);
    }

    .ji-mobile-copyright {
        color: var(--h-gov-navy-400);
        font-family: var(--h-font-mono);
        font-size: 12px; /* SEO: 11px → 12px for legible font sizes */
        letter-spacing: 0.05em;
    }

    .sr-only {
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

    body.menu-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
        height: 100%;
    }

    :focus-visible {
        outline: 3px solid var(--h-gov-gold);
        outline-offset: 2px;
    }

    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            transition-duration: 0.01ms !important;
        }
    }

    @media print {
        .ji-header {
            position: static;
            background: white;
            border-bottom: 2px solid black;
        }
        
        .ji-nav,
        .ji-actions,
        .ji-search-panel,
        .ji-mobile-menu {
            display: none !important;
        }
        
        .ji-logo-image {
            filter: none;
        }
    }

    @media (max-width: 640px) {
        .ji-mobile-content {
            padding: 16px;
        }
        
        .ji-mobile-cta {
            padding: 16px;
            font-size: 14px;
        }
        
        .ji-mobile-accordion-trigger,
        .ji-mobile-single-link {
            font-size: 14px;
            min-height: 56px;
        }
    }
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="ji-header" class="ji-header" role="banner">
    <div class="ji-header-main">
        <div class="ji-header-inner">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="ji-logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> - ホームへ">
                <div class="ji-logo-image-wrapper">
                    <img 
                        src="https://joseikin-insight.com/gemini_generated_image_19k6yi19k6yi19k6/" 
                        alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                        class="ji-logo-image"
                        width="240"
                        height="40"
                        loading="eager"
                        fetchpriority="high"
                        decoding="async"
                        data-no-lazy="1"
                        data-skip-lazy="1"
                    >
                </div>
            </a>
            
            <nav class="ji-nav" role="navigation" aria-label="メインナビゲーション">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="ji-nav-link<?php echo is_front_page() ? ' ji-nav-link--active' : ''; ?>">
                    <i class="fas fa-home ji-icon" aria-hidden="true"></i>
                    <span>ホーム</span>
                </a>
                
                <div class="ji-nav-item" data-menu="services">
                    <button type="button" class="ji-nav-link" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-list-ul ji-icon" aria-hidden="true"></i>
                        <span>サービス一覧</span>
                        <i class="fas fa-chevron-down ji-chevron" aria-hidden="true"></i>
                    </button>
                    
                    <div class="ji-mega-menu" role="menu" aria-label="サービス一覧メニュー">
                        <div class="ji-mega-menu-inner">
                            <div class="ji-mega-menu-header">
                                <div class="ji-mega-menu-title">
                                    <i class="fas fa-coins" aria-hidden="true"></i>
                                    補助金・助成金を探す
                                </div>
                            </div>
                            
                            <div class="ji-mega-menu-grid">
                                <div class="ji-mega-column">
                                    <div class="ji-mega-column-title">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                        検索方法
                                    </div>
                                    <div class="ji-mega-links">
                                        <a href="<?php echo esc_url($grants_url); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">すべての補助金・助成金</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('application_status', 'open', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">募集中の補助金・助成金</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('orderby', 'deadline', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">締切間近</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('orderby', 'new', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">新着補助金・助成金</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('orderby', 'popular', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">人気の補助金・助成金</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="ji-mega-column">
                                    <div class="ji-mega-column-title">
                                        <i class="fas fa-folder" aria-hidden="true"></i>
                                        カテゴリーから探す
                                    </div>
                                    <div class="ji-mega-links">
                                        <?php
                                        if ($header_data['categories'] && !is_wp_error($header_data['categories'])) {
                                            foreach (array_slice($header_data['categories'], 0, 8) as $category) {
                                                echo '<a href="' . esc_url(get_term_link($category)) . '" class="ji-mega-link" role="menuitem"><span class="ji-mega-link-text">' . esc_html($category->name) . '</span></a>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="ji-mega-column">
                                    <div class="ji-mega-column-title">
                                        <i class="fas fa-user-tie" aria-hidden="true"></i>
                                        対象者から探す
                                    </div>
                                    <div class="ji-mega-links">
                                        <a href="<?php echo esc_url(add_query_arg('grant_tag', '個人向け', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">個人向け</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('grant_tag', '中小企業', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">中小企業向け</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('grant_tag', '小規模事業者', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">小規模事業者向け</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('grant_tag', 'スタートアップ', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">スタートアップ向け</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('grant_tag', 'NPO', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">NPO・団体向け</span>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('grant_tag', '農業', $grants_url)); ?>" class="ji-mega-link" role="menuitem">
                                            <span class="ji-mega-link-text">農業・一次産業向け</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="ji-mega-column ji-mega-column--prefectures">
                                    <div class="ji-mega-column-title">
                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                        都道府県から探す
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
                
                <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="ji-nav-link">
                    <i class="fas fa-stethoscope ji-icon" aria-hidden="true"></i>
                    <span>補助金診断</span>
                </a>
                
                <a href="<?php echo esc_url(home_url('/about/')); ?>" class="ji-nav-link">
                    <i class="fas fa-info-circle ji-icon" aria-hidden="true"></i>
                    <span>当サイトについて</span>
                </a>
                
                <a href="<?php echo esc_url(home_url('/column/')); ?>" class="ji-nav-link">
                    <i class="fas fa-newspaper ji-icon" aria-hidden="true"></i>
                    <span>ニュース</span>
                </a>
                
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="ji-nav-link">
                    <i class="fas fa-envelope ji-icon" aria-hidden="true"></i>
                    <span>お問い合わせ</span>
                </a>
            </nav>
            
            <div class="ji-actions">
                <button type="button" id="ji-search-toggle" class="ji-btn ji-btn-icon" aria-label="検索を開く" aria-expanded="false" aria-controls="ji-search-panel">
                    <i class="fas fa-search" aria-hidden="true"></i>
                </button>
                
                <a href="<?php echo esc_url($grants_url); ?>" class="ji-btn ji-btn-primary">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <span>補助金を探す</span>
                </a>
                
                <button type="button" id="ji-mobile-toggle" class="ji-mobile-toggle" aria-label="メニューを開く" aria-expanded="false" aria-controls="ji-mobile-menu">
                    <span class="ji-hamburger">
                        <span class="ji-hamburger-line"></span>
                        <span class="ji-hamburger-line"></span>
                        <span class="ji-hamburger-line"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    
    <div id="ji-search-panel" class="ji-search-panel" role="search" aria-label="サイト内検索">
        <div class="ji-search-panel-inner">
            <form id="ji-search-form" class="ji-search-form" action="<?php echo esc_url($grants_url); ?>" method="get">
                <div class="ji-search-main">
                    <div class="ji-search-input-wrapper">
                        <i class="fas fa-search ji-search-icon" aria-hidden="true"></i>
                        <input type="search" id="ji-search-input" name="search" class="ji-search-input" placeholder="補助金名、キーワードで検索..." autocomplete="off" aria-label="検索キーワード">
                        <button type="button" class="ji-search-clear" aria-label="検索をクリア">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    
                    <div class="ji-search-suggestions" role="group" aria-label="人気の検索キーワード">
                        <span class="ji-search-suggestion-label">人気:</span>
                        <?php foreach ($header_data['popular_searches'] as $search): ?>
                        <a href="<?php echo esc_url(add_query_arg('search', $search, $grants_url)); ?>" class="ji-search-suggestion"><?php echo esc_html($search); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="ji-search-filters">
                    <select name="category" class="ji-search-select" aria-label="カテゴリー">
                        <option value="">すべてのカテゴリー</option>
                        <?php
                        if ($header_data['categories'] && !is_wp_error($header_data['categories'])) {
                            foreach ($header_data['categories'] as $cat) {
                                echo '<option value="' . esc_attr($cat->slug) . '">' . esc_html($cat->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    
                    <select name="prefecture" class="ji-search-select" aria-label="都道府県">
                        <option value="">すべての都道府県</option>
                        <?php
                        if ($header_data['prefectures'] && !is_wp_error($header_data['prefectures'])) {
                            foreach ($header_data['prefectures'] as $pref) {
                                echo '<option value="' . esc_attr($pref->slug) . '">' . esc_html($pref->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    
                    <button type="submit" class="ji-search-submit">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <span>検索</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</header>

<div id="ji-mobile-menu" class="ji-mobile-menu" role="dialog" aria-modal="true" aria-label="モバイルメニュー">
    <div class="ji-mobile-menu-header">
        <div class="ji-mobile-logo">
            <div class="ji-mobile-logo-icon">
                <img src="https://joseikin-insight.com/gemini_generated_image_19k6yi19k6yi19k6/" alt="アイコン" width="32" height="32">
            </div>
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
        <div class="ji-mobile-section">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="ji-mobile-single-link">
                <span><i class="fas fa-home" aria-hidden="true"></i> ホーム</span>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
            
            <div class="ji-mobile-accordion">
                <button type="button" class="ji-mobile-accordion-trigger" aria-expanded="false" aria-controls="accordion-services">
                    <span><i class="fas fa-list-ul" aria-hidden="true"></i> サービス一覧</span>
                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </button>
                <div id="accordion-services" class="ji-mobile-accordion-content">
                    <a href="<?php echo esc_url($grants_url); ?>" class="ji-mobile-link">すべての補助金・助成金</a>
                    <a href="<?php echo esc_url(add_query_arg('application_status', 'open', $grants_url)); ?>" class="ji-mobile-link">募集中の補助金・助成金</a>
                    <a href="<?php echo esc_url(add_query_arg('orderby', 'deadline', $grants_url)); ?>" class="ji-mobile-link">締切間近</a>
                    <a href="<?php echo esc_url(add_query_arg('orderby', 'new', $grants_url)); ?>" class="ji-mobile-link">新着補助金・助成金</a>
                    <a href="<?php echo esc_url(home_url('/categories/')); ?>" class="ji-mobile-link">カテゴリー一覧</a>
                    <a href="<?php echo esc_url(home_url('/prefectures/')); ?>" class="ji-mobile-link">都道府県一覧</a>
                </div>
            </div>
            
            <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="ji-mobile-single-link">
                <span><i class="fas fa-stethoscope" aria-hidden="true"></i> 補助金診断</span>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
            
            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="ji-mobile-single-link">
                <span><i class="fas fa-info-circle" aria-hidden="true"></i> 当サイトについて</span>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
            
            <a href="<?php echo esc_url(home_url('/column/')); ?>" class="ji-mobile-single-link">
                <span><i class="fas fa-newspaper" aria-hidden="true"></i> ニュース</span>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
            
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="ji-mobile-single-link">
                <span><i class="fas fa-envelope" aria-hidden="true"></i> お問い合わせ</span>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
        </div>
        
        <a href="<?php echo esc_url($grants_url); ?>" class="ji-mobile-cta">
            <i class="fas fa-search" aria-hidden="true"></i>
            <span>補助金・助成金を探す</span>
        </a>
    </div>
    
    <div class="ji-mobile-footer">
        <div class="ji-mobile-social">
            <a href="https://twitter.com/joseikininsight" class="ji-mobile-social-link" aria-label="X (Twitter)" target="_blank" rel="noopener">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="https://facebook.com/hojokin.zukan" class="ji-mobile-social-link" aria-label="Facebook" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.youtube.com/channel/UCbfjOrG3nSPI3GFzKnGcspQ" class="ji-mobile-social-link" aria-label="YouTube" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
            <a href="https://note.com/hojokin_zukan" class="ji-mobile-social-link" aria-label="Note" target="_blank" rel="noopener"><i class="fas fa-pen-nib"></i></a>
        </div>
        
        <div class="ji-mobile-trust">
            <span class="ji-mobile-trust-badge"><i class="fas fa-landmark"></i>公的情報源</span>
            <span class="ji-mobile-trust-badge"><i class="fas fa-user-check"></i>専門家監修</span>
        </div>
        
        <div class="ji-mobile-copyright">&copy; <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?></div>
    </div>
</div>

<div class="ji-header-placeholder" aria-hidden="true"></div>

<main id="main-content" role="main">

<script data-no-defer>
(function() {
    'use strict';
    
    const header = document.getElementById('ji-header');
    const searchToggle = document.getElementById('ji-search-toggle');
    const searchPanel = document.getElementById('ji-search-panel');
    const searchInput = document.getElementById('ji-search-input');
    const mobileToggle = document.getElementById('ji-mobile-toggle');
    const mobileMenu = document.getElementById('ji-mobile-menu');
    const mobileClose = document.getElementById('ji-mobile-close');
    const mobileSearchInput = document.getElementById('ji-mobile-search-input');
    const navItems = document.querySelectorAll('.ji-nav-item[data-menu]');
    
    let lastScrollY = 0;
    let isSearchOpen = false;
    let isMobileMenuOpen = false;
    let ticking = false;
    
    function initMegaMenus() {
        navItems.forEach(item => {
            const link = item.querySelector('.ji-nav-link');
            const menu = item.querySelector('.ji-mega-menu');
            
            if (!menu || !link) return;
            
            link.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isExpanded = item.classList.contains('menu-active');
                
                navItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('menu-active');
                        const otherLink = otherItem.querySelector('.ji-nav-link');
                        if (otherLink) otherLink.setAttribute('aria-expanded', 'false');
                    }
                });
                
                if (isExpanded) {
                    item.classList.remove('menu-active');
                    link.setAttribute('aria-expanded', 'false');
                } else {
                    item.classList.add('menu-active');
                    link.setAttribute('aria-expanded', 'true');
                }
            });
            
            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    link.click();
                }
                if (e.key === 'Escape') {
                    item.classList.remove('menu-active');
                    link.setAttribute('aria-expanded', 'false');
                    link.focus();
                }
            });
        });
        
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.ji-nav-item')) {
                navItems.forEach(item => {
                    item.classList.remove('menu-active');
                    const link = item.querySelector('.ji-nav-link');
                    if (link) link.setAttribute('aria-expanded', 'false');
                });
            }
        });
    }
    
    function handleScroll() {
        const scrollY = window.scrollY;
        
        // Add scrolled class for shadow effect
        if (scrollY > 50) {
            header?.classList.add('scrolled');
        } else {
            header?.classList.remove('scrolled');
        }
        
        // Hide/show header based on scroll direction (works on mobile & desktop)
        if (scrollY > 100) {  // Lower threshold for mobile
            // Scrolling down - hide header
            if (scrollY > lastScrollY + 3) {  // More sensitive (was 5)
                header?.classList.add('hidden');
                // Close any open menus
                navItems.forEach(item => {
                    item.classList.remove('menu-active');
                    const link = item.querySelector('.ji-nav-link');
                    if (link) link.setAttribute('aria-expanded', 'false');
                });
            } 
            // Scrolling up - show header
            else if (scrollY < lastScrollY - 3) {  // More sensitive (was 5)
                header?.classList.remove('hidden');
            }
        } else {
            // Always show header at top of page
            header?.classList.remove('hidden');
        }
        
        lastScrollY = scrollY;
        ticking = false;
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(handleScroll);
            ticking = true;
        }
    }
    
    function toggleSearch() {
        isSearchOpen = !isSearchOpen;
        searchPanel?.classList.toggle('open', isSearchOpen);
        
        navItems.forEach(item => {
            item.classList.remove('menu-active');
            const link = item.querySelector('.ji-nav-link');
            if (link) link.setAttribute('aria-expanded', 'false');
        });
        
        if (searchToggle) {
            searchToggle.setAttribute('aria-expanded', isSearchOpen);
            searchToggle.innerHTML = isSearchOpen 
                ? '<i class="fas fa-times" aria-hidden="true"></i>'
                : '<i class="fas fa-search" aria-hidden="true"></i>';
        }
        
        if (isSearchOpen && searchInput) {
            setTimeout(() => searchInput.focus(), 150);
        }
    }
    
    function closeSearch() {
        if (!isSearchOpen) return;
        isSearchOpen = false;
        searchPanel?.classList.remove('open');
        if (searchToggle) {
            searchToggle.setAttribute('aria-expanded', 'false');
            searchToggle.innerHTML = '<i class="fas fa-search" aria-hidden="true"></i>';
        }
    }
    
    function openMobileMenu() {
        isMobileMenuOpen = true;
        mobileMenu?.classList.add('open');
        mobileToggle?.setAttribute('aria-expanded', 'true');
        document.body.classList.add('menu-open');
        setTimeout(() => mobileClose?.focus(), 100);
    }
    
    function closeMobileMenu() {
        if (!isMobileMenuOpen) return;
        isMobileMenuOpen = false;
        mobileMenu?.classList.remove('open');
        mobileToggle?.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-open');
        mobileToggle?.focus();
    }
    
    function initAccordions() {
        document.querySelectorAll('.ji-mobile-accordion-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                const contentId = trigger.getAttribute('aria-controls');
                const content = document.getElementById(contentId);
                
                document.querySelectorAll('.ji-mobile-accordion-trigger').forEach(t => {
                    if (t !== trigger) {
                        t.setAttribute('aria-expanded', 'false');
                        const c = document.getElementById(t.getAttribute('aria-controls'));
                        c?.classList.remove('open');
                    }
                });
                
                trigger.setAttribute('aria-expanded', !isExpanded);
                content?.classList.toggle('open', !isExpanded);
            });
        });
    }
    
    function initSearchSuggestions() {
        document.querySelectorAll('.ji-search-suggestion').forEach(btn => {
            btn.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = btn.dataset.search;
                    searchInput.focus();
                }
            });
        });
    }
    
    function initSearchClear() {
        const clearBtn = document.querySelector('.ji-search-clear');
        if (clearBtn && searchInput) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.focus();
            });
        }
    }
    
    function initMobileSearch() {
        if (mobileSearchInput) {
            mobileSearchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = mobileSearchInput.value.trim();
                    if (query) {
                        window.location.href = '<?php echo esc_url($grants_url); ?>?search=' + encodeURIComponent(query);
                    }
                }
            });
        }
    }
    
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        element.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey && document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                } else if (!e.shiftKey && document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    }
    
    window.addEventListener('scroll', requestTick, { passive: true });
    
    searchToggle?.addEventListener('click', toggleSearch);
    mobileToggle?.addEventListener('click', openMobileMenu);
    mobileClose?.addEventListener('click', closeMobileMenu);
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (isMobileMenuOpen) closeMobileMenu();
            else if (isSearchOpen) closeSearch();
            else {
                navItems.forEach(item => {
                    item.classList.remove('menu-active');
                    const link = item.querySelector('.ji-nav-link');
                    if (link) link.setAttribute('aria-expanded', 'false');
                });
            }
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            toggleSearch();
        }
    });
    
    document.addEventListener('click', (e) => {
        if (isSearchOpen && !e.target.closest('.ji-search-panel') && !e.target.closest('#ji-search-toggle')) {
            closeSearch();
        }
    });
    
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024 && isMobileMenuOpen) {
            closeMobileMenu();
        }
    });
    
    initMegaMenus();
    initAccordions();
    initSearchSuggestions();
    initSearchClear();
    initMobileSearch();
    
    if (mobileMenu) {
        trapFocus(mobileMenu);
    }
    
    handleScroll();
    
    console.log('[✓] 補助金図鑑 Header v14.1.0 - Government Official Style (No Skip Link)');
})();
</script>
