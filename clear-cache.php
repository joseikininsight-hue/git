<?php
/**
 * WordPress Cache Clearer
 * Run this once to clear all caches
 */

// WordPress環境のロード
require_once('./wp-load.php');

echo "=== WordPress Cache Clearing ===\n\n";

// 1. WordPress Object Cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✓ Object cache flushed\n";
}

// 2. Transients
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
echo "✓ Transients cleared\n";

// 3. Rewrite Rules
flush_rewrite_rules();
echo "✓ Rewrite rules flushed\n";

// 4. OPcache (if available)
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✓ OPcache reset\n";
}

// 5. LiteSpeed Cache (if active)
if (class_exists('LiteSpeed_Cache_API')) {
    LiteSpeed_Cache_API::purge_all();
    echo "✓ LiteSpeed Cache purged\n";
}

// 6. WP Super Cache (if active)
if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
    echo "✓ WP Super Cache cleared\n";
}

echo "\n=== Cache clearing complete! ===\n";
echo "Please refresh your browser (Ctrl+Shift+R or Cmd+Shift+R)\n";
