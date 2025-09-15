<?php
/**
 * Image Optimizer Class
 *
 * @package AdminX_Performance
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AdminX_Performance_Image_Optimizer
 */
class AdminX_Performance_Image_Optimizer {

    /**
     * Initialize the image optimizer
     */
    public function __construct() {
        add_filter('wp_handle_upload', array($this, 'optimize_uploaded_image'));
        add_action('add_attachment', array($this, 'generate_webp_version'));
        add_filter('wp_get_attachment_image_src', array($this, 'serve_webp_if_supported'), 10, 4);
    }

    /**
     * Optimize uploaded image
     *
     * @param array $upload
     * @return array
     */
    public function optimize_uploaded_image($upload) {
        if (!$this->is_optimization_enabled()) {
            return $upload;
        }

        $file_path = $upload['file'];
        $file_type = $upload['type'];

        // Only process images
        if (strpos($file_type, 'image/') !== 0) {
            return $upload;
        }

        $this->compress_image($file_path, $file_type);

        return $upload;
    }

    /**
     * Check if image optimization is enabled
     *
     * @return bool
     */
    private function is_optimization_enabled() {
        return get_option('adminx_performance_image_optimization', true);
    }

    /**
     * Compress image file
     *
     * @param string $file_path
     * @param string $file_type
     */
    private function compress_image($file_path, $file_type) {
        $quality = get_option('adminx_performance_image_quality', 85);

        switch ($file_type) {
            case 'image/jpeg':
                $this->compress_jpeg($file_path, $quality);
                break;
            case 'image/png':
                $this->compress_png($file_path);
                break;
            case 'image/gif':
                // GIF compression is more complex, skip for now
                break;
        }
    }

    /**
     * Compress JPEG image
     *
     * @param string $file_path
     * @param int $quality
     */
    private function compress_jpeg($file_path, $quality) {
        if (!function_exists('imagecreatefromjpeg')) {
            return;
        }

        $image = imagecreatefromjpeg($file_path);
        if ($image === false) {
            return;
        }

        // Save compressed image
        imagejpeg($image, $file_path, $quality);
        imagedestroy($image);
    }

    /**
     * Compress PNG image
     *
     * @param string $file_path
     */
    private function compress_png($file_path) {
        if (!function_exists('imagecreatefrompng')) {
            return;
        }

        $image = imagecreatefrompng($file_path);
        if ($image === false) {
            return;
        }

        // Enable compression
        imagealphablending($image, false);
        imagesavealpha($image, true);
        
        // Save compressed image
        imagepng($image, $file_path, 6); // Compression level 6 (0-9)
        imagedestroy($image);
    }

    /**
     * Generate WebP version of uploaded image
     *
     * @param int $attachment_id
     */
    public function generate_webp_version($attachment_id) {
        if (!$this->is_webp_enabled()) {
            return;
        }

        $file_path = get_attached_file($attachment_id);
        $file_type = get_post_mime_type($attachment_id);

        // Only process supported image types
        if (!in_array($file_type, array('image/jpeg', 'image/png'))) {
            return;
        }

        $this->create_webp_version($file_path, $file_type);
    }

    /**
     * Check if WebP generation is enabled
     *
     * @return bool
     */
    private function is_webp_enabled() {
        return get_option('adminx_performance_webp_enabled', true) && function_exists('imagewebp');
    }

    /**
     * Create WebP version of image
     *
     * @param string $file_path
     * @param string $file_type
     */
    private function create_webp_version($file_path, $file_type) {
        $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);
        
        // Don't create if WebP already exists
        if (file_exists($webp_path)) {
            return;
        }

        $image = null;
        
        switch ($file_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file_path);
                break;
        }

        if ($image === null || $image === false) {
            return;
        }

        // Create WebP image
        $quality = get_option('adminx_performance_webp_quality', 80);
        imagewebp($image, $webp_path, $quality);
        imagedestroy($image);
    }

    /**
     * Serve WebP image if browser supports it
     *
     * @param array $image
     * @param int $attachment_id
     * @param string $size
     * @param bool $icon
     * @return array
     */
    public function serve_webp_if_supported($image, $attachment_id, $size, $icon) {
        if (!$this->is_webp_enabled() || !$this->browser_supports_webp()) {
            return $image;
        }

        if (!$image || !isset($image[0])) {
            return $image;
        }

        $original_url = $image[0];
        $webp_url = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $original_url);
        
        // Check if WebP version exists
        $webp_path = $this->url_to_path($webp_url);
        if (file_exists($webp_path)) {
            $image[0] = $webp_url;
        }

        return $image;
    }

    /**
     * Check if browser supports WebP
     *
     * @return bool
     */
    private function browser_supports_webp() {
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        return strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
    }

    /**
     * Convert URL to file path
     *
     * @param string $url
     * @return string
     */
    private function url_to_path($url) {
        $upload_dir = wp_upload_dir();
        return str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);
    }

    /**
     * Bulk optimize existing images
     *
     * @param int $limit
     * @return array
     */
    public function bulk_optimize_images($limit = 10) {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => array('image/jpeg', 'image/png'),
            'post_status' => 'inherit',
            'posts_per_page' => $limit,
            'meta_query' => array(
                array(
                    'key' => '_adminx_optimized',
                    'compare' => 'NOT EXISTS'
                )
            )
        );

        $attachments = get_posts($args);
        $optimized = 0;

        foreach ($attachments as $attachment) {
            $file_path = get_attached_file($attachment->ID);
            $file_type = get_post_mime_type($attachment->ID);

            if (file_exists($file_path)) {
                $original_size = filesize($file_path);
                
                $this->compress_image($file_path, $file_type);
                
                if ($this->is_webp_enabled()) {
                    $this->create_webp_version($file_path, $file_type);
                }

                $new_size = filesize($file_path);
                $savings = $original_size - $new_size;

                // Mark as optimized
                update_post_meta($attachment->ID, '_adminx_optimized', true);
                update_post_meta($attachment->ID, '_adminx_size_savings', $savings);

                $optimized++;
            }
        }

        return array(
            'optimized' => $optimized,
            'remaining' => $this->get_unoptimized_count()
        );
    }

    /**
     * Get count of unoptimized images
     *
     * @return int
     */
    public function get_unoptimized_count() {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => array('image/jpeg', 'image/png'),
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => '_adminx_optimized',
                    'compare' => 'NOT EXISTS'
                )
            )
        );

        $attachments = get_posts($args);
        return count($attachments);
    }

    /**
     * Get optimization statistics
     *
     * @return array
     */
    public function get_optimization_stats() {
        global $wpdb;

        $total_images = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND post_mime_type IN ('image/jpeg', 'image/png')
        ");

        $optimized_images = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'attachment' 
            AND p.post_mime_type IN ('image/jpeg', 'image/png')
            AND pm.meta_key = '_adminx_optimized'
        ");

        $total_savings = $wpdb->get_var("
            SELECT SUM(CAST(pm.meta_value AS SIGNED))
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'attachment' 
            AND pm.meta_key = '_adminx_size_savings'
        ");

        return array(
            'total_images' => (int) $total_images,
            'optimized_images' => (int) $optimized_images,
            'total_savings' => (int) $total_savings,
            'percentage_optimized' => $total_images > 0 ? round(($optimized_images / $total_images) * 100, 2) : 0
        );
    }
}