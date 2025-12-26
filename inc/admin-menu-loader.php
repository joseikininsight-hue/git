<?php
/**
 * Admin Menu Loader - Lightweight Menu Registration Only
 * 
 * メモリ最適化のため、管理画面メニューの登録のみを行う軽量ファイル
 * 実際の重い処理（ページレンダリング、AJAX処理）は該当ページでのみ読み込む
 * 
 * @package Grant_Insight_Perfect
 * @version 11.0.6
 * @since 11.0.6
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 管理画面メニューを登録（軽量）
 * 
 * 重いファイルを読み込まずにメニューだけを表示
 * ページアクセス時に初めて重いファイルを読み込む
 */
add_action('admin_menu', 'gi_register_admin_menus_lightweight', 5);

function gi_register_admin_menus_lightweight() {
    // Google Sheets Integration メニュー
    if (!class_exists('GI_Google_Sheets_Integration')) {
        add_menu_page(
            'Google Sheets連携',
            'Google Sheets',
            'manage_options',
            'gi-google-sheets',
            'gi_lazy_load_google_sheets_page',
            'dashicons-cloud',
            30
        );
    }
    
    // SEO Content Manager メニュー
    if (!class_exists('GI_SEO_Content_Manager')) {
        add_menu_page(
            'SEO Manager',
            'SEO Manager',
            'manage_options',
            'gi-seo-manager',
            'gi_lazy_load_seo_manager_page',
            'dashicons-chart-line',
            30
        );
        
        add_submenu_page('gi-seo-manager', '記事統合（M&A）', '記事統合', 'manage_options', 'gi-seo-merge', 'gi_lazy_load_seo_merge_page');
        add_submenu_page('gi-seo-manager', '404管理', '404管理', 'manage_options', 'gi-seo-404', 'gi_lazy_load_seo_404_page');
        add_submenu_page('gi-seo-manager', 'カルテ一覧', 'カルテ一覧', 'manage_options', 'gi-seo-reports', 'gi_lazy_load_seo_reports_page');
        add_submenu_page('gi-seo-manager', 'PV推移', 'PV推移', 'manage_options', 'gi-seo-pv-stats', 'gi_lazy_load_seo_pv_page');
        add_submenu_page('gi-seo-manager', '失敗リスト', '失敗リスト', 'manage_options', 'gi-seo-failed', 'gi_lazy_load_seo_failed_page');
        add_submenu_page('gi-seo-manager', '補助金DB', '補助金DB', 'manage_options', 'gi-subsidy-db', 'gi_lazy_load_subsidy_db_page');
        add_submenu_page('gi-seo-manager', '設定', '設定', 'manage_options', 'gi-seo-settings', 'gi_lazy_load_seo_settings_page');
    }
    
    // Archive SEO Content メニュー
    if (!function_exists('gi_archive_seo_page')) {
        add_menu_page(
            'アーカイブSEO',
            'アーカイブSEO',
            'manage_options',
            'gi-archive-seo',
            'gi_lazy_load_archive_seo_page',
            'dashicons-archive',
            31
        );
        
        add_submenu_page('gi-archive-seo', '一覧', '一覧', 'manage_options', 'gi-archive-seo', 'gi_lazy_load_archive_seo_page');
        add_submenu_page('gi-archive-seo', '編集', '編集', 'manage_options', 'gi-archive-seo-edit', 'gi_lazy_load_archive_seo_edit_page');
        add_submenu_page('gi-archive-seo', 'ターム統合', 'ターム統合', 'manage_options', 'gi-archive-seo-merge', 'gi_lazy_load_archive_seo_merge_page');
    }
    
    // Grant Article Creator メニュー
    if (!class_exists('GI_Grant_Article_Creator')) {
        add_menu_page(
            '補助金記事作成',
            '記事作成',
            'manage_options',
            'gi-grant-article',
            'gi_lazy_load_grant_article_page',
            'dashicons-edit-large',
            32
        );
    }
    
    // AI Concierge メニュー
    if (!function_exists('gip_page_dashboard')) {
        add_menu_page(
            'AI補助金診断',
            'AI補助金診断',
            'manage_options',
            'gip-admin',
            'gi_lazy_load_ai_concierge_page',
            'dashicons-format-chat',
            30
        );
        
        add_submenu_page('gip-admin', 'ダッシュボード', 'ダッシュボード', 'manage_options', 'gip-admin', 'gi_lazy_load_ai_concierge_page');
        add_submenu_page('gip-admin', '質問ログ分析', '質問ログ分析', 'manage_options', 'gip-question-logs', 'gi_lazy_load_ai_question_logs_page');
        add_submenu_page('gip-admin', 'インデックス', 'インデックス', 'manage_options', 'gip-index', 'gi_lazy_load_ai_index_page');
        add_submenu_page('gip-admin', '設定', '設定', 'manage_options', 'gip-settings', 'gi_lazy_load_ai_settings_page');
    }
}

/**
 * ==========================================================================
 * Lazy Load Callbacks - ページアクセス時に重いファイルを読み込む
 * ==========================================================================
 */

// Google Sheets
function gi_lazy_load_google_sheets_page() {
    gi_load_heavy_file('google-sheets-integration.php');
    if (class_exists('GI_Google_Sheets_Integration')) {
        $instance = GI_Google_Sheets_Integration::getInstance();
        $instance->renderAdminPage();
    } else {
        gi_show_loading_error('Google Sheets Integration');
    }
}

// SEO Manager
function gi_lazy_load_seo_manager_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

function gi_lazy_load_seo_merge_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_merge_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

function gi_lazy_load_seo_404_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_404_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

function gi_lazy_load_seo_reports_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_reports_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

function gi_lazy_load_seo_pv_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_pv_stats_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

function gi_lazy_load_seo_failed_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_failed_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

function gi_lazy_load_subsidy_db_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_subsidy_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

function gi_lazy_load_seo_settings_page() {
    gi_load_heavy_file('seo-content-manager.php');
    if (class_exists('GI_SEO_Content_Manager')) {
        GI_SEO_Content_Manager::getInstance()->render_settings_page();
    } else {
        gi_show_loading_error('SEO Content Manager');
    }
}

// Archive SEO
function gi_lazy_load_archive_seo_page() {
    gi_load_heavy_file('archive-seo-content.php');
    if (function_exists('gi_archive_seo_page')) {
        gi_archive_seo_page();
    } else {
        gi_show_loading_error('Archive SEO Content');
    }
}

function gi_lazy_load_archive_seo_edit_page() {
    gi_load_heavy_file('archive-seo-content.php');
    if (function_exists('gi_archive_seo_edit_page')) {
        gi_archive_seo_edit_page();
    } else {
        gi_show_loading_error('Archive SEO Content');
    }
}

function gi_lazy_load_archive_seo_merge_page() {
    gi_load_heavy_file('archive-seo-content.php');
    if (function_exists('gi_archive_seo_merge_page')) {
        gi_archive_seo_merge_page();
    } else {
        gi_show_loading_error('Archive SEO Content');
    }
}

// Grant Article Creator
function gi_lazy_load_grant_article_page() {
    gi_load_heavy_file('grant-article-creator.php');
    if (class_exists('GI_Grant_Article_Creator')) {
        GI_Grant_Article_Creator::getInstance()->render_page();
    } else {
        gi_show_loading_error('Grant Article Creator');
    }
}

// AI Concierge
function gi_lazy_load_ai_concierge_page() {
    gi_load_heavy_file('ai-concierge.php');
    if (function_exists('gip_page_dashboard')) {
        gip_page_dashboard();
    } else {
        gi_show_loading_error('AI Concierge');
    }
}

function gi_lazy_load_ai_question_logs_page() {
    gi_load_heavy_file('ai-concierge.php');
    if (function_exists('gip_page_question_logs')) {
        gip_page_question_logs();
    } else {
        gi_show_loading_error('AI Concierge');
    }
}

function gi_lazy_load_ai_index_page() {
    gi_load_heavy_file('ai-concierge.php');
    if (function_exists('gip_page_index')) {
        gip_page_index();
    } else {
        gi_show_loading_error('AI Concierge');
    }
}

function gi_lazy_load_ai_settings_page() {
    gi_load_heavy_file('ai-concierge.php');
    if (function_exists('gip_page_settings')) {
        gip_page_settings();
    } else {
        gi_show_loading_error('AI Concierge');
    }
}

/**
 * 重いファイルを読み込むヘルパー関数
 */
function gi_load_heavy_file($filename) {
    $file_path = get_template_directory() . '/inc/' . $filename;
    if (file_exists($file_path) && !gi_is_file_loaded($filename)) {
        require_once $file_path;
        gi_mark_file_loaded($filename);
    }
}

/**
 * ファイルが既に読み込まれているかチェック
 */
function gi_is_file_loaded($filename) {
    global $gi_loaded_heavy_files;
    if (!isset($gi_loaded_heavy_files)) {
        $gi_loaded_heavy_files = array();
    }
    return in_array($filename, $gi_loaded_heavy_files);
}

/**
 * ファイルを読み込み済みとしてマーク
 */
function gi_mark_file_loaded($filename) {
    global $gi_loaded_heavy_files;
    if (!isset($gi_loaded_heavy_files)) {
        $gi_loaded_heavy_files = array();
    }
    $gi_loaded_heavy_files[] = $filename;
}

/**
 * エラー表示
 */
function gi_show_loading_error($module_name) {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html($module_name) . '</h1>';
    echo '<div class="notice notice-error"><p>';
    echo 'モジュールの読み込みに失敗しました。テーマファイルを確認してください。';
    echo '</p></div>';
    echo '</div>';
}
