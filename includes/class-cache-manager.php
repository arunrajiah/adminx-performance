<?php
/**
 * Cache Manager Class
 *
 * @package AdminX_Performance
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AdminX_Performance_Cache_Manager
 */
class AdminX_Performance_Cache_Manager {

    /**
     * Initialize the cache manager
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('save_post', array($this, 'clear_cache_on_post_update'));
        add_action('wp_update_nav_menu', array($this, 'clear_cache_on_menu_update'));
    }

    /**
     * Initialize cache functionality
     */
    public function init() {
        if (!$this->is_cache_enabled()) {
            return;
        }

        add_action('template_redirect', array($this, 'start_output_buffering'));
        add_action('wp_footer', array($this, 'end_output_buffering'));
    }

    /**
     * Check if caching is enabled
     *
     * @return bool
     */
    private function is_cache_enabled() {
        return get_option('adminx_performance_cache_enabled', true);
    }

    /**
     * Start output buffering for page caching
     */
    public function start_output_buffering() {
        if ($this->should_cache_page()) {
            ob_start(array($this, 'cache_page_content'));
        }
    }

    /**
     * End output buffering
     */
    public function end_output_buffering() {
        if (ob_get_level()) {
            ob_end_flush();
        }
    }

    /**
     * Check if current page should be cached
     *
     * @return bool
     */
    private function should_cache_page() {
        // Don't cache admin pages
        if (is_admin()) {
            return false;
        }

        // Don't cache for logged-in users
        if (is_user_logged_in()) {
            return false;
        }

        // Don't cache POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return false;
        }

        // Don't cache pages with query parameters (except pagination)
        if (!empty($_GET) && !isset($_GET['page'])) {
            return false;
        }

        return true;
    }

    /**
     * Cache page content
     *
     * @param string $content
     * @return string
     */
    public function cache_page_content($content) {
        if (!$this->should_cache_page()) {
            return $content;
        }

        $cache_key = $this->get_cache_key();
        $cache_file = $this->get_cache_file_path($cache_key);

        // Create cache directory if it doesn't exist
        $cache_dir = dirname($cache_file);
        if (!file_exists($cache_dir)) {
            wp_mkdir_p($cache_dir);
        }

        // Save content to cache file
        file_put_contents($cache_file, $content);

        return $content;
    }

    /**
     * Get cache key for current request
     *
     * @return string
     */
    private function get_cache_key() {
        $url = $_SERVER['REQUEST_URI'];
        return md5($url);
    }

    /**
     * Get cache file path
     *
     * @param string $cache_key
     * @return string
     */
    private function get_cache_file_path($cache_key) {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/adminx-cache/' . $cache_key . '.html';
    }

    /**
     * Clear cache on post update
     *
     * @param int $post_id
     */
    public function clear_cache_on_post_update($post_id) {
        if (wp_is_post_revision($post_id)) {
            return;
        }

        $this->clear_all_cache();
    }

    /**
     * Clear cache on menu update
     */
    public function clear_cache_on_menu_update() {
        $this->clear_all_cache();
    }

    /**
     * Clear all cache files
     */
    public function clear_all_cache() {
        $upload_dir = wp_upload_dir();
        $cache_dir = $upload_dir['basedir'] . '/adminx-cache/';

        if (file_exists($cache_dir)) {
            $files = glob($cache_dir . '*.html');
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public function get_cache_stats() {
        $upload_dir = wp_upload_dir();
        $cache_dir = $upload_dir['basedir'] . '/adminx-cache/';
        
        $stats = array(
            'total_files' => 0,
            'total_size' => 0,
            'cache_dir' => $cache_dir
        );

        if (file_exists($cache_dir)) {
            $files = glob($cache_dir . '*.html');
            $stats['total_files'] = count($files);
            
            foreach ($files as $file) {
                $stats['total_size'] += filesize($file);
            }
        }

        return $stats;
    }
}