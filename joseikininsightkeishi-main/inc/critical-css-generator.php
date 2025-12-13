<?php
/**
 * Critical CSS Generator
 * 
 * Phase 3 SEO Enhancement: Automatic Critical CSS Generation
 * Generates and caches critical CSS for above-the-fold content
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 * @since 11.0.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class GI_Critical_CSS_Generator {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Cache directory
     */
    private $cache_dir;
    
    /**
     * Cache expiration in seconds (1 week)
     */
    private $cache_expiration = 604800;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->cache_dir = WP_CONTENT_DIR . '/cache/critical-css/';
        
        // Ensure cache directory exists
        if (!file_exists($this->cache_dir)) {
            wp_mkdir_p($this->cache_dir);
        }
        
        // Add critical CSS to head
        add_action('wp_head', array($this, 'output_critical_css'), 1);
        
        // Clear cache on theme update
        add_action('after_switch_theme', array($this, 'clear_cache'));
        add_action('customize_save_after', array($this, 'clear_cache'));
        
        // Admin notice for cache regeneration
        add_action('admin_notices', array($this, 'admin_notice'));
        
        // AJAX handler for manual cache clear
        add_action('wp_ajax_gi_clear_critical_css', array($this, 'ajax_clear_cache'));
    }
    
    /**
     * Get cache key based on page type
     */
    private function get_cache_key() {
        if (is_front_page()) {
            return 'front-page';
        } elseif (is_singular('grant')) {
            return 'single-grant';
        } elseif (is_singular('column') || is_singular('post')) {
            return 'single-column';
        } elseif (is_post_type_archive('grant')) {
            return 'archive-grant';
        } elseif (is_tax()) {
            return 'taxonomy';
        } else {
            return 'default';
        }
    }
    
    /**
     * Get critical CSS for page type
     */
    private function get_critical_css($page_type) {
        // Check cache
        $cache_file = $this->cache_dir . $page_type . '.css';
        
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $this->cache_expiration) {
            return file_get_contents($cache_file);
        }
        
        // Generate critical CSS
        $css = $this->generate_critical_css($page_type);
        
        // Save to cache
        file_put_contents($cache_file, $css);
        
        return $css;
    }
    
    /**
     * Generate critical CSS for page type
     */
    private function generate_critical_css($page_type) {
        $css = '';
        
        // Common critical CSS for all pages
        $css .= $this->get_common_critical_css();
        
        // Page-specific critical CSS
        switch ($page_type) {
            case 'front-page':
                $css .= $this->get_front_page_critical_css();
                break;
            case 'single-grant':
                $css .= $this->get_single_grant_critical_css();
                break;
            case 'single-column':
                $css .= $this->get_single_column_critical_css();
                break;
            case 'archive-grant':
                $css .= $this->get_archive_critical_css();
                break;
            default:
                $css .= $this->get_default_critical_css();
                break;
        }
        
        // Minify CSS
        return $this->minify_css($css);
    }
    
    /**
     * Common critical CSS for all pages
     */
    private function get_common_critical_css() {
        return '
        /* Critical CSS - Common Base */
        :root {
            --h-gov-navy-900: #0d1b2a;
            --h-gov-navy-800: #1b263b;
            --h-gov-navy-700: #2c3e50;
            --h-gov-navy-50: #f4f6f8;
            --h-gov-gold: #c9a227;
            --h-white: #ffffff;
            --h-gray-900: #212529;
            --h-gray-100: #f8f9fa;
            --h-font-sans: "Noto Sans JP", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --h-font-serif: "Shippori Mincho", "Yu Mincho", serif;
            --h-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
        
        /* Header */
        .ji-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: linear-gradient(135deg, var(--h-gov-navy-800), var(--h-gov-navy-900));
            height: 64px;
            border-bottom: 3px solid var(--h-gov-gold);
        }
        
        .ji-header-placeholder {
            height: 64px;
            display: block;
        }
        
        /* Container */
        .gi-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        @media (max-width: 768px) {
            .gi-container {
                padding: 0 16px;
            }
        }
        ';
    }
    
    /**
     * Front page critical CSS
     */
    private function get_front_page_critical_css() {
        return '
        /* Critical CSS - Front Page */
        .hero {
            min-height: 60vh;
            display: flex;
            align-items: center;
            padding: 100px 0 60px;
            background: linear-gradient(135deg, var(--h-gov-navy-900), var(--h-gov-navy-800));
        }
        
        .hero__title {
            font-family: var(--h-font-serif);
            font-size: clamp(28px, 6vw, 48px);
            color: var(--h-white);
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .search {
            background: var(--h-white);
            padding: 40px 0;
        }
        
        .search__input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--h-gov-navy-200);
            border-radius: 8px;
            font-size: 16px;
        }
        
        .grant-tabs {
            background: var(--h-white);
            padding: 40px 0;
        }
        ';
    }
    
    /**
     * Single grant critical CSS
     */
    private function get_single_grant_critical_css() {
        return '
        /* Critical CSS - Single Grant */
        .gi-breadcrumb {
            padding: 16px 0;
            padding-top: 88px;
            background: var(--h-white);
            border-bottom: 1px solid #dee2e6;
        }
        
        .gi-hero {
            padding: 32px 0 24px;
            border-bottom: 2px solid var(--h-gov-navy-800);
        }
        
        .gi-hero-title {
            font-family: var(--h-font-serif);
            font-size: clamp(24px, 5vw, 32px);
            font-weight: 700;
            color: var(--h-gov-navy-900);
        }
        
        .gi-metrics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: #cfd8e3;
            border-radius: 8px;
            margin: 24px 0;
            overflow: hidden;
        }
        
        .gi-metric {
            padding: 20px 16px;
            text-align: center;
            background: var(--h-white);
            min-height: 100px;
        }
        
        @media (max-width: 768px) {
            .gi-metrics {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .gi-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 40px;
            padding: 32px 0;
        }
        
        @media (max-width: 1024px) {
            .gi-layout {
                grid-template-columns: 1fr;
            }
        }
        
        .gi-section {
            background: var(--h-white);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 32px;
            margin-bottom: 32px;
        }
        ';
    }
    
    /**
     * Single column critical CSS
     */
    private function get_single_column_critical_css() {
        return '
        /* Critical CSS - Single Column */
        .column-header {
            padding-top: 80px;
            background: var(--h-white);
        }
        
        .column-title {
            font-family: var(--h-font-serif);
            font-size: clamp(24px, 5vw, 36px);
            color: var(--h-gov-navy-900);
            font-weight: 700;
        }
        
        .column-content {
            background: var(--h-white);
            padding: 40px;
            border-radius: 8px;
        }
        
        .column-sidebar {
            position: sticky;
            top: 80px;
        }
        ';
    }
    
    /**
     * Archive critical CSS
     */
    private function get_archive_critical_css() {
        return '
        /* Critical CSS - Archive */
        .archive-header {
            padding-top: 80px;
            padding-bottom: 40px;
            background: linear-gradient(135deg, var(--h-gov-navy-900), var(--h-gov-navy-800));
        }
        
        .archive-title {
            font-family: var(--h-font-serif);
            font-size: clamp(28px, 5vw, 40px);
            color: var(--h-white);
            font-weight: 700;
        }
        
        .grant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            padding: 40px 0;
        }
        
        .grant-card {
            background: var(--h-white);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 24px;
            min-height: 200px;
        }
        ';
    }
    
    /**
     * Default critical CSS
     */
    private function get_default_critical_css() {
        return '
        /* Critical CSS - Default */
        .page-header {
            padding-top: 80px;
            padding-bottom: 40px;
            background: var(--h-white);
        }
        
        .page-title {
            font-family: var(--h-font-serif);
            font-size: clamp(24px, 5vw, 36px);
            color: var(--h-gov-navy-900);
        }
        
        .page-content {
            background: var(--h-white);
            padding: 40px;
        }
        ';
    }
    
    /**
     * Minify CSS
     */
    private function minify_css($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        // Remove spaces around specific characters
        $css = preg_replace('/\s*([\{\}\:\;\,\>])\s*/', '$1', $css);
        // Remove trailing semicolons
        $css = str_replace(';}', '}', $css);
        
        return trim($css);
    }
    
    /**
     * Output critical CSS
     */
    public function output_critical_css() {
        // Don't output for logged-in users viewing customizer
        if (is_customize_preview()) {
            return;
        }
        
        $page_type = $this->get_cache_key();
        $css = $this->get_critical_css($page_type);
        
        echo '<style id="critical-css-' . esc_attr($page_type) . '">' . $css . '</style>' . "\n";
    }
    
    /**
     * Clear cache
     */
    public function clear_cache() {
        if (!file_exists($this->cache_dir)) {
            return;
        }
        
        $files = glob($this->cache_dir . '*.css');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
    
    /**
     * Admin notice
     */
    public function admin_notice() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $screen = get_current_screen();
        if ($screen->id !== 'toplevel_page_gi-theme-settings' && $screen->id !== 'appearance_page_gi-theme-settings') {
            return;
        }
        
        echo '<div class="notice notice-info">';
        echo '<p><strong>Critical CSS Cache:</strong> ';
        echo '<a href="' . wp_nonce_url(admin_url('admin-ajax.php?action=gi_clear_critical_css'), 'gi_clear_critical_css') . '" class="button button-secondary">Clear Cache</a>';
        echo '</p></div>';
    }
    
    /**
     * AJAX clear cache handler
     */
    public function ajax_clear_cache() {
        if (!wp_verify_nonce($_GET['_wpnonce'], 'gi_clear_critical_css')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $this->clear_cache();
        
        wp_redirect(admin_url('themes.php?critical_css_cleared=1'));
        exit;
    }
}

// Initialize
GI_Critical_CSS_Generator::get_instance();
