<?php
/**
 * AdminX Performance - Admin Page Template
 *
 * @package AdminX_Performance
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current stats
$cache_manager = new AdminX_Performance_Cache_Manager();
$optimizer = new AdminX_Performance_Optimizer();
$image_optimizer = new AdminX_Performance_Image_Optimizer();
$db_optimizer = new AdminX_Performance_DB_Optimizer();

$cache_stats = $cache_manager->get_cache_stats();
$optimization_stats = $optimizer->get_optimization_stats();
$image_stats = $image_optimizer->get_optimization_stats();
$db_stats = $db_optimizer->get_database_stats();
?>

<div class="adminx-performance-wrap">
    <!-- Header -->
    <div class="adminx-performance-header">
        <h1><?php _e('AdminX Performance', 'adminx-performance'); ?></h1>
        <p><?php _e('Optimize your WordPress site for maximum performance with caching, minification, image optimization, and database cleanup.', 'adminx-performance'); ?></p>
    </div>

    <!-- Statistics Overview -->
    <div class="adminx-performance-stats">
        <div class="adminx-performance-stat-card">
            <h3><?php _e('Page Cache', 'adminx-performance'); ?></h3>
            <div class="adminx-performance-stat-number cache-files-count"><?php echo esc_html($cache_stats['total_files']); ?></div>
            <div class="adminx-performance-stat-label"><?php _e('Cached Files', 'adminx-performance'); ?></div>
            <div class="cache-size"><?php echo esc_html(size_format($cache_stats['total_size'])); ?></div>
        </div>

        <div class="adminx-performance-stat-card">
            <h3><?php _e('Asset Optimization', 'adminx-performance'); ?></h3>
            <div class="adminx-performance-stat-number">
                <span class="optimized-css-files"><?php echo esc_html($optimization_stats['css_files']); ?></span> / 
                <span class="optimized-js-files"><?php echo esc_html($optimization_stats['js_files']); ?></span>
            </div>
            <div class="adminx-performance-stat-label"><?php _e('CSS / JS Files', 'adminx-performance'); ?></div>
        </div>

        <div class="adminx-performance-stat-card">
            <h3><?php _e('Image Optimization', 'adminx-performance'); ?></h3>
            <div class="adminx-performance-stat-number optimization-percentage"><?php echo esc_html($image_stats['percentage_optimized']); ?>%</div>
            <div class="adminx-performance-stat-label">
                <span class="optimized-images"><?php echo esc_html($image_stats['optimized_images']); ?></span> / 
                <span class="total-images"><?php echo esc_html($image_stats['total_images']); ?></span> <?php _e('Images', 'adminx-performance'); ?>
            </div>
            <div class="size-savings"><?php echo esc_html(size_format($image_stats['total_savings'])); ?> <?php _e('saved', 'adminx-performance'); ?></div>
        </div>

        <div class="adminx-performance-stat-card">
            <h3><?php _e('Database', 'adminx-performance'); ?></h3>
            <div class="adminx-performance-stat-number db-size"><?php echo esc_html($db_stats['total_size_mb']); ?> MB</div>
            <div class="adminx-performance-stat-label"><?php _e('Total Size', 'adminx-performance'); ?></div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="adminx-performance-tabs">
        <ul class="adminx-performance-tab-nav">
            <li><a href="#cache-settings" class="active"><?php _e('Cache Settings', 'adminx-performance'); ?></a></li>
            <li><a href="#optimization-settings"><?php _e('Optimization', 'adminx-performance'); ?></a></li>
            <li><a href="#image-settings"><?php _e('Images', 'adminx-performance'); ?></a></li>
            <li><a href="#database-settings"><?php _e('Database', 'adminx-performance'); ?></a></li>
            <li><a href="#tools"><?php _e('Tools', 'adminx-performance'); ?></a></li>
        </ul>

        <!-- Cache Settings Tab -->
        <div id="cache-settings" class="adminx-performance-tab-content">
            <form method="post" action="options.php">
                <?php settings_fields('adminx_performance_cache_settings'); ?>
                
                <table class="adminx-performance-form-table">
                    <tr>
                        <th><?php _e('Enable Page Caching', 'adminx-performance'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminx_performance_cache_enabled" value="1" 
                                       <?php checked(get_option('adminx_performance_cache_enabled', true)); ?> />
                                <?php _e('Enable page caching for faster load times', 'adminx-performance'); ?>
                            </label>
                            <p class="description"><?php _e('Automatically clears cache when posts or pages are updated.', 'adminx-performance'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Cache Statistics', 'adminx-performance'); ?></th>
                        <td>
                            <p><?php printf(__('Total cached files: %d', 'adminx-performance'), $cache_stats['total_files']); ?></p>
                            <p><?php printf(__('Cache size: %s', 'adminx-performance'), size_format($cache_stats['total_size'])); ?></p>
                            <p><?php printf(__('Cache directory: %s', 'adminx-performance'), esc_html($cache_stats['cache_dir'])); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Cache Settings', 'adminx-performance')); ?>
            </form>
        </div>

        <!-- Optimization Settings Tab -->
        <div id="optimization-settings" class="adminx-performance-tab-content" style="display: none;">
            <form method="post" action="options.php">
                <?php settings_fields('adminx_performance_optimization_settings'); ?>
                
                <table class="adminx-performance-form-table">
                    <tr>
                        <th><?php _e('Enable CSS/JS Optimization', 'adminx-performance'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminx_performance_optimize_enabled" value="1" 
                                       <?php checked(get_option('adminx_performance_optimize_enabled', true)); ?> />
                                <?php _e('Minify and combine CSS/JS files', 'adminx-performance'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Defer CSS Loading', 'adminx-performance'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminx_performance_defer_css" value="1" 
                                       <?php checked(get_option('adminx_performance_defer_css', false)); ?> />
                                <?php _e('Defer non-critical CSS loading', 'adminx-performance'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Defer JavaScript', 'adminx-performance'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminx_performance_defer_js" value="1" 
                                       <?php checked(get_option('adminx_performance_defer_js', false)); ?> />
                                <?php _e('Defer JavaScript loading', 'adminx-performance'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Optimization Settings', 'adminx-performance')); ?>
            </form>
        </div>

        <!-- Image Settings Tab -->
        <div id="image-settings" class="adminx-performance-tab-content" style="display: none;">
            <form method="post" action="options.php">
                <?php settings_fields('adminx_performance_image_settings'); ?>
                
                <table class="adminx-performance-form-table">
                    <tr>
                        <th><?php _e('Enable Image Optimization', 'adminx-performance'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminx_performance_image_optimization" value="1" 
                                       <?php checked(get_option('adminx_performance_image_optimization', true)); ?> />
                                <?php _e('Automatically compress uploaded images', 'adminx-performance'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Image Quality', 'adminx-performance'); ?></th>
                        <td>
                            <input type="range" name="adminx_performance_image_quality" 
                                   min="60" max="100" value="<?php echo esc_attr(get_option('adminx_performance_image_quality', 85)); ?>" 
                                   oninput="this.nextElementSibling.value = this.value" />
                            <output><?php echo esc_html(get_option('adminx_performance_image_quality', 85)); ?></output>%
                            <p class="description"><?php _e('Higher values mean better quality but larger file sizes.', 'adminx-performance'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable WebP Generation', 'adminx-performance'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminx_performance_webp_enabled" value="1" 
                                       <?php checked(get_option('adminx_performance_webp_enabled', true)); ?>
                                       <?php disabled(!function_exists('imagewebp')); ?> />
                                <?php _e('Generate WebP versions of images', 'adminx-performance'); ?>
                                <?php if (!function_exists('imagewebp')): ?>
                                    <span class="adminx-performance-status disabled"><?php _e('Not Available', 'adminx-performance'); ?></span>
                                <?php endif; ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('WebP Quality', 'adminx-performance'); ?></th>
                        <td>
                            <input type="range" name="adminx_performance_webp_quality" 
                                   min="60" max="100" value="<?php echo esc_attr(get_option('adminx_performance_webp_quality', 80)); ?>" 
                                   oninput="this.nextElementSibling.value = this.value" />
                            <output><?php echo esc_html(get_option('adminx_performance_webp_quality', 80)); ?></output>%
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Optimization Progress', 'adminx-performance'); ?></th>
                        <td>
                            <div class="image-optimization-progress">
                                <div class="adminx-performance-progress">
                                    <div class="adminx-performance-progress-bar" 
                                         data-percentage="<?php echo esc_attr($image_stats['percentage_optimized']); ?>"></div>
                                </div>
                                <p><?php printf(__('%d of %d images optimized (%s saved)', 'adminx-performance'), 
                                    $image_stats['optimized_images'], 
                                    $image_stats['total_images'], 
                                    size_format($image_stats['total_savings'])); ?></p>
                            </div>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Image Settings', 'adminx-performance')); ?>
            </form>
        </div>

        <!-- Database Settings Tab -->
        <div id="database-settings" class="adminx-performance-tab-content" style="display: none;">
            <form method="post" action="options.php">
                <?php settings_fields('adminx_performance_database_settings'); ?>
                
                <table class="adminx-performance-form-table">
                    <tr>
                        <th><?php _e('Auto Database Cleanup', 'adminx-performance'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminx_performance_auto_db_cleanup" value="1" 
                                       <?php checked(get_option('adminx_performance_auto_db_cleanup', true)); ?> />
                                <?php _e('Run weekly database cleanup automatically', 'adminx-performance'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Database Statistics', 'adminx-performance'); ?></th>
                        <td>
                            <p><?php printf(__('Total database size: %s MB', 'adminx-performance'), $db_stats['total_size_mb']); ?></p>
                            <p><?php printf(__('Post revisions: %s', 'adminx-performance'), '<span class="revisions-count">' . number_format($db_stats['counts']['revisions']) . '</span>'); ?></p>
                            <p><?php printf(__('Spam comments: %s', 'adminx-performance'), '<span class="spam-comments-count">' . number_format($db_stats['counts']['spam_comments']) . '</span>'); ?></p>
                            <p><?php printf(__('Trash posts: %s', 'adminx-performance'), '<span class="trash-posts-count">' . number_format($db_stats['counts']['trash_posts']) . '</span>'); ?></p>
                            <p><?php printf(__('Expired transients: %s', 'adminx-performance'), number_format($db_stats['counts']['transients'])); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Database Settings', 'adminx-performance')); ?>
            </form>
        </div>

        <!-- Tools Tab -->
        <div id="tools" class="adminx-performance-tab-content" style="display: none;">
            <div class="adminx-performance-tools">
                <div class="adminx-performance-tool">
                    <h3><?php _e('Clear Page Cache', 'adminx-performance'); ?></h3>
                    <p><?php _e('Remove all cached pages to force regeneration.', 'adminx-performance'); ?></p>
                    <button type="button" class="adminx-performance-button adminx-clear-cache">
                        <?php _e('Clear Cache', 'adminx-performance'); ?>
                    </button>
                </div>

                <div class="adminx-performance-tool">
                    <h3><?php _e('Clear Optimization Cache', 'adminx-performance'); ?></h3>
                    <p><?php _e('Remove optimized CSS/JS files to force regeneration.', 'adminx-performance'); ?></p>
                    <button type="button" class="adminx-performance-button adminx-clear-optimization-cache">
                        <?php _e('Clear Optimization Cache', 'adminx-performance'); ?>
                    </button>
                </div>

                <div class="adminx-performance-tool">
                    <h3><?php _e('Bulk Optimize Images', 'adminx-performance'); ?></h3>
                    <p><?php _e('Optimize existing images in your media library.', 'adminx-performance'); ?></p>
                    <button type="button" class="adminx-performance-button adminx-bulk-optimize-images">
                        <?php _e('Start Optimization', 'adminx-performance'); ?>
                    </button>
                </div>

                <div class="adminx-performance-tool">
                    <h3><?php _e('Database Cleanup', 'adminx-performance'); ?></h3>
                    <p><?php _e('Clean up revisions, spam comments, and optimize database tables.', 'adminx-performance'); ?></p>
                    <button type="button" class="adminx-performance-button danger adminx-run-db-cleanup">
                        <?php _e('Run Cleanup', 'adminx-performance'); ?>
                    </button>
                </div>

                <div class="adminx-performance-tool">
                    <h3><?php _e('Performance Test', 'adminx-performance'); ?></h3>
                    <p><?php _e('Test your website performance and get optimization recommendations.', 'adminx-performance'); ?></p>
                    <button type="button" class="adminx-performance-button secondary adminx-test-performance">
                        <?php _e('Run Test', 'adminx-performance'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var adminx_performance_ajax = {
        nonce: '<?php echo wp_create_nonce('adminx_performance_ajax'); ?>'
    };
</script>