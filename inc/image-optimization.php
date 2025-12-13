<?php
/**
 * Advanced Image Optimization
 * 
 * Phase 3 SEO Enhancement: Next-Gen Image Format Support
 * WebP/AVIF automatic conversion and delivery
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 * @since 11.0.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class GI_Image_Optimizer {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Supported source formats
     */
    private $source_formats = array('jpg', 'jpeg', 'png', 'gif');
    
    /**
     * WebP quality (0-100)
     */
    private $webp_quality = 85;
    
    /**
     * AVIF quality (0-100)
     */
    private $avif_quality = 80;
    
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
        // Auto-convert on upload
        add_filter('wp_generate_attachment_metadata', array($this, 'generate_next_gen_formats'), 10, 2);
        
        // Serve optimized images
        add_filter('wp_get_attachment_image', array($this, 'output_picture_element'), 10, 5);
        add_filter('the_content', array($this, 'convert_content_images'), 20);
        
        // Add srcset support for WebP
        add_filter('wp_calculate_image_srcset', array($this, 'add_webp_srcset'), 10, 5);
        
        // Image size hints for browsers
        add_filter('wp_img_tag_add_width_and_height_attr', '__return_true');
        add_filter('wp_img_tag_add_loading_attr', '__return_true');
        add_filter('wp_img_tag_add_decoding_attr', '__return_true');
        
        // Clean up on attachment deletion
        add_action('delete_attachment', array($this, 'delete_next_gen_files'));
        
        // Add admin column for image optimization status
        add_filter('manage_media_columns', array($this, 'add_optimization_column'));
        add_action('manage_media_custom_column', array($this, 'display_optimization_column'), 10, 2);
        
        // AJAX handler for manual optimization
        add_action('wp_ajax_gi_optimize_image', array($this, 'ajax_optimize_image'));
        
        // Add bulk optimization action
        add_filter('bulk_actions-upload', array($this, 'add_bulk_optimization_action'));
        add_filter('handle_bulk_actions-upload', array($this, 'handle_bulk_optimization'), 10, 3);
    }
    
    /**
     * Check if AVIF is supported
     */
    private function avif_supported() {
        return function_exists('imageavif');
    }
    
    /**
     * Check if WebP is supported
     */
    private function webp_supported() {
        return function_exists('imagewebp');
    }
    
    /**
     * Check browser support for next-gen formats
     */
    private function get_browser_support() {
        $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        
        return array(
            'avif' => strpos($accept, 'image/avif') !== false,
            'webp' => strpos($accept, 'image/webp') !== false,
        );
    }
    
    /**
     * Generate next-gen image formats on upload
     */
    public function generate_next_gen_formats($metadata, $attachment_id) {
        if (!isset($metadata['file'])) {
            return $metadata;
        }
        
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/' . $metadata['file'];
        
        if (!file_exists($file_path)) {
            return $metadata;
        }
        
        $file_info = pathinfo($file_path);
        $extension = strtolower($file_info['extension']);
        
        // Only process supported formats
        if (!in_array($extension, $this->source_formats)) {
            return $metadata;
        }
        
        // Generate WebP for main file
        if ($this->webp_supported()) {
            $webp_path = $this->convert_to_webp($file_path);
            if ($webp_path) {
                update_post_meta($attachment_id, '_gi_webp_path', $webp_path);
            }
        }
        
        // Generate AVIF for main file (if supported)
        if ($this->avif_supported()) {
            $avif_path = $this->convert_to_avif($file_path);
            if ($avif_path) {
                update_post_meta($attachment_id, '_gi_avif_path', $avif_path);
            }
        }
        
        // Generate for all sizes
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $webp_sizes = array();
            $avif_sizes = array();
            
            foreach ($metadata['sizes'] as $size => $size_data) {
                $size_file = dirname($file_path) . '/' . $size_data['file'];
                
                if (!file_exists($size_file)) {
                    continue;
                }
                
                // WebP
                if ($this->webp_supported()) {
                    $size_webp = $this->convert_to_webp($size_file);
                    if ($size_webp) {
                        $webp_sizes[$size] = basename($size_webp);
                    }
                }
                
                // AVIF
                if ($this->avif_supported()) {
                    $size_avif = $this->convert_to_avif($size_file);
                    if ($size_avif) {
                        $avif_sizes[$size] = basename($size_avif);
                    }
                }
            }
            
            if (!empty($webp_sizes)) {
                update_post_meta($attachment_id, '_gi_webp_sizes', $webp_sizes);
            }
            
            if (!empty($avif_sizes)) {
                update_post_meta($attachment_id, '_gi_avif_sizes', $avif_sizes);
            }
        }
        
        // Mark as optimized
        update_post_meta($attachment_id, '_gi_optimized', current_time('mysql'));
        
        return $metadata;
    }
    
    /**
     * Convert image to WebP
     */
    private function convert_to_webp($file_path) {
        if (!$this->webp_supported()) {
            return false;
        }
        
        $file_info = pathinfo($file_path);
        $webp_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';
        
        // Skip if already exists and is newer
        if (file_exists($webp_path) && filemtime($webp_path) >= filemtime($file_path)) {
            return $webp_path;
        }
        
        $image = $this->load_image($file_path);
        
        if (!$image) {
            return false;
        }
        
        // Preserve transparency for PNG
        if (strtolower($file_info['extension']) === 'png') {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }
        
        $result = imagewebp($image, $webp_path, $this->webp_quality);
        imagedestroy($image);
        
        if ($result && file_exists($webp_path)) {
            // Check if WebP is actually smaller
            $original_size = filesize($file_path);
            $webp_size = filesize($webp_path);
            
            if ($webp_size >= $original_size) {
                // WebP is not smaller, delete it
                unlink($webp_path);
                return false;
            }
            
            return $webp_path;
        }
        
        return false;
    }
    
    /**
     * Convert image to AVIF
     */
    private function convert_to_avif($file_path) {
        if (!$this->avif_supported()) {
            return false;
        }
        
        $file_info = pathinfo($file_path);
        $avif_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.avif';
        
        // Skip if already exists and is newer
        if (file_exists($avif_path) && filemtime($avif_path) >= filemtime($file_path)) {
            return $avif_path;
        }
        
        $image = $this->load_image($file_path);
        
        if (!$image) {
            return false;
        }
        
        // AVIF supports transparency
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        
        $result = imageavif($image, $avif_path, $this->avif_quality);
        imagedestroy($image);
        
        if ($result && file_exists($avif_path)) {
            // Check if AVIF is actually smaller
            $original_size = filesize($file_path);
            $avif_size = filesize($avif_path);
            
            if ($avif_size >= $original_size * 0.9) {
                // AVIF is not significantly smaller, delete it
                unlink($avif_path);
                return false;
            }
            
            return $avif_path;
        }
        
        return false;
    }
    
    /**
     * Load image from file
     */
    private function load_image($file_path) {
        $file_info = pathinfo($file_path);
        $extension = strtolower($file_info['extension']);
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return @imagecreatefromjpeg($file_path);
            case 'png':
                return @imagecreatefrompng($file_path);
            case 'gif':
                return @imagecreatefromgif($file_path);
            default:
                return false;
        }
    }
    
    /**
     * Output picture element with multiple sources
     */
    public function output_picture_element($html, $attachment_id, $size, $icon, $attr) {
        // Get next-gen paths
        $webp_path = get_post_meta($attachment_id, '_gi_webp_path', true);
        $avif_path = get_post_meta($attachment_id, '_gi_avif_path', true);
        
        // If no next-gen versions, return original
        if (empty($webp_path) && empty($avif_path)) {
            return $html;
        }
        
        // Get image data
        $src = wp_get_attachment_image_src($attachment_id, $size);
        if (!$src) {
            return $html;
        }
        
        $upload_dir = wp_upload_dir();
        
        // Build picture element
        $picture = '<picture>';
        
        // AVIF source (best quality, smallest size)
        if (!empty($avif_path) && file_exists($avif_path)) {
            $avif_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $avif_path);
            $picture .= sprintf(
                '<source srcset="%s" type="image/avif">',
                esc_url($avif_url)
            );
        }
        
        // WebP source (wide support)
        if (!empty($webp_path) && file_exists($webp_path)) {
            $webp_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $webp_path);
            $picture .= sprintf(
                '<source srcset="%s" type="image/webp">',
                esc_url($webp_url)
            );
        }
        
        // Original image as fallback
        $picture .= $html;
        
        $picture .= '</picture>';
        
        return $picture;
    }
    
    /**
     * Convert images in content to picture elements
     */
    public function convert_content_images($content) {
        if (empty($content)) {
            return $content;
        }
        
        // Find all img tags with wp-image-ID class
        preg_match_all('/<img[^>]+wp-image-(\d+)[^>]+>/i', $content, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            return $content;
        }
        
        foreach ($matches as $match) {
            $img_tag = $match[0];
            $attachment_id = intval($match[1]);
            
            // Skip if already in a picture element
            $pos = strpos($content, $img_tag);
            if ($pos !== false) {
                $before = substr($content, max(0, $pos - 10), 10);
                if (strpos($before, '<picture') !== false) {
                    continue;
                }
            }
            
            // Get next-gen paths
            $webp_path = get_post_meta($attachment_id, '_gi_webp_path', true);
            $avif_path = get_post_meta($attachment_id, '_gi_avif_path', true);
            
            if (empty($webp_path) && empty($avif_path)) {
                continue;
            }
            
            $upload_dir = wp_upload_dir();
            $picture = '<picture>';
            
            // AVIF source
            if (!empty($avif_path) && file_exists($avif_path)) {
                $avif_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $avif_path);
                $picture .= sprintf('<source srcset="%s" type="image/avif">', esc_url($avif_url));
            }
            
            // WebP source
            if (!empty($webp_path) && file_exists($webp_path)) {
                $webp_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $webp_path);
                $picture .= sprintf('<source srcset="%s" type="image/webp">', esc_url($webp_url));
            }
            
            $picture .= $img_tag . '</picture>';
            
            $content = str_replace($img_tag, $picture, $content);
        }
        
        return $content;
    }
    
    /**
     * Add WebP URLs to srcset
     */
    public function add_webp_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
        $webp_sizes = get_post_meta($attachment_id, '_gi_webp_sizes', true);
        
        if (empty($webp_sizes)) {
            return $sources;
        }
        
        // This would require more complex handling to properly integrate WebP into srcset
        // For now, we rely on the picture element approach
        
        return $sources;
    }
    
    /**
     * Delete next-gen files when attachment is deleted
     */
    public function delete_next_gen_files($attachment_id) {
        $webp_path = get_post_meta($attachment_id, '_gi_webp_path', true);
        $avif_path = get_post_meta($attachment_id, '_gi_avif_path', true);
        $webp_sizes = get_post_meta($attachment_id, '_gi_webp_sizes', true);
        $avif_sizes = get_post_meta($attachment_id, '_gi_avif_sizes', true);
        
        // Delete main files
        if ($webp_path && file_exists($webp_path)) {
            unlink($webp_path);
        }
        
        if ($avif_path && file_exists($avif_path)) {
            unlink($avif_path);
        }
        
        // Delete size variations
        $upload_dir = wp_upload_dir();
        $base_dir = dirname($upload_dir['basedir'] . '/' . get_post_meta($attachment_id, '_wp_attached_file', true));
        
        if (!empty($webp_sizes)) {
            foreach ($webp_sizes as $size => $filename) {
                $file_path = $base_dir . '/' . $filename;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        if (!empty($avif_sizes)) {
            foreach ($avif_sizes as $size => $filename) {
                $file_path = $base_dir . '/' . $filename;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }
    
    /**
     * Add optimization status column to media library
     */
    public function add_optimization_column($columns) {
        $columns['gi_optimization'] = 'Image Optimization';
        return $columns;
    }
    
    /**
     * Display optimization status in column
     */
    public function display_optimization_column($column_name, $attachment_id) {
        if ($column_name !== 'gi_optimization') {
            return;
        }
        
        $mime_type = get_post_mime_type($attachment_id);
        
        // Only for images
        if (strpos($mime_type, 'image/') !== 0) {
            echo '—';
            return;
        }
        
        $optimized = get_post_meta($attachment_id, '_gi_optimized', true);
        $webp_path = get_post_meta($attachment_id, '_gi_webp_path', true);
        $avif_path = get_post_meta($attachment_id, '_gi_avif_path', true);
        
        if ($optimized) {
            $formats = array();
            if ($webp_path && file_exists($webp_path)) {
                $savings = $this->calculate_savings($attachment_id, 'webp');
                $formats[] = 'WebP' . ($savings ? " (-{$savings}%)" : '');
            }
            if ($avif_path && file_exists($avif_path)) {
                $savings = $this->calculate_savings($attachment_id, 'avif');
                $formats[] = 'AVIF' . ($savings ? " (-{$savings}%)" : '');
            }
            
            if (!empty($formats)) {
                echo '<span style="color: green;">✓ ' . implode(', ', $formats) . '</span>';
            } else {
                echo '<span style="color: orange;">⚠ No savings</span>';
            }
        } else {
            echo '<a href="' . wp_nonce_url(admin_url('admin-ajax.php?action=gi_optimize_image&id=' . $attachment_id), 'gi_optimize_image') . '">Optimize</a>';
        }
    }
    
    /**
     * Calculate size savings
     */
    private function calculate_savings($attachment_id, $format) {
        $file = get_attached_file($attachment_id);
        if (!$file || !file_exists($file)) {
            return 0;
        }
        
        $original_size = filesize($file);
        
        if ($format === 'webp') {
            $optimized_path = get_post_meta($attachment_id, '_gi_webp_path', true);
        } else {
            $optimized_path = get_post_meta($attachment_id, '_gi_avif_path', true);
        }
        
        if (!$optimized_path || !file_exists($optimized_path)) {
            return 0;
        }
        
        $optimized_size = filesize($optimized_path);
        $savings = round((1 - ($optimized_size / $original_size)) * 100);
        
        return max(0, $savings);
    }
    
    /**
     * AJAX handler for manual optimization
     */
    public function ajax_optimize_image() {
        if (!wp_verify_nonce($_GET['_wpnonce'], 'gi_optimize_image')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('upload_files')) {
            wp_die('Unauthorized');
        }
        
        $attachment_id = intval($_GET['id']);
        
        if (!$attachment_id) {
            wp_die('Invalid attachment ID');
        }
        
        $metadata = wp_get_attachment_metadata($attachment_id);
        $this->generate_next_gen_formats($metadata, $attachment_id);
        
        wp_redirect(admin_url('upload.php?optimized=' . $attachment_id));
        exit;
    }
    
    /**
     * Add bulk optimization action
     */
    public function add_bulk_optimization_action($actions) {
        $actions['gi_bulk_optimize'] = 'Optimize Images (WebP/AVIF)';
        return $actions;
    }
    
    /**
     * Handle bulk optimization
     */
    public function handle_bulk_optimization($redirect_to, $action, $post_ids) {
        if ($action !== 'gi_bulk_optimize') {
            return $redirect_to;
        }
        
        $optimized = 0;
        
        foreach ($post_ids as $post_id) {
            $mime_type = get_post_mime_type($post_id);
            
            if (strpos($mime_type, 'image/') !== 0) {
                continue;
            }
            
            $metadata = wp_get_attachment_metadata($post_id);
            $this->generate_next_gen_formats($metadata, $post_id);
            $optimized++;
        }
        
        return add_query_arg('bulk_optimized', $optimized, $redirect_to);
    }
}

// Initialize
GI_Image_Optimizer::get_instance();
