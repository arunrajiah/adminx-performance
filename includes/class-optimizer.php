<?php
/**
 * CSS/JS Optimizer Class
 *
 * @package AdminX_Performance
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AdminX_Performance_Optimizer
 */
class AdminX_Performance_Optimizer {

    /**
     * Initialize the optimizer
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'optimize_assets'), 999);
        add_action('wp_print_styles', array($this, 'defer_non_critical_css'), 999);
        add_action('wp_print_scripts', array($this, 'defer_javascript'), 999);
    }

    /**
     * Optimize CSS and JS assets
     */
    public function optimize_assets() {
        if (!$this->is_optimization_enabled()) {
            return;
        }

        // Only optimize on frontend
        if (is_admin()) {
            return;
        }

        add_filter('style_loader_src', array($this, 'process_css_file'), 10, 2);
        add_filter('script_loader_src', array($this, 'process_js_file'), 10, 2);
    }

    /**
     * Check if optimization is enabled
     *
     * @return bool
     */
    private function is_optimization_enabled() {
        return get_option('adminx_performance_optimize_enabled', true);
    }

    /**
     * Process CSS file for optimization
     *
     * @param string $src
     * @param string $handle
     * @return string
     */
    public function process_css_file($src, $handle) {
        // Skip external files
        if (strpos($src, home_url()) === false) {
            return $src;
        }

        $optimized_file = $this->get_optimized_css_file($src);
        
        if ($optimized_file && file_exists($optimized_file['path'])) {
            return $optimized_file['url'];
        }

        // Create optimized version
        $this->create_optimized_css($src);
        
        $optimized_file = $this->get_optimized_css_file($src);
        return $optimized_file ? $optimized_file['url'] : $src;
    }

    /**
     * Process JS file for optimization
     *
     * @param string $src
     * @param string $handle
     * @return string
     */
    public function process_js_file($src, $handle) {
        // Skip external files
        if (strpos($src, home_url()) === false) {
            return $src;
        }

        $optimized_file = $this->get_optimized_js_file($src);
        
        if ($optimized_file && file_exists($optimized_file['path'])) {
            return $optimized_file['url'];
        }

        // Create optimized version
        $this->create_optimized_js($src);
        
        $optimized_file = $this->get_optimized_js_file($src);
        return $optimized_file ? $optimized_file['url'] : $src;
    }

    /**
     * Get optimized CSS file path and URL
     *
     * @param string $src
     * @return array|false
     */
    private function get_optimized_css_file($src) {
        $upload_dir = wp_upload_dir();
        $cache_dir = $upload_dir['basedir'] . '/adminx-optimized/css/';
        $cache_url = $upload_dir['baseurl'] . '/adminx-optimized/css/';
        
        $filename = basename($src);
        $hash = md5($src);
        $optimized_filename = $hash . '-' . $filename;
        
        return array(
            'path' => $cache_dir . $optimized_filename,
            'url' => $cache_url . $optimized_filename
        );
    }

    /**
     * Get optimized JS file path and URL
     *
     * @param string $src
     * @return array|false
     */
    private function get_optimized_js_file($src) {
        $upload_dir = wp_upload_dir();
        $cache_dir = $upload_dir['basedir'] . '/adminx-optimized/js/';
        $cache_url = $upload_dir['baseurl'] . '/adminx-optimized/js/';
        
        $filename = basename($src);
        $hash = md5($src);
        $optimized_filename = $hash . '-' . $filename;
        
        return array(
            'path' => $cache_dir . $optimized_filename,
            'url' => $cache_url . $optimized_filename
        );
    }

    /**
     * Create optimized CSS file
     *
     * @param string $src
     */
    private function create_optimized_css($src) {
        $file_path = $this->url_to_path($src);
        
        if (!file_exists($file_path)) {
            return;
        }

        $content = file_get_contents($file_path);
        $optimized_content = $this->minify_css($content);
        
        $optimized_file = $this->get_optimized_css_file($src);
        
        // Create directory if it doesn't exist
        $cache_dir = dirname($optimized_file['path']);
        if (!file_exists($cache_dir)) {
            wp_mkdir_p($cache_dir);
        }
        
        file_put_contents($optimized_file['path'], $optimized_content);
    }

    /**
     * Create optimized JS file
     *
     * @param string $src
     */
    private function create_optimized_js($src) {
        $file_path = $this->url_to_path($src);
        
        if (!file_exists($file_path)) {
            return;
        }

        $content = file_get_contents($file_path);
        $optimized_content = $this->minify_js($content);
        
        $optimized_file = $this->get_optimized_js_file($src);
        
        // Create directory if it doesn't exist
        $cache_dir = dirname($optimized_file['path']);
        if (!file_exists($cache_dir)) {
            wp_mkdir_p($cache_dir);
        }
        
        file_put_contents($optimized_file['path'], $optimized_content);
    }

    /**
     * Convert URL to file path
     *
     * @param string $url
     * @return string
     */
    private function url_to_path($url) {
        $upload_dir = wp_upload_dir();
        $base_url = home_url();
        $base_path = ABSPATH;
        
        return str_replace($base_url, $base_path, $url);
    }

    /**
     * Minify CSS content
     *
     * @param string $css
     * @return string
     */
    private function minify_css($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        
        // Remove unnecessary spaces
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/;\s*}/', '}', $css);
        $css = preg_replace('/\s*{\s*/', '{', $css);
        $css = preg_replace('/;\s*/', ';', $css);
        $css = preg_replace('/:\s*/', ':', $css);
        
        return trim($css);
    }

    /**
     * Minify JavaScript content
     *
     * @param string $js
     * @return string
     */
    private function minify_js($js) {
        // Remove single line comments
        $js = preg_replace('/\/\/.*$/m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        
        // Remove extra whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove unnecessary spaces around operators
        $js = preg_replace('/\s*([{}();,:])\s*/', '$1', $js);
        
        return trim($js);
    }

    /**
     * Defer non-critical CSS
     */
    public function defer_non_critical_css() {
        if (!get_option('adminx_performance_defer_css', false)) {
            return;
        }

        // This would be implemented with more sophisticated CSS analysis
        // For now, we'll just add the defer attribute to non-critical stylesheets
    }

    /**
     * Defer JavaScript loading
     */
    public function defer_javascript() {
        if (!get_option('adminx_performance_defer_js', false)) {
            return;
        }

        global $wp_scripts;
        
        if (!is_object($wp_scripts)) {
            return;
        }

        foreach ($wp_scripts->registered as $handle => $script) {
            // Skip jQuery and other critical scripts
            if (in_array($handle, array('jquery', 'jquery-core', 'jquery-migrate'))) {
                continue;
            }
            
            // Add defer attribute
            $wp_scripts->add_data($handle, 'defer', true);
        }
    }

    /**
     * Clear optimization cache
     */
    public function clear_optimization_cache() {
        $upload_dir = wp_upload_dir();
        $cache_dirs = array(
            $upload_dir['basedir'] . '/adminx-optimized/css/',
            $upload_dir['basedir'] . '/adminx-optimized/js/'
        );

        foreach ($cache_dirs as $cache_dir) {
            if (file_exists($cache_dir)) {
                $files = glob($cache_dir . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Get optimization statistics
     *
     * @return array
     */
    public function get_optimization_stats() {
        $upload_dir = wp_upload_dir();
        $css_dir = $upload_dir['basedir'] . '/adminx-optimized/css/';
        $js_dir = $upload_dir['basedir'] . '/adminx-optimized/js/';
        
        $stats = array(
            'css_files' => 0,
            'js_files' => 0,
            'total_size_saved' => 0
        );

        if (file_exists($css_dir)) {
            $css_files = glob($css_dir . '*');
            $stats['css_files'] = count($css_files);
        }

        if (file_exists($js_dir)) {
            $js_files = glob($js_dir . '*');
            $stats['js_files'] = count($js_files);
        }

        return $stats;
    }
}