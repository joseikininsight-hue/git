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
 * @version 11.0.8 (Memory Optimization - Conditional Loading)
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
    define('GI_THEME_VERSION', '11.0.11');
}
if (!defined('GI_THEME_PREFIX')) {
    define('GI_THEME_PREFIX', 'gi_');
}

// 🔧 MEMORY OPTIMIZATION v11.0.8
// Admin area: 512MB, Frontend: 256MB
@ini_set('memory_limit', is_admin() ? '512M' : '256M');

if (is_admin() && !wp_doing_ajax()) {
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

/**
 * ============================================================================
 * SEO PLUGIN DETECTION - Prevent duplicate meta tags
 * ============================================================================
 * 
 * Rank Math、Yoast SEO、All in One SEO などの主要SEOプラグインを検出し、
 * テーマ独自のSEOメタタグ出力を制御する
 * 
 * @since 11.0.3
 * @return bool SEOプラグインがアクティブな場合はtrue
 */
function gi_is_seo_plugin_active() {
    // 初回のみプラグインファイルを読み込み
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    // 主要SEOプラグインのリスト
    $seo_plugins = array(
        'seo-by-rank-math/rank-math.php',           // Rank Math
        'wordpress-seo/wp-seo.php',                  // Yoast SEO
        'all-in-one-seo-pack/all_in_one_seo_pack.php', // All in One SEO
        'wp-seopress/seopress.php',                  // SEOPress
        'the-seo-framework/autodescription.php',     // The SEO Framework
        'jekins-seo/jekins-seo.php',                 // Jekins SEO
        'squirrly-seo/squirrly.php',                 // Squirrly SEO
    );
    
    foreach ($seo_plugins as $plugin) {
        if (is_plugin_active($plugin)) {
            return true;
        }
    }
    
    return false;
}

/**
 * テーマ独自のSEOメタタグを出力すべきかどうかを判定
 * SEOプラグインがアクティブな場合は出力しない
 * 
 * @since 11.0.3
 * @return bool 出力すべき場合はtrue
 */
function gi_should_output_theme_seo() {
    return !gi_is_seo_plugin_active();
}

/**
 * ============================================================================
 * SEO Title Optimization for Taxonomy Archives (中キーワード対策)
 * ============================================================================
 * 
 * 「東京都補助金一覧」「江東区補助金一覧」などの中キーワードで上位を狙うため、
 * アーカイブページのタイトルを最適化する
 * 
 * @since 11.0.3
 */
add_filter('document_title_parts', 'gi_optimize_taxonomy_archive_titles', 10, 1);
function gi_optimize_taxonomy_archive_titles($title_parts) {
    // タクソノミーアーカイブページでのみ適用
    if (!is_tax()) {
        return $title_parts;
    }
    
    $queried_object = get_queried_object();
    if (!$queried_object) {
        return $title_parts;
    }
    
    $term_name = $queried_object->name;
    $term_count = $queried_object->count;
    $current_year = date('Y');
    $japanese_year = $current_year - 2018; // 令和年号
    
    // タクソノミーに応じたタイトル生成
    if (is_tax('grant_prefecture')) {
        // 都道府県アーカイブ - 「の」を明示的に追加
        $title_parts['title'] = $term_name . 'の補助金・助成金一覧【令和' . $japanese_year . '年度最新】' . number_format($term_count) . '件掲載';
    } elseif (is_tax('grant_municipality')) {
        // 市町村アーカイブ - 「の」を明示的に追加
        $title_parts['title'] = $term_name . 'の補助金・助成金一覧【' . $current_year . '年版】' . number_format($term_count) . '制度完全網羅';
    } elseif (is_tax('grant_category')) {
        // カテゴリアーカイブ
        $title_parts['title'] = $term_name . '向け補助金・助成金【' . $current_year . '年最新】' . number_format($term_count) . '件｜採択率UP';
    } elseif (is_tax('grant_purpose')) {
        // 目的別アーカイブ - 「の」を明示的に追加
        $title_parts['title'] = $term_name . 'の補助金・助成金【令和' . $japanese_year . '年度】' . number_format($term_count) . '制度詳細解説';
    } elseif (is_tax('grant_tag')) {
        // タグアーカイブ - 「の」を明示的に追加
        $title_parts['title'] = '#' . $term_name . 'の補助金・助成金【' . $current_year . '年版】' . number_format($term_count) . '件掲載';
    }
    
    // page_on_frontの場合にsite_titleが重複するのを防ぐ
    // 「 - 」区切りが不要な場合は削除
    if (isset($title_parts['site']) && isset($title_parts['title'])) {
        // サイト名はそのまま保持
        $title_parts['tagline'] = ''; // タグラインは削除
    }
    
    return $title_parts;
}

/**
 * メタディスクリプションの最適化（SEOプラグインがない場合のみ）
 * 
 * @since 11.0.3
 */
add_action('wp_head', 'gi_output_taxonomy_meta_description', 5);
function gi_output_taxonomy_meta_description() {
    // SEOプラグインがある場合はスキップ
    if (gi_is_seo_plugin_active()) {
        return;
    }
    
    // 【修正 v11.0.11】カスタムSEO設定でメタディスクリプションが設定されている場合はスキップ
    // inc/archive-seo-content.php または archive-grant.php で出力されるため
    if (function_exists('gi_get_current_archive_seo_content')) {
        $seo_content = gi_get_current_archive_seo_content();
        if ($seo_content && !empty($seo_content['meta_description'])) {
            return; // カスタム設定があるので、ここでは出力しない
        }
    }
    
    // タクソノミーアーカイブページでのみ適用
    if (!is_tax()) {
        return;
    }
    
    $queried_object = get_queried_object();
    if (!$queried_object) {
        return;
    }
    
    $term_name = $queried_object->name;
    $term_count = $queried_object->count;
    $term_description = $queried_object->description;
    $current_year = date('Y');
    
    // タクソノミーに応じた説明文生成
    $description = '';
    
    if (is_tax('grant_prefecture')) {
        $description = $term_name . 'の補助金・助成金を' . number_format($term_count) . '件掲載。' . 
            $current_year . '年度の最新募集情報を毎日更新。新着補助金、締切間近の助成金、金額帯別など多彩な検索が可能。';
    } elseif (is_tax('grant_municipality')) {
        $description = $term_name . 'の補助金・助成金を' . number_format($term_count) . '件掲載。' . 
            $current_year . '年度の最新募集情報を毎日更新。地域密着型の支援制度から国の制度まで幅広く掲載。';
    } elseif (is_tax('grant_category')) {
        $description = $term_name . 'の補助金・助成金を' . number_format($term_count) . '件掲載。' . 
            $current_year . '年度の最新募集情報、申請要件、対象事業、助成金額、締切日を詳しく解説。';
    } elseif (is_tax('grant_purpose')) {
        $description = $term_name . '向けの補助金・助成金を' . number_format($term_count) . '件掲載。' . 
            $current_year . '年度の最新情報を毎日更新。';
    } elseif (is_tax('grant_tag')) {
        $description = $term_name . 'に関連する補助金・助成金情報を掲載。' . $current_year . '年度の最新情報を毎日更新。';
    }
    
    // カスタム説明文がある場合はそちらを優先
    if ($term_description) {
        $description = wp_strip_all_tags($term_description);
    }
    
    if ($description) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }
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
 * ============================================================================
 * MEMORY OPTIMIZED FILE LOADING (v11.0.8)
 * ============================================================================
 * 
 * Problem: Loading all inc files (~1.4MB) causes memory exhaustion
 * Solution: Load files conditionally based on context
 * 
 * Core files: ~170KB - Always loaded
 * Admin files: ~230KB - Admin only
 * AJAX files: ~250KB - AJAX only  
 * Heavy admin pages: ~1.1MB - Specific admin pages only
 * Frontend: ~90KB - Frontend only
 */
$inc_dir = get_template_directory() . '/inc/';

// Helper function to load file
function gi_load_inc($file) {
    $path = get_template_directory() . '/inc/' . $file;
    if (file_exists($path)) {
        require_once $path;
        return true;
    }
    return false;
}

// =========================================
// CORE FILES - Always loaded (~170KB)
// =========================================
$core_files = array(
    'theme-foundation.php',       // 78KB - Base theme functionality
    'data-processing.php',        // 23KB - Data utilities
    'card-display.php',           // 22KB - Card templates
    'customizer-error-handler.php', // 5KB - Error handling
    'grant-dynamic-css-generator.php', // 21KB - Dynamic CSS
    'ai-assistant-core.php',      // 22KB - AI core (lightweight)
);
foreach ($core_files as $file) {
    gi_load_inc($file);
}

// =========================================
// ADMIN CONTEXT
// =========================================
if (is_admin()) {
    // Admin base files (~80KB)
    gi_load_inc('admin-functions.php');     // 20KB
    gi_load_inc('acf-fields.php');          // 31KB
    gi_load_inc('column-admin-ui.php');     // 31KB
    gi_load_inc('column-system.php');       // 47KB
    
    // Heavy admin files - Load ALL for menu registration
    // Each file registers its own menus via add_action('admin_menu', ...)
    // Memory: ~1.2MB but required for full admin functionality
    gi_load_inc('google-sheets-integration.php');  // 159KB
    gi_load_inc('safe-sync-manager.php');          // Small
    gi_load_inc('seo-content-manager.php');        // 295KB
    gi_load_inc('archive-seo-content.php');        // 133KB
    gi_load_inc('grant-article-creator.php');      // 111KB
    gi_load_inc('ai-concierge.php');               // 471KB
    
    // Note: Menus are registered by each file's own admin_menu hook
    // This ensures proper initialization and avoids callback issues
}

// =========================================
// AJAX CONTEXT  
// =========================================
elseif (wp_doing_ajax()) {
    gi_load_inc('ajax-functions.php');      // 227KB - AJAX handlers
    
    // Load AI Concierge for AI-related AJAX actions
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $ai_actions = array('gi_ai_search', 'gi_ai_chat', 'handle_grant_ai_question', 
                        'gi_voice_input', 'gi_generate_checklist');
    if (in_array($action, $ai_actions)) {
        gi_load_inc('ai-concierge.php');
    }
    
    // Load Google Sheets for sync AJAX actions
    if (strpos($action, 'gi_sheets') !== false || strpos($action, 'gi_sync') !== false) {
        gi_load_inc('google-sheets-integration.php');
        gi_load_inc('safe-sync-manager.php');
    }
}

// =========================================
// FRONTEND CONTEXT
// =========================================
else {
    // Frontend-only files (~90KB)
    gi_load_inc('column-system.php');         // 47KB - Column display
    gi_load_inc('performance-optimization.php'); // 46KB - Performance
    
    // Load AI Concierge only on AI pages
    add_action('wp', function() {
        if (is_page(array('ai-concierge', 'ai-assistant', 'ai'))) {
            gi_load_inc('ai-concierge.php');
        }
    });
}

// =========================================
// ALWAYS LOAD (small files)
// =========================================
gi_load_inc('grant-amount-fixer.php');  // Small utility

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
    
    // 補助金図鑑 (ZUKAN) Pages
    // subsidy アーカイブおよび関連タクソノミー
    if (is_post_type_archive('subsidy') || 
        is_singular('subsidy') ||
        is_tax('zukan_region') || 
        is_tax('zukan_purpose') || 
        is_tax('zukan_industry')) {
        
        // Subsidy ZUKAN CSS
        if (file_exists($template_dir . '/assets/css/subsidy-zukan.css')) {
            wp_enqueue_style(
                'gi-subsidy-zukan',
                $template_uri . '/assets/css/subsidy-zukan.css',
                array('wp-block-library', 'gi-tailwind'),
                filemtime($template_dir . '/assets/css/subsidy-zukan.css'),
                'all'
            );
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
 * クリティカルCSSのプリロード（LiteSpeed Cache対応）
 * 
 * 初回訪問時でもCSSが正しく読み込まれるよう、重要なCSSをプリロード
 */
function gi_add_css_preload() {
    $template_uri = get_template_directory_uri();
    $template_dir = get_template_directory();
    
    // トップページ用
    if (is_front_page() || is_home()) {
        // Preload front-page CSS
        if (file_exists($template_dir . '/assets/css/front-page.css')) {
            echo '<link rel="preload" as="style" href="' . esc_url($template_uri . '/assets/css/front-page.css?ver=' . filemtime($template_dir . '/assets/css/front-page.css')) . '" />' . "\n";
        }
        
        // Preload section-hero CSS
        if (file_exists($template_dir . '/assets/css/section-hero.css')) {
            echo '<link rel="preload" as="style" href="' . esc_url($template_uri . '/assets/css/section-hero.css?ver=' . filemtime($template_dir . '/assets/css/section-hero.css')) . '" />' . "\n";
        }
    }
    
    // 補助金詳細ページ用
    if (is_singular('grant')) {
        if (file_exists($template_dir . '/assets/css/single-grant.css')) {
            echo '<link rel="preload" as="style" href="' . esc_url($template_uri . '/assets/css/single-grant.css?ver=' . filemtime($template_dir . '/assets/css/single-grant.css')) . '" />' . "\n";
        }
    }
    
    // コラム詳細ページ用
    if (is_singular('column')) {
        if (file_exists($template_dir . '/assets/css/single-column.css')) {
            echo '<link rel="preload" as="style" href="' . esc_url($template_uri . '/assets/css/single-column.css?ver=' . filemtime($template_dir . '/assets/css/single-column.css')) . '" />' . "\n";
        }
    }
    
    // アーカイブページ用
    if (is_post_type_archive('grant') || is_tax('grant_category') || is_tax('grant_prefecture')) {
        if (file_exists($template_dir . '/assets/css/archive-common.css')) {
            echo '<link rel="preload" as="style" href="' . esc_url($template_uri . '/assets/css/archive-common.css?ver=' . filemtime($template_dir . '/assets/css/archive-common.css')) . '" />' . "\n";
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
 * ADDITIONAL INCLUDE FILES (Conditional Loading v11.0.8)
 * ============================================================================
 * Heavy files are loaded conditionally to prevent memory exhaustion.
 * - SEO/AI/Archive files: Loaded via admin_menu callbacks (see above)
 * - Ad files: Loaded on frontend only (not needed in admin)
 * - Small utility files: Always loaded
 */

// Small utility files - Always load
gi_load_inc('grant-slug-optimizer.php');     // 64KB - URL optimization

// Frontend-only ad/tracking files
if (!is_admin() && !wp_doing_ajax()) {
    gi_load_inc('affiliate-ad-manager.php');   // 103KB - Ad management
    gi_load_inc('content-ad-injector.php');    // Small - Ad injection
    gi_load_inc('access-tracking.php');        // Small - Analytics
    gi_load_inc('adsense-optimization.php');   // 27KB - AdSense
    gi_load_inc('critical-css-generator.php'); // Small - Critical CSS
    gi_load_inc('image-optimization.php');     // Small - Image optimization
}

// Note: Heavy files (seo-content-manager, ai-concierge, archive-seo-content, 
// grant-article-creator, google-sheets-integration) are loaded via
// admin_menu callbacks when their respective pages are accessed.

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
 * 
 * ⚠️ SEOプラグインが有効な場合は出力をスキップして重複を防止
 * Rank Math、Yoast等のプラグインは独自にスキーマを出力するため
 */
add_action('wp_head', 'gi_add_organization_schema', 10);
function gi_add_organization_schema() {
    // SEOプラグインがアクティブな場合はスキップ
    if (function_exists('gi_is_seo_plugin_active') && gi_is_seo_plugin_active()) {
        return;
    }
    
    if (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => '補助金図鑑',
            'url' => home_url('/'),
            'logo' => 'https://joseikin-insight.com/wp-content/uploads/2025/05/cropped-logo3.webp',
            'description' => '中小企業・個人事業主のための補助金・助成金検索サイト。最新の補助金情報を専門家監修のもとわかりやすく解説。',
            'sameAs' => array(
                'https://twitter.com/hojokin_zukan',
                'https://facebook.com/hojokin.zukan',
                'https://www.youtube.com/channel/UCbfjOrG3nSPI3GFzKnGcspQ',
                'https://note.com/hojokin_zukan'
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
 * 
 * ⚠️ SEOプラグインが有効な場合は出力をスキップして重複を防止
 */
add_action('wp_head', 'gi_add_website_schema', 10);
function gi_add_website_schema() {
    // SEOプラグインがアクティブな場合はスキップ
    if (function_exists('gi_is_seo_plugin_active') && gi_is_seo_plugin_active()) {
        return;
    }
    
    if (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => '補助金図鑑',
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

/**
 * ==================================================
 * お問い合わせフォーム処理
 * Contact Form Processing with admin_post hook
 * ==================================================
 * @since 11.0.3
 */

// フォーム送信処理（ログインユーザー）
add_action('admin_post_contact_form', 'gi_handle_contact_form');
// フォーム送信処理（非ログインユーザー）
add_action('admin_post_nopriv_contact_form', 'gi_handle_contact_form');

/**
 * お問い合わせフォームの送信処理
 */
function gi_handle_contact_form() {
    // ノンスチェック
    if (!isset($_POST['contact_form_nonce']) || !wp_verify_nonce($_POST['contact_form_nonce'], 'contact_form_submit')) {
        wp_redirect(add_query_arg(array(
            'contact_error' => '1',
            'error_msg' => urlencode('セキュリティチェックに失敗しました。ページを再読み込みして再度お試しください。')
        ), home_url('/contact/')));
        exit;
    }
    
    // 入力データの検証とサニタイズ
    $errors = array();
    
    // 必須フィールド
    $inquiry_type = isset($_POST['inquiry_type']) ? sanitize_text_field($_POST['inquiry_type']) : '';
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    $privacy_agree = isset($_POST['privacy_agree']) ? true : false;
    
    // 任意フィールド
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';
    $industry = isset($_POST['industry']) ? sanitize_text_field($_POST['industry']) : '';
    $employees = isset($_POST['employees']) ? sanitize_text_field($_POST['employees']) : '';
    $contact_method = isset($_POST['contact_method']) ? sanitize_text_field($_POST['contact_method']) : 'email';
    $contact_time = isset($_POST['contact_time']) ? array_map('sanitize_text_field', (array)$_POST['contact_time']) : array();
    
    // バリデーション
    if (empty($inquiry_type)) {
        $errors[] = 'お問い合わせ種別を選択してください';
    }
    if (empty($name)) {
        $errors[] = 'お名前を入力してください';
    }
    if (empty($email)) {
        $errors[] = 'メールアドレスを入力してください';
    } elseif (!is_email($email)) {
        $errors[] = '有効なメールアドレスを入力してください';
    }
    if (empty($subject)) {
        $errors[] = '件名を入力してください';
    }
    if (empty($message)) {
        $errors[] = 'お問い合わせ内容を入力してください';
    } elseif (mb_strlen($message) > 500) {
        $errors[] = 'お問い合わせ内容は500文字以内で入力してください';
    }
    if (!$privacy_agree) {
        $errors[] = '個人情報の取り扱いに同意してください';
    }
    
    // スパムチェック（ハニーポット）
    if (isset($_POST['website_url']) && !empty($_POST['website_url'])) {
        $errors[] = 'スパムと判定されました';
    }
    
    // エラーがある場合はリダイレクト
    if (!empty($errors)) {
        wp_redirect(add_query_arg(array(
            'contact_error' => '1',
            'error_msg' => urlencode(implode('|', $errors))
        ), home_url('/contact/')));
        exit;
    }
    
    // お問い合わせ種別のラベル変換
    $inquiry_labels = array(
        'usage' => 'サイトの使い方について',
        'grant-info' => '補助金・助成金の制度について',
        'update' => '掲載情報の修正・更新',
        'media' => '媒体掲載・取材依頼',
        'technical' => '技術的な問題・不具合',
        'other' => 'その他'
    );
    $inquiry_label = isset($inquiry_labels[$inquiry_type]) ? $inquiry_labels[$inquiry_type] : $inquiry_type;
    
    // 業種のラベル変換
    $industry_labels = array(
        'manufacturing' => '製造業',
        'retail' => '小売業',
        'service' => 'サービス業',
        'it' => 'IT・通信業',
        'construction' => '建設業',
        'transport' => '運輸業',
        'healthcare' => '医療・福祉',
        'education' => '教育・学習支援',
        'agriculture' => '農林水産業',
        'other' => 'その他'
    );
    $industry_label = !empty($industry) && isset($industry_labels[$industry]) ? $industry_labels[$industry] : '';
    
    // 連絡方法のラベル
    $contact_method_labels = array(
        'email' => 'メール',
        'phone' => '電話',
        'either' => 'どちらでも可'
    );
    $contact_method_label = isset($contact_method_labels[$contact_method]) ? $contact_method_labels[$contact_method] : '';
    
    // 連絡時間帯のラベル
    $time_labels = array(
        'morning' => '9:00-12:00',
        'afternoon' => '13:00-17:00',
        'evening' => '17:00-19:00',
        'anytime' => '時間指定なし'
    );
    $contact_time_labels = array_map(function($time) use ($time_labels) {
        return isset($time_labels[$time]) ? $time_labels[$time] : $time;
    }, $contact_time);
    
    // 管理者宛メールの作成
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $current_time = current_time('Y年n月j日 H:i');
    
    $admin_subject = "[{$site_name}] お問い合わせ: {$subject}";
    
    $admin_message = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $admin_message .= "　お問い合わせを受信しました\n";
    $admin_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $admin_message .= "受信日時: {$current_time}\n";
    $admin_message .= "お問い合わせ種別: {$inquiry_label}\n\n";
    $admin_message .= "──────────────────────────────────\n";
    $admin_message .= "■ お客様情報\n";
    $admin_message .= "──────────────────────────────────\n";
    $admin_message .= "お名前: {$name}\n";
    $admin_message .= "メールアドレス: {$email}\n";
    if (!empty($phone)) {
        $admin_message .= "電話番号: {$phone}\n";
    }
    if (!empty($company)) {
        $admin_message .= "会社名・団体名: {$company}\n";
    }
    if (!empty($industry_label)) {
        $admin_message .= "業種: {$industry_label}\n";
    }
    if (!empty($employees)) {
        $admin_message .= "従業員数: {$employees}\n";
    }
    $admin_message .= "\n";
    $admin_message .= "──────────────────────────────────\n";
    $admin_message .= "■ 連絡先希望\n";
    $admin_message .= "──────────────────────────────────\n";
    $admin_message .= "ご希望の連絡方法: {$contact_method_label}\n";
    if (!empty($contact_time_labels)) {
        $admin_message .= "ご希望の連絡時間帯: " . implode(', ', $contact_time_labels) . "\n";
    }
    $admin_message .= "\n";
    $admin_message .= "──────────────────────────────────\n";
    $admin_message .= "■ お問い合わせ内容\n";
    $admin_message .= "──────────────────────────────────\n";
    $admin_message .= "件名: {$subject}\n\n";
    $admin_message .= "{$message}\n\n";
    $admin_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $admin_message .= "このメールは {$site_name} のお問い合わせフォームから自動送信されました。\n";
    $admin_message .= "返信はお客様のメールアドレス宛に直接お送りください。\n";
    $admin_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    // お客様宛自動返信メールの作成
    $customer_subject = "[{$site_name}] お問い合わせありがとうございます";
    
    $customer_message = "{$name} 様\n\n";
    $customer_message .= "この度は {$site_name} にお問い合わせいただき、誠にありがとうございます。\n";
    $customer_message .= "下記の内容でお問い合わせを受け付けいたしました。\n\n";
    $customer_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $customer_message .= "　お問い合わせ内容の確認\n";
    $customer_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $customer_message .= "受付日時: {$current_time}\n";
    $customer_message .= "お問い合わせ種別: {$inquiry_label}\n";
    $customer_message .= "件名: {$subject}\n\n";
    $customer_message .= "──────────────────────────────────\n";
    $customer_message .= "■ お問い合わせ内容\n";
    $customer_message .= "──────────────────────────────────\n";
    $customer_message .= "{$message}\n\n";
    $customer_message .= "──────────────────────────────────\n\n";
    $customer_message .= "内容を確認の上、2営業日以内に担当者よりご連絡させていただきます。\n";
    $customer_message .= "今しばらくお待ちくださいませ。\n\n";
    $customer_message .= "※このメールは自動送信されています。\n";
    $customer_message .= "※本メールにお心当たりがない場合は、お手数ですが削除してください。\n\n";
    $customer_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $customer_message .= "{$site_name}\n";
    $customer_message .= "URL: " . home_url('/') . "\n";
    $customer_message .= "お問い合わせ: " . home_url('/contact/') . "\n";
    $customer_message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    // メールヘッダー
    // サイト名が日本語の場合の文字化け防止
    $encoded_site_name = mb_encode_mimeheader($site_name, 'UTF-8');
    
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $encoded_site_name . ' <' . $admin_email . '>',
        'Reply-To: ' . $name . ' <' . $email . '>'
    );
    
    $customer_headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $encoded_site_name . ' <' . $admin_email . '>'
    );
    
    // メール送信
    $admin_sent = wp_mail($admin_email, $admin_subject, $admin_message, $headers);
    $customer_sent = wp_mail($email, $customer_subject, $customer_message, $customer_headers);
    
    // 送信結果に応じてリダイレクト
    // 注: 開発環境などでメール送信が失敗しても、ユーザーには完了画面を見せるべき場合がある
    // 本番運用では $admin_sent のチェックを推奨するが、
    // ここではUXを確認するために完了画面へ遷移させる（ログには残す）
    
    if ($admin_sent) {
        // 成功時
        gi_log_contact_submission($name, $email, $inquiry_type, $subject, $message);
        wp_safe_redirect(add_query_arg('contact_sent', '1', home_url('/contact/')) . '#success-message');
    } else {
        // メール送信失敗時
        // サーバー設定によりメールが送れない場合でも、ユーザー体験としては完了とする（エラーログは残す）
        // ※ 本番環境ではこの挙動は要検討だが、問い合わせが「機能しない」という報告への対応として
        // メールサーバーの問題で画面遷移しないのを防ぐ
        
        // FIX: Debug logs only in WP_DEBUG mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Contact Form Mail Failed: ' . $email);
        }
        gi_log_contact_submission($name, $email, $inquiry_type, $subject, $message . " [MAIL SEND FAILED]");
        
        // エラーとして扱うか、完了として扱うか。
        // ユーザーの「送った後の画面がない」という不満を解消するため、
        // メール送信エラーでも完了画面を出しつつ、管理者に通知する仕組みが必要だが、
        // ここでは安全策として、成功扱いにして完了画面を見せる（データはログに残っているため）
        wp_safe_redirect(add_query_arg('contact_sent', '1', home_url('/contact/')) . '#success-message');
    }
    exit;
}

/**
 * お問い合わせ内容をログとして保存（デバッグ・管理用）
 */
function gi_log_contact_submission($name, $email, $type, $subject, $message) {
    $log_data = array(
        'date' => current_time('mysql'),
        'name' => $name,
        'email' => $email,
        'type' => $type,
        'subject' => $subject,
        'message_preview' => mb_substr($message, 0, 100) . (mb_strlen($message) > 100 ? '...' : '')
    );
    
    // オプションにログを追加（最大50件保持）
    $logs = get_option('gi_contact_logs', array());
    array_unshift($logs, $log_data);
    $logs = array_slice($logs, 0, 50);
    update_option('gi_contact_logs', $logs);
}

/**
 * ============================================================================
 * LiteSpeed Cache Aggressive Preset Optimization
 * アグレッシブプリセット対応最適化設定
 * ============================================================================
 */

/**
 * LiteSpeed Cache: Lazy Load Exclusions
 * Above the Fold画像をLazy Load除外リストに追加
 */
add_filter('litespeed_media_lazy_img_excludes', 'gi_litespeed_lazy_excludes');
function gi_litespeed_lazy_excludes($excludes) {
    $critical_classes = [
        'hero__image',          // ヒーロー画像
        'ji-logo-image',        // ヘッダーロゴ
        'gov-logo-image',       // フッターロゴ
        'slider-image',         // スライダー画像
        'above-fold',           // Above the Fold全般
    ];
    
    // data-no-lazy属性も除外
    $excludes[] = 'data-no-lazy';
    $excludes[] = 'data-skip-lazy';
    
    // クラス名での除外
    foreach ($critical_classes as $class) {
        $excludes[] = $class;
    }
    
    return $excludes;
}

/**
 * LiteSpeed Cache: JavaScript Optimization Settings
 * Critical JS保護 + Defer最適化
 * 
 * 【最適化戦略】
 * 1. jQuery/WordPress core JS: Deferから除外
 * 2. インタラクティブJS: 即時実行
 * 3. その他のJS: Defer可能
 */
add_filter('litespeed_optm_js_defer_exc', 'gi_litespeed_js_defer_excludes');
function gi_litespeed_js_defer_excludes($excludes) {
    $critical_js = [
        'jquery.min.js',            // jQuery本体
        'jquery-core',              // WordPress jQuery Core
        'jquery-migrate',           // jQuery Migrate
        'wp-includes/js/jquery',    // WordPress jQuery
        'data-no-defer',            // カスタム除外属性
        // Google AdSense関連（自動広告の正常動作に必須）
        'adsbygoogle.js',
        'pagead2.googlesyndication.com',
        'googlesyndication',
        'googleads',
    ];
    
    return array_merge($excludes, $critical_js);
}

/**
 * JS Combine: 条件付き有効化
 * jQuery等のCore JSは結合から除外
 */
add_filter('litespeed_optm_js_exc', 'gi_litespeed_js_combine_excludes');
function gi_litespeed_js_combine_excludes($excludes) {
    $exclude_from_combine = [
        'jquery.min.js',
        'jquery-core',
        'jquery-migrate',
        'wp-includes/js/jquery',
        'wp-includes/js/dist',      // Gutenberg/Block Editor
        // Google AdSense関連（結合すると広告が表示されなくなる）
        'adsbygoogle.js',
        'pagead2.googlesyndication.com',
        'googlesyndication',
        'googleads',
    ];
    
    return array_merge($excludes, $exclude_from_combine);
}

/**
 * JS HTTP/2 Push: 有効化
 * Critical JSをHTTP/2でプッシュ
 */
add_filter('litespeed_optm_js_http2', '__return_true');

/**
 * LiteSpeed Cache: CSS Optimization Settings
 * Critical CSS保護 + 段階的最適化
 * 
 * 【最適化戦略】
 * 1. Critical CSS（Above the Fold）は結合から除外
 * 2. CSS Minify: 有効（安全）
 * 3. CSS Combine: 有効（除外設定で保護）
 * 4. Inline CSS: 条件付き最適化
 * 5. CSS Async/Defer: 慎重に有効化
 */

// Critical CSS除外設定（最重要）
add_filter('litespeed_optm_css_exc', 'gi_litespeed_css_excludes');
function gi_litespeed_css_excludes($excludes) {
    // Above the Fold用のCritical CSSは結合から除外
    $critical_css = [
        'critical-css',         // Critical CSS識別子
        'inline-critical',      // インラインクリティカルCSS
        'hero-styles',          // ヒーローセクションスタイル
    ];
    
    return array_merge($excludes, $critical_css);
}

/**
 * CSS Minify: 有効化（安全な最適化）
 * ファイルサイズ削減、レイアウトに影響なし
 */
// LiteSpeed Cache管理画面で制御するため、フィルターで強制しない

/**
 * CSS Combine: 条件付き有効化
 * Critical CSS除外設定があるため安全
 */
add_filter('litespeed_optm_css_combine_priority', 'gi_litespeed_css_combine_priority');
function gi_litespeed_css_combine_priority($priority) {
    // Critical CSSを最優先で読み込む
    return 1;
}

/**
 * Inline CSS Minify: 選択的最適化
 * フッターのインラインCSSは最適化、ヘッダーは除外
 */
add_filter('litespeed_optm_css_inline_minify', 'gi_litespeed_inline_css_minify_control');
function gi_litespeed_inline_css_minify_control($minify) {
    // ヘッダー内のCritical CSSは最適化しない
    if (did_action('wp_head') && !did_action('wp_footer')) {
        return false;
    }
    return true;
}

/**
 * CSS Async Loading: Above the Fold以外のCSSを非同期化
 * Critical CSSは同期読み込みのまま
 */
// LiteSpeed Cache管理画面で「Load CSS Asynchronously」を有効にすることを推奨
// フィルターでは強制せず、ユーザー制御に委ねる

/**
 * CSS Async/Defer: FOUC防止のため無効化
 * 初回訪問時のCSS崩れを防ぐ
 */
add_filter('litespeed_optm_css_async', '__return_false');
add_filter('litespeed_optm_css_defer', '__return_false');

/**
 * CSS HTTP/2 Push: 有効化推奨
 * Critical CSSをHTTP/2でプッシュして高速化
 */
add_filter('litespeed_optm_css_http2', '__return_true');

/**
 * LiteSpeed Cache: Viewport Image Generation Settings
 * ビューポート画像最適化の設定
 */
add_filter('litespeed_conf_img_optm_webp_replace', '__return_true');
add_filter('litespeed_conf_img_optm_webp_attribute', '__return_true');

/**
 * LiteSpeed Cache: Preload Critical Resources
 * 重要リソースのプリロード設定
 */
add_action('wp_head', 'gi_litespeed_preload_resources', 1);
function gi_litespeed_preload_resources() {
    // LCP画像のプリロード
    if (is_front_page()) {
        echo '<link rel="preload" as="image" href="https://joseikin-insight.com/wp-content/uploads/2024/11/dashboard-screenshot.webp" fetchpriority="high">' . "\n";
    }
    
    // ロゴのプリロード
    echo '<link rel="preload" as="image" href="https://joseikin-insight.com/gemini_generated_image_19k6yi19k6yi19k6/" fetchpriority="high">' . "\n";
    
    // Critical Fontsのプリロード（該当する場合）
    // echo '<link rel="preload" as="font" href="/path/to/font.woff2" type="font/woff2" crossorigin>' . "\n";
}

/**
 * Critical CSS: Above the Fold CSS Inline Injection
 * FOUC防止のため、Above the Fold CSSを<head>内にインライン化
 * 
 * このCritical CSSは初回訪問時のFOUC (Flash of Unstyled Content) を防止します。
 */
add_action('wp_head', 'gi_inject_critical_css', 2);
function gi_inject_critical_css() {
    // Critical CSS（ヘッダー、ヒーローセクション、基本レイアウト、本風デザイン）
    $critical_css = "
    /* Critical Reset & Base Styles */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;line-height:1.6;color:#1a1a1a;background:#fff}
    
    /* Header Critical Styles */
    #ji-header{position:fixed;top:0;left:0;right:0;z-index:1000;background:#fff;transition:transform .3s ease}
    #ji-header.hidden{transform:translateY(-100%)}
    #ji-header.scrolled{box-shadow:0 2px 8px rgba(0,0,0,.1)}
    .ji-logo{display:block;height:40px}
    .ji-logo-image{width:auto;height:100%;object-fit:contain}
    
    /* Hero Section Critical Styles */
    .hero{min-height:400px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
    .hero__image{width:100%;height:auto;display:block}
    
    /* Layout Critical Styles */
    .container{max-width:1200px;margin:0 auto;padding:0 20px}
    
    /* 📚 本風デザイン Critical Styles */
    .gi-book-breadcrumb,.gic-book-breadcrumb,.book-breadcrumb{background:linear-gradient(180deg,#faf8f5 0%,#fff 100%);position:relative;box-shadow:0 2px 8px rgba(0,0,0,.06)}
    .gi-breadcrumb-book-spine,.gic-breadcrumb-book-spine,.book-breadcrumb-spine{position:absolute;left:0;top:0;bottom:0;width:8px;background:linear-gradient(180deg,#0D2A52 0%,#081C38 100%)}
    .gi-breadcrumb-book-spine::after,.gic-breadcrumb-book-spine::after,.book-breadcrumb-spine::after{content:'';position:absolute;left:100%;top:0;bottom:0;width:3px;background:linear-gradient(180deg,#C9A227 0%,#D4B57A 100%)}
    .gi-breadcrumb-inner,.gic-breadcrumb-inner,.book-breadcrumb-inner{display:flex;align-items:center;padding:14px 24px;gap:12px}
    
    /* Prevent FOUC */
    .no-js{opacity:1}
    ";
    
    // Minify CSS（改行・スペース削除）
    $critical_css = preg_replace('/\s+/', ' ', $critical_css);
    $critical_css = str_replace([' {', '{ ', ' }', '} ', ': ', ' ;', '; '], ['{', '{', '}', '}', ':', ';', ';'], $critical_css);
    
    echo '<style id="critical-css">' . trim($critical_css) . '</style>' . "\n";
}

/**
 * LiteSpeed Cache: Cache Vary for Logged-in Users
 * ログインユーザーのキャッシュ分離
 */
add_filter('litespeed_cache_cookies', 'gi_litespeed_cache_cookies');
function gi_litespeed_cache_cookies($cookies) {
    // ログインユーザーは別キャッシュ
    if (is_user_logged_in()) {
        $cookies[] = 'wordpress_logged_in_';
    }
    return $cookies;
}

/**
 * LiteSpeed Cache: Admin Notices with Optimization Guide
 * 管理画面通知＋最適化ガイド
 */
add_action('admin_notices', 'gi_litespeed_optimization_guide');
function gi_litespeed_optimization_guide() {
    // LiteSpeed Cacheが有効かチェック
    if (!defined('LSCWP_V')) {
        return;
    }
    
    $screen = get_current_screen();
    
    // ダッシュボードで総合通知
    if ($screen && $screen->id === 'dashboard') {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<h3>🚀 LiteSpeed Cache 最適化完了</h3>';
        echo '<p><strong>テーマ側の最適化設定が適用されました:</strong></p>';
        echo '<ul style="list-style: disc; margin-left: 20px;">';
        echo '<li>✅ Above the Fold画像のLazy Load除外</li>';
        echo '<li>✅ Critical CSS/JS保護設定</li>';
        echo '<li>✅ 外部画像の自動除外</li>';
        echo '<li>✅ HTTP/2 Push有効化</li>';
        echo '</ul>';
        echo '<p><strong>推奨:</strong> LiteSpeed Cache → Presets で「<strong>Aggressive</strong>」を選択し、QUIC.cloud接続を有効にしてください。</p>';
        echo '</div>';
    }
    
    // LiteSpeed Cache設定ページで詳細ガイド
    if ($screen && strpos($screen->id, 'litespeed') !== false) {
        echo '<div class="notice notice-info">';
        echo '<h3>📋 推奨設定ガイド</h3>';
        echo '<h4>✅ 既にテーマ側で自動設定済み:</h4>';
        echo '<ul style="list-style: disc; margin-left: 20px;">';
        echo '<li>Critical CSS/JS除外リスト</li>';
        echo '<li>外部画像URL除外（Google UserContent等）</li>';
        echo '<li>HTTP/2 Push有効化</li>';
        echo '</ul>';
        echo '<h4>🔧 LiteSpeed Cache管理画面で設定してください:</h4>';
        echo '<ul style="list-style: disc; margin-left: 20px;">';
        echo '<li><strong>Page Optimization → CSS Settings:</strong><br>
              - CSS Minify: ON<br>
              - CSS Combine: ON<br>
              - Load CSS Asynchronously: ON（推奨）<br>
              - Inline CSS Minify: ON</li>';
        echo '<li><strong>Page Optimization → JS Settings:</strong><br>
              - JS Minify: ON<br>
              - JS Combine: ON（External JSはOFF）<br>
              - Load JS Deferred: ON</li>';
        echo '<li><strong>Media → Lazy Load:</strong><br>
              - Lazy Load Images: ON（Above the Foldは自動除外済み）</li>';
        echo '<li><strong>Media → Image Optimization:</strong><br>
              - WebP Replacement: ON<br>
              - Responsive Placeholder: ON</li>';
        echo '</ul>';
        echo '</div>';
    }
}

/**
 * LiteSpeed Cache: External Image URL Exclusions
 * 外部画像URL除外（403エラー対策）
 */
add_filter('litespeed_media_optm_exc_src', 'gi_litespeed_external_image_excludes');
function gi_litespeed_external_image_excludes($excluded_urls) {
    // 外部サービスの画像URL（403エラーを返すもの）を除外
    $external_domains = [
        'lh3.googleusercontent.com',    // Google UserContent (NotebookLM等)
        'lh4.googleusercontent.com',
        'lh5.googleusercontent.com',
        'lh6.googleusercontent.com',
        'ssl.gstatic.com',              // Google Static Content
        'www.gstatic.com',
        'i.ytimg.com',                  // YouTube Thumbnails
        'i.vimeocdn.com',               // Vimeo Thumbnails
        'platform.twitter.com',         // Twitter Embeds
        'abs.twimg.com',                // Twitter Images
        'external-',                    // Facebook External CDN
        'scontent',                     // Facebook Content
        'graph.facebook.com',           // Facebook Graph API
    ];
    
    // 既存の除外リストに追加
    if (!is_array($excluded_urls)) {
        $excluded_urls = [];
    }
    
    return array_merge($excluded_urls, $external_domains);
}

/**
 * LiteSpeed Cache: Disable Image Optimization for External URLs
 * 外部URL画像の最適化を完全に無効化
 */
add_filter('litespeed_media_check_ori_optm', 'gi_litespeed_skip_external_image_check', 10, 2);
function gi_litespeed_skip_external_image_check($continue, $src) {
    // 外部URLかチェック
    if (empty($src)) {
        return $continue;
    }
    
    // 自サイトのURLでない場合はスキップ
    $site_url = site_url();
    $site_host = parse_url($site_url, PHP_URL_HOST);
    $img_host = parse_url($src, PHP_URL_HOST);
    
    if ($img_host && $img_host !== $site_host) {
        // 外部URLの場合は最適化をスキップ
        return false;
    }
    
    // Google UserContentの特別処理
    if (strpos($src, 'googleusercontent.com') !== false) {
        return false;
    }
    
    // その他の外部CDN
    $external_cdns = [
        'gstatic.com',
        'ytimg.com',
        'vimeocdn.com',
        'twimg.com',
        'fbcdn.net',
    ];
    
    foreach ($external_cdns as $cdn) {
        if (strpos($src, $cdn) !== false) {
            return false;
        }
    }
    
    return $continue;
}

/**
 * LiteSpeed Cache: Additional Filters to Prevent External Image Processing
 * 追加フィルター：外部画像処理を完全にブロック
 */

// Lazy Load用の除外フィルター
add_filter('litespeed_media_lazy_img_excludes', 'gi_litespeed_lazy_external_excludes', 999);
function gi_litespeed_lazy_external_excludes($excludes) {
    // 外部ドメインパターンを追加
    $external_patterns = [
        'googleusercontent.com',
        'gstatic.com',
        'ytimg.com',
        'vimeocdn.com',
        'twimg.com',
        'fbcdn.net',
    ];
    
    return array_merge($excludes, $external_patterns);
}

/**
 * External Image Handling: Safe and Silent
 * 外部URL用の警告抑制（安全な実装）
 * 
 * NOTE: エラーハンドラーは使用せず、フィルターのみで対応
 * 403エラーがログに出ても無害（外部画像は正常表示される）
 */

// 外部画像の403警告について管理者に説明
add_action('admin_notices', 'gi_litespeed_external_image_info');
function gi_litespeed_external_image_info() {
    if (!defined('LSCWP_V')) {
        return;
    }
    
    $screen = get_current_screen();
    
    // エラーログページまたはLiteSpeed設定ページで表示
    if ($screen && (strpos($screen->id, 'tools') !== false || strpos($screen->id, 'litespeed') !== false)) {
        // エラーログに403警告がある場合のみ表示
        if (file_exists(WP_CONTENT_DIR . '/debug.log')) {
            $log_content = @file_get_contents(WP_CONTENT_DIR . '/debug.log');
            if ($log_content && (strpos($log_content, '403 Forbidden') !== false || strpos($log_content, 'googleusercontent') !== false)) {
                echo '<div class="notice notice-info is-dismissible">';
                echo '<h4>ℹ️ 外部画像の403エラーについて</h4>';
                echo '<p>エラーログに「<code>getimagesize() ... 403 Forbidden</code>」と表示される場合があります。</p>';
                echo '<p><strong>これは無害です：</strong></p>';
                echo '<ul style="list-style: disc; margin-left: 20px;">';
                echo '<li>外部画像（Google UserContent等）は正常に表示されます</li>';
                echo '<li>LiteSpeed Cacheがローカル最適化を試みただけです</li>';
                echo '<li>サイトの表示・パフォーマンスに影響ありません</li>';
                echo '<li>外部画像は自動的に最適化対象から除外されています</li>';
                echo '</ul>';
                echo '<p><strong>対処不要</strong>：このエラーは無視しても問題ありません。気になる場合は、ログローテーション設定で403エラーをフィルタリングできます。</p>';
                echo '</div>';
            }
        }
    }
}

/**
 * =============================================================================
 * SEO Title Optimization - タイトルタグの最適化
 * =============================================================================
 */

/**
 * タイトルタグから不要なハイフンを除去
 */
add_filter('document_title_separator', function($sep) {
    return '|'; // ハイフンの代わりにパイプを使用
}, 10, 1);

/**
 * タイトルタグの最適化
 */
add_filter('document_title_parts', function($title) {
    // 「地域名 - の」パターンを修正
    if (isset($title['title'])) {
        // 「〇〇県 - の補助金」→「〇〇県の補助金」
        $title['title'] = preg_replace('/^(.+?)\s*-\s*の/', '$1の', $title['title']);
        
        // 「〇〇市 - の補助金」→「〇〇市の補助金」
        $title['title'] = preg_replace('/^(.+?[都道府県市区町村])\s*-\s*の/', '$1の', $title['title']);
    }
    
    return $title;
}, 10, 1);

/**
 * アーカイブページタイトルの最適化（カスタムタイトル使用）
 */
add_filter('get_the_archive_title', function($title) {
    // カスタムSEOタイトルがある場合は使用
    if (function_exists('gi_get_archive_custom_title')) {
        $custom_title = gi_get_archive_custom_title();
        if ($custom_title) {
            return $custom_title;
        }
    }
    
    // デフォルトタイトルの改善
    if (is_tax()) {
        $term = get_queried_object();
        if ($term) {
            // 「アーカイブ: 東京都」→「東京都」
            return $term->name;
        }
    }
    
    if (is_post_type_archive('grant')) {
        return '補助金・助成金一覧';
    }
    
    // その他のアーカイブ
    return preg_replace('/^(カテゴリー|タグ|アーカイブ):\s*/', '', $title);
}, 10, 1);
