=== AdminX Performance ===
Contributors: adminx
Tags: performance, cache, optimization, speed, minify, images, database
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Comprehensive performance optimization plugin with caching, CSS/JS minification, image optimization, and database cleanup.

== Description ==

AdminX Performance is a comprehensive WordPress performance optimization plugin designed to make your website faster and more efficient. This plugin combines multiple optimization techniques into one easy-to-use solution.

**Key Features:**

* **Page Caching** - Automatically cache pages for faster load times with smart cache invalidation
* **CSS/JS Optimization** - Minify and combine CSS/JavaScript files to reduce file sizes and HTTP requests
* **Image Optimization** - Compress images on upload with WebP support for modern browsers
* **Database Cleanup** - Remove unnecessary data like revisions, spam comments, and expired transients
* **Scheduled Optimization** - Automatic database optimization and cache management

**Performance Benefits:**

* Faster page load times through intelligent caching
* Reduced bandwidth usage with optimized assets
* Improved Core Web Vitals scores
* Better user experience and SEO rankings
* Reduced server load and resource usage

**Local Processing:**

All optimization processes run locally on your server - no external API calls or third-party dependencies required. Your data stays on your server for maximum privacy and security.

**Easy to Use:**

* Simple one-click optimization tools
* Detailed performance statistics and reports
* Automatic optimization with manual override options
* Clean, intuitive admin interface

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/adminx-performance` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the AdminX → Performance screen to configure the plugin settings.
4. Enable the optimization features you want to use.
5. Use the Tools tab to run manual optimizations as needed.

== Frequently Asked Questions ==

= Does this plugin work with other caching plugins? =

AdminX Performance includes its own caching system. We recommend disabling other caching plugins to avoid conflicts, though the plugin is designed to work alongside most other optimization tools.

= Will this plugin slow down my admin area? =

No, AdminX Performance only optimizes the frontend of your website. The admin area performance is not affected by the optimization features.

= Can I exclude certain pages from caching? =

Yes, the plugin automatically excludes admin pages, user-specific content, and POST requests from caching. Additional exclusion options are available in the settings.

= Is image optimization reversible? =

Image optimization compresses the original files. We recommend backing up your media library before running bulk optimization. Individual images can be re-uploaded if needed.

= How often does database cleanup run? =

By default, database cleanup runs weekly. You can disable automatic cleanup and run it manually from the Tools section, or adjust the schedule in the settings.

= Does this work with WooCommerce? =

Yes, AdminX Performance is compatible with WooCommerce and automatically excludes cart, checkout, and account pages from caching to ensure proper functionality.

= What image formats are supported for optimization? =

The plugin supports JPEG, PNG, and GIF images. WebP generation is available for JPEG and PNG images when your server supports it.

= Can I revert optimization settings? =

Yes, all optimization features can be disabled at any time. Cached files and optimized assets can be cleared using the tools provided in the admin interface.

== Screenshots ==

1. Main performance dashboard showing optimization statistics
2. Cache settings and configuration options
3. CSS/JS optimization settings
4. Image optimization settings with quality controls
5. Database cleanup tools and statistics
6. Performance tools for manual optimization

== Changelog ==

= 1.0.0 =
* Initial release
* Page caching with automatic invalidation
* CSS/JS minification and combination
* Image compression with WebP support
* Database cleanup and optimization
* Performance monitoring and statistics
* Bulk image optimization tools
* Scheduled maintenance features

== Upgrade Notice ==

= 1.0.0 =
Initial release of AdminX Performance. Install to start optimizing your WordPress site performance.

== Technical Requirements ==

**Minimum Requirements:**
* WordPress 5.0 or higher
* PHP 7.4 or higher
* MySQL 5.6 or higher

**Recommended:**
* WordPress 6.0 or higher
* PHP 8.0 or higher
* MySQL 8.0 or higher
* GD or ImageMagick extension for image optimization
* WebP support for modern image formats

**Server Requirements:**
* Write permissions for wp-content/uploads directory
* Sufficient disk space for cache files
* Memory limit of at least 128MB (256MB recommended)

== Support ==

For support, feature requests, or bug reports, please visit our support forum or contact us through the plugin's admin interface.

**Documentation:**
Complete documentation is available in the plugin's docs folder and online at our website.

**Contributing:**
This plugin is open source. Contributions are welcome through our GitHub repository.

== Privacy ==

AdminX Performance processes data locally on your server and does not send any information to external services. All optimization processes respect WordPress privacy standards and user data protection requirements.

The plugin may store optimization statistics and settings in your WordPress database. No personal user data is collected or transmitted outside of your website.