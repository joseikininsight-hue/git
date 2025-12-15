<?php
/**
 * Grant Insight Perfect - Functions File (Consolidated & Clean Edition)
 * 
 * Simplified structure with consolidated files in single /inc/ directory
 * - Removed unused code and duplicate functionality
 * - Merged related files for better organization
 * - Eliminated folder over-organization
 * 
 * @package Grant_Insight_Perfect
 * @version 11.0.2 (SEO Duplicate Meta Fix)
 * 
 * Changelog v11.0.2:
 * - Disabled gi_add_seo_meta_tags to prevent duplicate meta tags (header.php handles this)
 * - Disabled gi_inject_inline_cta to prevent content flow interruption
 * - Kept remove_duplicate_sections_from_content active for duplicate section removal
 * - Cleaned up commented code and improved documentation
 *
 * Changelog v10.0.0:
 * - Implemented Yahoo! JAPAN-style tabbed grant browsing system
 * - Added 4 tabs: 締切間近(30日以内), おすすめ, 新着, あなたにおすすめ
 * - Added cookie-based viewing history tracking
 * - Created reusable grant card template (template-parts/grant/card.php)
 * - Added personalized recommendations based on browsing history
 * - Replaced separate grant sections with unified tabbed interface
 * - Current theme styling (black/white, Yahoo! functionality)
 *
 * Previous v9.2.1:
 * - Fixed Jetpack duplicate store registration errors
 * - Added React key prop warning fixes
 * - Fixed Gutenberg block editor JavaScript errors
 * - Added customizer 500 error prevention
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// テーマバージョン定数
if (!defined('GI_THEME_VERSION')) {
    define('GI_THEME_VERSION', '11.0.2');
}
if (!defined('GI_THEME_PREFIX')) {
    define('GI_THEME_PREFIX', 'gi_');
}

// 🔧 MEMORY OPTIMIZATION
if (is_admin() && !wp_doing_ajax()) {
    @ini_set('memory_limit', '256M');
    
    add_action('init', function() {
        if (!defined('WP_POST_REVISIONS')) {
            define('WP_POST_REVISIONS', 3);
        }
        
        if (!defined('AUTOSAVE_INTERVAL')) {
            define('AUTOSAVE_INTERVAL', 300);
        }
    }, 1);
}

/**
 * 🔧 JavaScript Error Handling & Optimization
 */

// Dequeue problematic Jetpack scripts
add_action('admin_enqueue_scripts', 'gi_fix_jetpack_conflicts', 100);
function gi_fix_jetpack_conflicts() {
    if (class_exists('Jetpack')) {
        wp_deregister_script('jetpack-ai-logo-generator');
        wp_deregister_script('jetpack-modules-store');
    }
}

// Fix Gutenberg block editor JavaScript errors
add_action('enqueue_block_editor_assets', 'gi_fix_block_editor_errors', 100);
function gi_fix_block_editor_errors() {
    wp_add_inline_script('wp-blocks', '
        (function() {
            var originalRegisterStore = wp.data && wp.data.registerStore;
            if (originalRegisterStore) {
                wp.data.registerStore = function(storeName, options) {
                    try {
                        return originalRegisterStore.call(wp.data, storeName, options);
                    } catch (error) {
                        if (!error.message.includes("already registered")) {
                            console.error("Store registration error:", error);
                        }
                        return wp.data.select(storeName);
                    }
                };
            }
        })();
    ', 'before');
}

// Disable Jetpack modules that cause conflicts
add_filter('jetpack_get_available_modules', 'gi_disable_problematic_jetpack_modules', 999);
function gi_disable_problematic_jetpack_modules($modules) {
    $problematic_modules = array('photon', 'photon-cdn', 'videopress');
    foreach ($problematic_modules as $module) {
        if (isset($modules[$module])) {
            unset($modules[$module]);
        }
    }
    return $modules;
}

// Fix customizer 500 error
add_action('customize_register', 'gi_fix_customizer_errors', 999);
function gi_fix_customizer_errors($wp_customize) {
    $wp_customize->remove_section('custom_css');
}

// Add error logging for JavaScript errors (debug mode only)
add_action('wp_footer', 'gi_add_js_error_logging');
add_action('admin_footer', 'gi_add_js_error_logging');
function gi_add_js_error_logging() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        ?>
        <script>
        window.addEventListener('error', function(e) {
            if (console && console.error) {
                console.error('JS Error caught:', e.message, 'at', e.filename + ':' + e.lineno);
            }
        });
        
        window.addEventListener('unhandledrejection', function(e) {
            if (console && console.error) {
                console.error('Unhandled Promise Rejection:', e.reason);
            }
        });
        </script>
        <?php
    }
}

/**
 * Purpose Page Rewrite Rules
 */
add_action('init', 'gi_register_purpose_rewrite_rules');
function gi_register_purpose_rewrite_rules() {
    add_rewrite_rule(
        '^purpose/([^/]+)/?$',
        'index.php?gi_purpose=$matches[1]',
        'top'
    );
}

// AUTO-FLUSH: Rewrite rules for purpose pages
add_action('init', function() {
    if (get_option('gi_purpose_rewrite_flushed') !== 'yes') {
        flush_rewrite_rules(false);
        update_option('gi_purpose_rewrite_flushed', 'yes');
    }
}, 99);

// Register purpose query var
add_filter('query_vars', 'gi_register_purpose_query_var');
function gi_register_purpose_query_var($vars) {
    $vars[] = 'gi_purpose';
    return $vars;
}

// Template redirect for purpose pages
add_action('template_redirect', 'gi_purpose_template_redirect');
function gi_purpose_template_redirect() {
    $purpose_slug = get_query_var('gi_purpose');
    if ($purpose_slug) {
        $template = locate_template('page-purpose.php');
        if ($template) {
            include $template;
            exit;
        }
    }
}

/**
 * Get purpose-to-category mapping
 */
function gi_get_purpose_category_mapping() {
    static $mapping = null;
    
    if ($mapping !== null) {
        return $mapping;
    }
    
    $mapping = array(
        // ===== 8 Main Purposes =====
        'equipment' => array(
            '設備投資', 'ものづくり・新商品開発', 'IT導入・DX', 
            '生産性向上・業務効率化', '防犯・防災・BCP', 
            '省エネ・再エネ', '医療・福祉', '観光・インバウンド', 
            '農業・林業・漁業'
        ),
        'training' => array(
            '人材育成・人材確保', '雇用維持・促進', 
            '働き方改革・待遇改善', '女性活躍・多様性', 
            '若者・学生支援', 'シニア・障害者支援', 
            'IT導入・DX', '生産性向上・業務効率化'
        ),
        'sales' => array(
            '販路拡大', '事業拡大', '新規事業・第二創業', 
            'ものづくり・新商品開発', '広告・マーケティング', 
            'EC・オンライン販売', '展示会・商談会', 
            '海外展開', '観光・インバウンド'
        ),
        'startup' => array(
            '創業・スタートアップ', '新規事業・第二創業', 
            '事業拡大', '販路拡大', '資金調達', 
            'IT導入・DX', '人材育成・人材確保', 
            '起業・独立'
        ),
        'digital' => array(
            'IT導入・DX', '生産性向上・業務効率化', 
            'EC・オンライン販売', '働き方改革・待遇改善', 
            'クラウド・SaaS', 'セキュリティ', 
            'AI・IoT・先端技術', '設備投資'
        ),
        'funding' => array(
            '資金調達', '運転資金', '設備投資', 
            '事業拡大', '創業・スタートアップ', 
            '事業再構築・転換', '新規事業・第二創業'
        ),
        'environment' => array(
            '省エネ・再エネ', '環境保護・脱炭素', 
            '設備投資', '生産性向上・業務効率化', 
            'SDGs', '循環型経済', '農業・林業・漁業'
        ),
        'global' => array(
            '海外展開', '輸出促進', '観光・インバウンド', 
            '販路拡大', 'クールジャパン・コンテンツ', 
            '国際交流', '展示会・商談会'
        ),
        
        // ===== 5 Additional Purposes =====
        'succession' => array(
            '事業承継', 'M&A', '経営改善', 
            '事業再構築・転換', '後継者育成', 
            '人材育成・人材確保'
        ),
        'rnd' => array(
            '研究開発', 'AI・IoT・先端技術', 
            'ものづくり・新商品開発', '設備投資', 
            '産学連携', 'イノベーション', 
            '特許・知的財産'
        ),
        'housing' => array(
            '住宅支援', 'リフォーム・改修', 
            '省エネ・再エネ', '防犯・防災・BCP', 
            '空き家対策', '子育て支援', 
            '移住・定住'
        ),
        'agriculture' => array(
            '農業・林業・漁業', '6次産業化', 
            '設備投資', '販路拡大', 
            '省エネ・再エネ', '人材育成・人材確保', 
            '地域活性化'
        ),
        'individual' => array(
            '起業・独立', 'フリーランス', 
            '資格取得・スキルアップ', '若者・学生支援', 
            '創業・スタートアップ', 'テレワーク・在宅ワーク', 
            '副業・兼業'
        )
    );
    
    return $mapping;
}

/**
 * Get grant categories for a specific purpose
 */
function gi_get_categories_for_purpose($purpose_slug) {
    $mapping = gi_get_purpose_category_mapping();
    
    if (!isset($mapping[$purpose_slug])) {
        return array();
    }
    
    $category_names = $mapping[$purpose_slug];
    
    $terms = get_terms(array(
        'taxonomy' => 'grant_category',
        'name' => $category_names,
        'hide_empty' => false
    ));
    
    if (is_wp_error($terms)) {
        return array();
    }
    
    return $terms;
}

/**
 * Get category slugs for a specific purpose
 */
function gi_get_category_slugs_for_purpose($purpose_slug) {
    $terms = gi_get_categories_for_purpose($purpose_slug);
    $slugs = array();
    
    if (empty($terms)) {
        return $slugs;
    }
    
    foreach ($terms as $term) {
        $slugs[] = $term->slug;
    }
    
    return $slugs;
}

/**
 * Load Required Include Files
 */
$inc_dir = get_template_directory() . '/inc/';

$required_files = array(
    // Core files
    'theme-foundation.php',
    'data-processing.php',
    
    // Admin & UI
    'admin-functions.php',
    'acf-fields.php',
    'customizer-error-handler.php',
    
    // Core functionality
    'card-display.php',
    'ajax-functions.php',
    
    // AI Assistant Core
    'ai-assistant-core.php',
    
    // Performance optimization
    'performance-optimization.php',
    
    // Google Sheets integration
    'google-sheets-integration.php',
    'safe-sync-manager.php',
    
    // Dynamic CSS Generator
    'grant-dynamic-css-generator.php',
    
    // Column System
    'column-system.php',
    
    // Grant Amount Fixer
    'grant-amount-fixer.php',
);

foreach ($required_files as $file) {
    $file_path = $inc_dir . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } elseif (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Grant Insight: Missing required file: ' . $file);
    }
}

/**
 * ============================================================================
 * CONTENT FILTER: Remove Duplicate Sections (ACTIVE)
 * ============================================================================
 * 
 * 本文（the_content）から重複する特定のセクションを削除する
 * 
 * 【有効化理由】(2025-11-27)
 * - 重複コンテンツがユーザー体験を悪化させているため
 * - single-grant.php の「詳細情報」セクションでACFフィールドから表示される内容と、
 *   本文で重複する部分を削除することで、ページの可読性を向上
 * 
 * 【削除対象】
 * - テンプレート（ACFフィールド）で既に表示されているセクション
 * - 本文中の見出しで始まる重複セクション
 */
function remove_duplicate_sections_from_content($content) {
    // 助成金（grant）の個別ページ以外では実行しない
    if (!is_singular('grant')) {
        return $content;
    }
    
    // 空のコンテンツは処理しない
    if (empty(trim($content))) {
        return $content;
    }

    // 削除したい見出しのリスト
    $targets = [
        // 完全一致パターン
        '対象経費（詳細）',
        '必要書類（詳細）',
        '対象者・対象事業',
        '■対象経費（詳細）',
        '■必要書類（詳細）',
        '■対象者・対象事業',
        // 表記ゆれ対策
        '対象経費',
        '必要書類',
        '対象者',
        '対象事業',
        // 追加パターン
        '補助対象経費',
        '申請書類',
        '提出書類',
    ];

    foreach ($targets as $target) {
        $escaped_target = preg_quote($target, '/');
        
        // パターン1: <h2>〜</h2> 見出しから次の同レベル以上の見出しまで削除
        $pattern1 = '/<h([2-4])[^>]*>\s*(?:■|●|◆|▼|【|★)?\s*' . $escaped_target . '.*?<\/h\1>[\s\S]*?(?=<h[2-4]|$)/iu';
        
        // パターン2: <p><strong>見出し</strong></p> 形式
        $pattern2 = '/<p[^>]*>\s*<strong>\s*(?:■|●|◆|▼|【|★)?\s*' . $escaped_target . '.*?<\/strong>\s*<\/p>[\s\S]*?(?=<p[^>]*>\s*<strong>|<h[2-6]|$)/iu';
        
        $content = preg_replace($pattern1, '', $content);
        $content = preg_replace($pattern2, '', $content);
    }
    
    // 空の段落タグを削除
    $content = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $content);
    
    // 連続した改行を整理
    $content = preg_replace('/(\s*<br\s*\/?>\s*){3,}/i', '<br><br>', $content);

    return $content;
}
add_filter('the_content', 'remove_duplicate_sections_from_content', 20);

/**
 * ============================================================================
 * DISABLED FUNCTIONS - SEO DUPLICATE PREVENTION
 * ============================================================================
 * 
 * 以下の関数は header.php で既に出力されているため無効化しました。
 * 
 * 1. gi_add_seo_meta_tags() - DISABLED
 *    理由: header.php の ji_get_current_page_info() が以下を出力済み
 *    - <meta name="description">
 *    - <link rel="canonical">
 *    - <meta property="og:*"> (OGPタグ全般)
 *    - <meta name="twitter:*"> (Twitterカード)
 * 
 * 2. gi_inject_inline_cta() - DISABLED
 *    理由: コンテンツの自然な流れを妨げる可能性がある
 *    CTAが必要な場合は single-grant.php 内で直接配置を推奨
 * 
 * 3. gi_remove_duplicate_acf_content() - DISABLED
 *    理由: remove_duplicate_sections_from_content() と機能が重複
 */

/**
 * ============================================================================
 * REST API SETTINGS
 * ============================================================================
 */
function gi_enqueue_rest_api_settings() {
    wp_enqueue_script('jquery');
    
    wp_localize_script('jquery', 'wpApiSettings', array(
        'root' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest')
    ));
    
    wp_localize_script('jquery', 'ajaxSettings', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}
add_action('wp_enqueue_scripts', 'gi_enqueue_rest_api_settings');

/**
 * Enqueue External CSS and JS Files
 * 外部ファイル化されたCSS/JSの読み込み
 */
function gi_enqueue_external_assets() {
    $template_dir = get_template_directory();
    $template_uri = get_template_directory_uri();
    

    
    // Front Page (フロントページ)
    if (is_front_page() || is_home()) {
        // Front page main styles and scripts (base styles)
        if (file_exists($template_dir . '/assets/css/front-page.css')) {
            wp_enqueue_style(
                'gi-front-page',
                $template_uri . '/assets/css/front-page.css',
                array('wp-block-library'), // Depend on WordPress core styles
                filemtime($template_dir . '/assets/css/front-page.css'),
                'all'
            );
        }
        
        // Hero section (depends on front-page base styles)
        if (file_exists($template_dir . '/assets/css/section-hero.css')) {
            wp_enqueue_style(
                'gi-section-hero',
                $template_uri . '/assets/css/section-hero.css',
                array('gi-front-page'),
                filemtime($template_dir . '/assets/css/section-hero.css'),
                'all'
            );
        }
        
        // Search section (depends on front-page base styles)
        if (file_exists($template_dir . '/assets/css/section-search.css')) {
            wp_enqueue_style(
                'gi-section-search',
                $template_uri . '/assets/css/section-search.css',
                array('gi-front-page'),
                filemtime($template_dir . '/assets/css/section-search.css'),
                'all'
            );
        }
        
        // Grant tabs section (depends on front-page base styles)
        if (file_exists($template_dir . '/assets/css/grant-tabs.css')) {
            wp_enqueue_style(
                'gi-grant-tabs',
                $template_uri . '/assets/css/grant-tabs.css',
                array('gi-front-page'),
                filemtime($template_dir . '/assets/css/grant-tabs.css'),
                'all'
            );
        }
        
        // JavaScript files
        if (file_exists($template_dir . '/assets/js/front-page.js')) {
            wp_enqueue_script(
                'gi-front-page-js',
                $template_uri . '/assets/js/front-page.js',
                array('jquery'),
                filemtime($template_dir . '/assets/js/front-page.js'),
                true
            );
        }
        
        if (file_exists($template_dir . '/assets/js/section-hero.js')) {
            wp_enqueue_script(
                'gi-section-hero-js',
                $template_uri . '/assets/js/section-hero.js',
                array('jquery', 'gi-front-page-js'),
                filemtime($template_dir . '/assets/js/section-hero.js'),
                true
            );
        }
        
        if (file_exists($template_dir . '/assets/js/section-search.js')) {
            wp_enqueue_script(
                'gi-section-search-js',
                $template_uri . '/assets/js/section-search.js',
                array('jquery', 'gi-front-page-js'),
                filemtime($template_dir . '/assets/js/section-search.js'),
                true
            );
            
            // Localize script with AJAX configuration
            wp_localize_script('gi-section-search-js', 'giSearchConfig', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gi_ajax_nonce'),
                'grantsUrl' => home_url('/grants/'),
                'municipalityUrl' => home_url('/grant_municipality/')
            ));
        }
        
        if (file_exists($template_dir . '/assets/js/grant-tabs.js')) {
            wp_enqueue_script(
                'gi-grant-tabs-js',
                $template_uri . '/assets/js/grant-tabs.js',
                array('jquery', 'gi-front-page-js'),
                filemtime($template_dir . '/assets/js/grant-tabs.js'),
                true
            );
        }
    }
    
    // Single Column Page (コラム記事詳細)
    if (is_singular('column') || (is_page() && get_page_template_slug() === 'single-column.php')) {
        if (file_exists($template_dir . '/assets/css/single-column.css')) {
            wp_enqueue_style(
                'gi-single-column',
                $template_uri . '/assets/css/single-column.css',
                array('wp-block-library'),
                filemtime($template_dir . '/assets/css/single-column.css'),
                'all'
            );
        }
        
        if (file_exists($template_dir . '/assets/js/single-column.js')) {
            wp_enqueue_script(
                'gi-single-column-js',
                $template_uri . '/assets/js/single-column.js',
                array('jquery'),
                filemtime($template_dir . '/assets/js/single-column.js'),
                true
            );
        }
    }
    
    // Single Grant Page (補助金詳細)
    if (is_singular('grant') || (is_page() && get_page_template_slug() === 'single-grant.php')) {
        if (file_exists($template_dir . '/assets/css/single-grant.css')) {
            wp_enqueue_style(
                'gi-single-grant',
                $template_uri . '/assets/css/single-grant.css',
                array('wp-block-library'),
                filemtime($template_dir . '/assets/css/single-grant.css'),
                'all'
            );
        }
        
        if (file_exists($template_dir . '/assets/js/single-grant.js')) {
            wp_enqueue_script(
                'gi-single-grant-js',
                $template_uri . '/assets/js/single-grant.js',
                array('jquery'),
                filemtime($template_dir . '/assets/js/single-grant.js'),
                true
            );
        }
    }
    
    // Archive Pages (アーカイブページ共通 - archive-grant, taxonomy-*)
    // 補助金アーカイブ、カテゴリ、都道府県、市町村、用途、タグアーカイブで使用
    if (is_post_type_archive('grant') || 
        is_post_type_archive('column') ||
        is_tax('grant_category') || 
        is_tax('grant_prefecture') || 
        is_tax('grant_municipality') || 
        is_tax('grant_purpose') || 
        is_tax('grant_tag') ||
        is_tax('column_category')) {
        
        // Archive Common CSS
        if (file_exists($template_dir . '/assets/css/archive-common.css')) {
            wp_enqueue_style(
                'gi-archive-common',
                $template_uri . '/assets/css/archive-common.css',
                array('wp-block-library'),
                filemtime($template_dir . '/assets/css/archive-common.css'),
                'all'
            );
        }
        
        // Archive Common JavaScript
        if (file_exists($template_dir . '/assets/js/archive-common.js')) {
            wp_enqueue_script(
                'gi-archive-common-js',
                $template_uri . '/assets/js/archive-common.js',
                array('jquery'),
                filemtime($template_dir . '/assets/js/archive-common.js'),
                true
            );
            
            // Localize script with AJAX configuration
            wp_localize_script('gi-archive-common-js', 'giArchiveConfig', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gi_ajax_nonce'),
                'postType' => is_post_type_archive('column') || is_tax('column_category') ? 'column' : 'grant'
            ));
        }
    }
}
add_action('wp_enqueue_scripts', 'gi_enqueue_external_assets', 1);

/**
 * Dequeue unused CSS on front-end
 * フロントエンドで不要なCSSを除去
 */
function gi_dequeue_unused_assets() {
    // ログインしていないユーザーには dashicons, admin-bar を読み込まない
    if (!is_user_logged_in()) {
        wp_dequeue_style('dashicons');
        wp_dequeue_style('admin-bar');
    }
    
    // block-library の未使用スタイルを削除（Gutenbergを使っていない場合）
    if (is_front_page() || is_home()) {
        // フロントページでは wp-block-library のスタイルは部分的に必要なので残す
        // 代わりに、使用していない Jetpack などのスタイルを削除
        wp_dequeue_style('jetpack-carousel');
        wp_dequeue_style('tiled-gallery');
    }
}
add_action('wp_enqueue_scripts', 'gi_dequeue_unused_assets', 100);

/**
 * Add defer attribute to non-critical JavaScript
 * 重要でないJavaScriptにdefer属性を追加
 */
function gi_add_defer_attribute($tag, $handle) {
    // jQuery は defer しない（多くのスクリプトが依存しているため）
    if ('jquery' === $handle || 'jquery-core' === $handle || 'jquery-migrate' === $handle) {
        return $tag;
    }
    
    // Our custom scripts に defer を追加
    $defer_scripts = array(
        'gi-front-page-js',
        'gi-section-hero-js',
        'gi-section-search-js',
        'gi-grant-tabs-js',
        'gi-single-column-js',
        'gi-single-grant-js',
        'gi-archive-common-js'
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'gi_add_defer_attribute', 10, 2);

/**
 * Add preload for critical CSS
 * クリティカルCSSのプリロード
 */
function gi_add_css_preload() {
    if (is_front_page() || is_home()) {
        $template_uri = get_template_directory_uri();
        $template_dir = get_template_directory();
        
        // Preload front-page CSS
        if (file_exists($template_dir . '/assets/css/front-page.css')) {
            echo '<link rel="preload" as="style" href="' . esc_url($template_uri . '/assets/css/front-page.css?ver=' . filemtime($template_dir . '/assets/css/front-page.css')) . '" />' . "\n";
        }
        
        // Preload section-hero CSS
        if (file_exists($template_dir . '/assets/css/section-hero.css')) {
            echo '<link rel="preload" as="style" href="' . esc_url($template_uri . '/assets/css/section-hero.css?ver=' . filemtime($template_dir . '/assets/css/section-hero.css')) . '" />' . "\n";
        }
    }
}
add_action('wp_head', 'gi_add_css_preload', 1);

/**
 * Optimize images - add loading="lazy" except for hero image
 * 画像の最適化 - ヒーロー画像以外に loading="lazy" を追加
 */
function gi_add_lazy_loading($attr, $attachment) {
    // LCP画像（ヒーロー画像）には loading="lazy" を付けない
    if (isset($attr['class']) && strpos($attr['class'], 'hero__image') !== false) {
        $attr['loading'] = 'eager';
        $attr['fetchpriority'] = 'high';
    } else {
        $attr['loading'] = 'lazy';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'gi_add_lazy_loading', 10, 2);

/**
 * Remove query strings from static resources
 * 静的リソースからクエリ文字列を削除（キャッシュ改善）
 */
function gi_remove_query_strings($src) {
    // filemtime() バージョンは保持（キャッシュバスティングに必要）
    // ただし、外部リソース（CDN等）のクエリ文字列は削除
    if (strpos($src, get_site_url()) !== false || strpos($src, 'ver=') === false) {
        return $src;
    }
    return remove_query_arg('ver', $src);
}
add_filter('style_loader_src', 'gi_remove_query_strings', 10, 1);
add_filter('script_loader_src', 'gi_remove_query_strings', 10, 1);

/**
 * ============================================================================
 * ADDITIONAL INCLUDE FILES
 * ============================================================================
 */

// Affiliate Ad Manager System
$affiliate_ad_file = get_template_directory() . '/inc/affiliate-ad-manager.php';
if (file_exists($affiliate_ad_file)) {
    require_once $affiliate_ad_file;
}

// Access Tracking System
$access_tracking_file = get_template_directory() . '/inc/access-tracking.php';
if (file_exists($access_tracking_file)) {
    require_once $access_tracking_file;
}

// SEO Content Manager
$seo_content_manager_file = get_template_directory() . '/inc/seo-content-manager.php';
if (file_exists($seo_content_manager_file)) {
    require_once $seo_content_manager_file;
}

// AI補助金コンシェルジュ読み込み
$ai_concierge_file = get_template_directory() . '/inc/ai-concierge.php';
if (file_exists($ai_concierge_file)) {
    require_once $ai_concierge_file;
}

// 補助金記事作成ツール読み込み
$grant_article_creator_file = get_template_directory() . '/inc/grant-article-creator.php';
if (file_exists($grant_article_creator_file)) {
    require_once $grant_article_creator_file;
}

// URLスラッグ最適化システム（日本語URL → 投稿IDベースURL + 301リダイレクト）
$grant_slug_optimizer_file = get_template_directory() . '/inc/grant-slug-optimizer.php';
if (file_exists($grant_slug_optimizer_file)) {
    require_once $grant_slug_optimizer_file;
}

// Phase 3: クリティカルCSS自動生成システム
$critical_css_file = get_template_directory() . '/inc/critical-css-generator.php';
if (file_exists($critical_css_file)) {
    require_once $critical_css_file;
}

// Phase 3: 画像次世代フォーマット対応強化（WebP/AVIF）
$image_optimization_file = get_template_directory() . '/inc/image-optimization.php';
if (file_exists($image_optimization_file)) {
    require_once $image_optimization_file;
}

/**
 * ============================================================================
 * PHASE 4: SEO 100点達成のための追加最適化
 * Lighthouse SEO Score 100/100 を目指す
 * @since 11.0.3
 * ============================================================================
 */

/**
 * 画像のalt属性を自動補完
 * Lighthouse SEO Audit: "Image elements have [alt] attributes"
 * 
 * @param array $attr 画像属性
 * @param WP_Post $attachment 添付ファイルオブジェクト
 * @param string $size 画像サイズ
 * @return array 修正された属性
 */
// DISABLED: ALT auto-generation harms accessibility (e.g. "Hero Bg" is meaningless)
// Decorative images should have alt="" (empty), not filename-based text
// add_filter('wp_get_attachment_image_attributes', 'gi_ensure_alt_attribute', 10, 3);
function gi_ensure_alt_attribute($attr, $attachment, $size) {
    if (empty($attr['alt'])) {
        // タイトルから取得
        $alt = get_the_title($attachment->ID);
        
        // タイトルも空の場合はファイル名から生成
        if (empty($alt)) {
            $file = basename(get_attached_file($attachment->ID));
            $alt = pathinfo($file, PATHINFO_FILENAME);
            $alt = str_replace(['-', '_'], ' ', $alt);
            $alt = ucwords($alt);
        }
        
        $attr['alt'] = $alt;
    }
    return $attr;
}

/**
 * コンテンツ内の画像にalt属性を自動追加
 * Lighthouse SEO Audit: "Image elements have [alt] attributes"
 * 
 * @param string $content 投稿コンテンツ
 * @return string 修正されたコンテンツ
 */
// DISABLED: ALT auto-generation from filename (same reason as above)
// add_filter('the_content', 'gi_add_alt_to_content_images', 20);
function gi_add_alt_to_content_images($content) {
    if (empty($content)) return $content;
    
    // alt=""（空のalt）を検出して修正
    $content = preg_replace_callback(
        '/<img([^>]*)\s+alt=[\'\"]{2}([^>]*)>/i',
        function($matches) {
            // src属性からファイル名を抽出
            preg_match('/src=[\'"]([^\'"]+)[\'"]/i', $matches[0], $src);
            if (!empty($src[1])) {
                $filename = pathinfo(parse_url($src[1], PHP_URL_PATH), PATHINFO_FILENAME);
                $alt = ucwords(str_replace(['-', '_'], ' ', $filename));
                return '<img' . $matches[1] . ' alt="' . esc_attr($alt) . '"' . $matches[2] . '>';
            }
            return $matches[0];
        },
        $content
    );
    
    // alt属性がない画像を検出して追加
    $content = preg_replace_callback(
        '/<img((?![^>]*alt=)[^>]*)>/i',
        function($matches) {
            preg_match('/src=[\'"]([^\'"]+)[\'"]/i', $matches[0], $src);
            if (!empty($src[1])) {
                $filename = pathinfo(parse_url($src[1], PHP_URL_PATH), PATHINFO_FILENAME);
                $alt = ucwords(str_replace(['-', '_'], ' ', $filename));
                return '<img' . $matches[1] . ' alt="' . esc_attr($alt) . '">';
            }
            return $matches[0];
        },
        $content
    );
    
    return $content;
}

/**
 * Organization Schema（サイト全体の構造化データ）
 * Google検索でのリッチリザルト表示を強化
 */
add_action('wp_head', 'gi_add_organization_schema', 10);
function gi_add_organization_schema() {
    if (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => '助成金インサイト',
            'url' => home_url('/'),
            'logo' => 'https://joseikin-insight.com/wp-content/uploads/2025/05/cropped-logo3.webp',
            'description' => '中小企業・個人事業主のための補助金・助成金検索サイト。最新の補助金情報を専門家監修のもとわかりやすく解説。',
            'sameAs' => array(
                'https://twitter.com/joseikininsight',
                'https://facebook.com/joseikin.insight',
                'https://www.youtube.com/channel/UCbfjOrG3nSPI3GFzKnGcspQ',
                'https://note.com/joseikin_insight'
            ),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'url' => home_url('/contact/'),
                'availableLanguage' => 'Japanese'
            )
        );
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}

/**
 * WebSite Schema with SearchAction（サイト内検索機能の構造化データ）
 * Google検索結果にサイト内検索ボックスを表示
 */
add_action('wp_head', 'gi_add_website_schema', 10);
function gi_add_website_schema() {
    if (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => '助成金インサイト',
            'url' => home_url('/'),
            'description' => '全国の補助金・助成金を簡単検索。中小企業診断士監修のもと毎日更新。',
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => array(
                    '@type' => 'EntryPoint',
                    'urlTemplate' => home_url('/grant/?search={search_term_string}')
                ),
                'query-input' => 'required name=search_term_string'
            )
        );
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}

/**
 * robots.txt の確認用デバッグ関数
 * （本番環境では使用しない）
 */
function gi_check_robots_txt() {
    if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options') && isset($_GET['debug_robots'])) {
        header('Content-Type: text/plain');
        echo "=== robots.txt Debug ===\n\n";
        echo "Site URL: " . home_url('/') . "\n";
        echo "robots.txt URL: " . home_url('/robots.txt') . "\n\n";
        echo "Expected Content:\n";
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /wp-admin/\n";
        echo "Disallow: /wp-includes/\n";
        echo "Sitemap: " . home_url('/sitemap_index.xml') . "\n";
        exit;
    }
}
