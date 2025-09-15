/**
 * AdminX Performance - Admin JavaScript
 *
 * @package AdminX_Performance
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        AdminXPerformance.init();
    });

    /**
     * AdminX Performance Admin Object
     */
    window.AdminXPerformance = {

        /**
         * Initialize the admin interface
         */
        init: function() {
            this.initTabs();
            this.initButtons();
            this.initProgressBars();
            this.loadStats();
        },

        /**
         * Initialize tab navigation
         */
        initTabs: function() {
            $('.adminx-performance-tab-nav a').on('click', function(e) {
                e.preventDefault();
                
                var target = $(this).attr('href');
                
                // Update active tab
                $('.adminx-performance-tab-nav a').removeClass('active');
                $(this).addClass('active');
                
                // Show target content
                $('.adminx-performance-tab-content').hide();
                $(target).show();
                
                // Save active tab in localStorage
                localStorage.setItem('adminx_performance_active_tab', target);
            });

            // Restore active tab from localStorage
            var activeTab = localStorage.getItem('adminx_performance_active_tab');
            if (activeTab && $(activeTab).length) {
                $('.adminx-performance-tab-nav a[href="' + activeTab + '"]').click();
            } else {
                $('.adminx-performance-tab-nav a:first').click();
            }
        },

        /**
         * Initialize button actions
         */
        initButtons: function() {
            var self = this;

            // Clear cache button
            $('.adminx-clear-cache').on('click', function(e) {
                e.preventDefault();
                self.clearCache($(this));
            });

            // Clear optimization cache button
            $('.adminx-clear-optimization-cache').on('click', function(e) {
                e.preventDefault();
                self.clearOptimizationCache($(this));
            });

            // Run database cleanup button
            $('.adminx-run-db-cleanup').on('click', function(e) {
                e.preventDefault();
                self.runDatabaseCleanup($(this));
            });

            // Bulk optimize images button
            $('.adminx-bulk-optimize-images').on('click', function(e) {
                e.preventDefault();
                self.bulkOptimizeImages($(this));
            });

            // Test performance button
            $('.adminx-test-performance').on('click', function(e) {
                e.preventDefault();
                self.testPerformance($(this));
            });
        },

        /**
         * Initialize progress bars
         */
        initProgressBars: function() {
            $('.adminx-performance-progress-bar').each(function() {
                var $bar = $(this);
                var percentage = $bar.data('percentage') || 0;
                
                setTimeout(function() {
                    $bar.css('width', percentage + '%');
                }, 100);
            });
        },

        /**
         * Load performance statistics
         */
        loadStats: function() {
            var self = this;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'adminx_performance_get_stats',
                    nonce: adminx_performance_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.updateStats(response.data);
                    }
                }
            });
        },

        /**
         * Update statistics display
         */
        updateStats: function(stats) {
            // Update cache stats
            if (stats.cache) {
                $('.cache-files-count').text(stats.cache.total_files);
                $('.cache-size').text(this.formatBytes(stats.cache.total_size));
            }

            // Update optimization stats
            if (stats.optimization) {
                $('.optimized-css-files').text(stats.optimization.css_files);
                $('.optimized-js-files').text(stats.optimization.js_files);
            }

            // Update image optimization stats
            if (stats.images) {
                $('.total-images').text(stats.images.total_images);
                $('.optimized-images').text(stats.images.optimized_images);
                $('.optimization-percentage').text(stats.images.percentage_optimized + '%');
                $('.size-savings').text(this.formatBytes(stats.images.total_savings));
                
                // Update progress bar
                $('.image-optimization-progress .adminx-performance-progress-bar')
                    .css('width', stats.images.percentage_optimized + '%');
            }

            // Update database stats
            if (stats.database) {
                $('.db-size').text(stats.database.total_size_mb + ' MB');
                $('.revisions-count').text(stats.database.counts.revisions);
                $('.spam-comments-count').text(stats.database.counts.spam_comments);
                $('.trash-posts-count').text(stats.database.counts.trash_posts);
            }
        },

        /**
         * Clear page cache
         */
        clearCache: function($button) {
            var self = this;
            var originalText = $button.text();
            
            $button.prop('disabled', true)
                   .html('<span class="adminx-performance-spinner"></span> Clearing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'adminx_performance_clear_cache',
                    nonce: adminx_performance_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotice('Cache cleared successfully!', 'success');
                        self.loadStats();
                    } else {
                        self.showNotice('Failed to clear cache: ' + response.data, 'error');
                    }
                },
                error: function() {
                    self.showNotice('Failed to clear cache. Please try again.', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Clear optimization cache
         */
        clearOptimizationCache: function($button) {
            var self = this;
            var originalText = $button.text();
            
            $button.prop('disabled', true)
                   .html('<span class="adminx-performance-spinner"></span> Clearing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'adminx_performance_clear_optimization_cache',
                    nonce: adminx_performance_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotice('Optimization cache cleared successfully!', 'success');
                        self.loadStats();
                    } else {
                        self.showNotice('Failed to clear optimization cache: ' + response.data, 'error');
                    }
                },
                error: function() {
                    self.showNotice('Failed to clear optimization cache. Please try again.', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Run database cleanup
         */
        runDatabaseCleanup: function($button) {
            var self = this;
            var originalText = $button.text();
            
            if (!confirm('Are you sure you want to run database cleanup? This action cannot be undone.')) {
                return;
            }
            
            $button.prop('disabled', true)
                   .html('<span class="adminx-performance-spinner"></span> Cleaning...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'adminx_performance_run_db_cleanup',
                    nonce: adminx_performance_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var results = response.data;
                        var message = 'Database cleanup completed!\n';
                        message += 'Revisions deleted: ' + results.revisions_deleted + '\n';
                        message += 'Spam comments deleted: ' + results.spam_comments_deleted + '\n';
                        message += 'Trash posts deleted: ' + results.trash_posts_deleted + '\n';
                        message += 'Transients deleted: ' + results.transients_deleted;
                        
                        self.showNotice(message, 'success');
                        self.loadStats();
                    } else {
                        self.showNotice('Failed to run database cleanup: ' + response.data, 'error');
                    }
                },
                error: function() {
                    self.showNotice('Failed to run database cleanup. Please try again.', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Bulk optimize images
         */
        bulkOptimizeImages: function($button) {
            var self = this;
            var originalText = $button.text();
            
            $button.prop('disabled', true)
                   .html('<span class="adminx-performance-spinner"></span> Optimizing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'adminx_performance_bulk_optimize_images',
                    nonce: adminx_performance_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var results = response.data;
                        var message = 'Optimized ' + results.optimized + ' images.';
                        if (results.remaining > 0) {
                            message += ' ' + results.remaining + ' images remaining.';
                        }
                        
                        self.showNotice(message, 'success');
                        self.loadStats();
                        
                        // Continue optimization if there are remaining images
                        if (results.remaining > 0) {
                            setTimeout(function() {
                                self.bulkOptimizeImages($button);
                            }, 2000);
                            return;
                        }
                    } else {
                        self.showNotice('Failed to optimize images: ' + response.data, 'error');
                    }
                },
                error: function() {
                    self.showNotice('Failed to optimize images. Please try again.', 'error');
                },
                complete: function() {
                    if (!$button.prop('disabled')) {
                        $button.prop('disabled', false).text(originalText);
                    }
                }
            });
        },

        /**
         * Test website performance
         */
        testPerformance: function($button) {
            var self = this;
            var originalText = $button.text();
            
            $button.prop('disabled', true)
                   .html('<span class="adminx-performance-spinner"></span> Testing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'adminx_performance_test_performance',
                    nonce: adminx_performance_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var results = response.data;
                        var message = 'Performance Test Results:\n';
                        message += 'Page Load Time: ' + results.load_time + 'ms\n';
                        message += 'Memory Usage: ' + self.formatBytes(results.memory_usage) + '\n';
                        message += 'Database Queries: ' + results.db_queries;
                        
                        self.showNotice(message, 'success');
                    } else {
                        self.showNotice('Failed to run performance test: ' + response.data, 'error');
                    }
                },
                error: function() {
                    self.showNotice('Failed to run performance test. Please try again.', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Show admin notice
         */
        showNotice: function(message, type) {
            type = type || 'info';
            
            var $notice = $('<div class="adminx-performance-notice ' + type + '">' + 
                          message.replace(/\n/g, '<br>') + '</div>');
            
            $('.adminx-performance-wrap').prepend($notice);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $notice.remove();
                });
            }, 5000);
        },

        /**
         * Format bytes to human readable format
         */
        formatBytes: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    };

})(jQuery);