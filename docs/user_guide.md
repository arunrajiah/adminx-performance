# AdminX Performance - User Guide

## Table of Contents
1. [Installation](#installation)
2. [Getting Started](#getting-started)
3. [Cache Settings](#cache-settings)
4. [Optimization Settings](#optimization-settings)
5. [Image Settings](#image-settings)
6. [Database Settings](#database-settings)
7. [Performance Tools](#performance-tools)
8. [Troubleshooting](#troubleshooting)
9. [Best Practices](#best-practices)
10. [FAQ](#faq)

## Installation

### Automatic Installation
1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins → Add New**
3. Search for "AdminX Performance"
4. Click **Install Now** and then **Activate**

### Manual Installation
1. Download the plugin zip file
2. Navigate to **Plugins → Add New → Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate Plugin**

### After Installation
1. Go to **AdminX → Performance** in your WordPress admin
2. Review the default settings
3. Enable the optimization features you want to use
4. Run initial optimization tools if needed

## Getting Started

### First Steps
After activating AdminX Performance, you'll see a new menu item under **AdminX** in your WordPress admin. The Performance dashboard provides an overview of your site's optimization status.

### Dashboard Overview
The main dashboard displays:
- **Cache Statistics**: Number of cached files and total cache size
- **Asset Optimization**: Count of optimized CSS and JS files
- **Image Optimization**: Percentage of optimized images and space saved
- **Database Status**: Total database size and cleanup opportunities

### Quick Setup
1. **Enable Page Caching**: Go to Cache Settings and enable page caching
2. **Enable Asset Optimization**: Turn on CSS/JS optimization
3. **Configure Image Settings**: Set your preferred image quality
4. **Run Initial Cleanup**: Use the Tools tab to clean your database

## Cache Settings

### Page Caching
Page caching stores static versions of your pages to serve them faster to visitors.

**Settings:**
- **Enable Page Caching**: Turn on/off the caching system
- **Cache Statistics**: View current cache usage

**How It Works:**
- Pages are cached automatically when first visited
- Cache is cleared when posts/pages are updated
- Logged-in users and admin pages are excluded
- POST requests are never cached

**Cache Exclusions:**
- WordPress admin area
- User-specific content (logged-in users)
- WooCommerce cart, checkout, account pages
- Pages with query parameters (except pagination)

### Cache Management
Use the **Clear Cache** tool in the Tools tab to manually clear all cached files when needed.

## Optimization Settings

### CSS/JS Optimization
Reduce file sizes and improve loading speed by optimizing your stylesheets and scripts.

**Available Options:**
- **Enable CSS/JS Optimization**: Minify and combine files
- **Defer CSS Loading**: Load non-critical CSS after page content
- **Defer JavaScript**: Load JavaScript files after page content

**Benefits:**
- Smaller file sizes (typically 20-40% reduction)
- Fewer HTTP requests
- Faster page load times
- Better Core Web Vitals scores

**Important Notes:**
- Test your site after enabling optimization
- Some themes/plugins may require specific files to load immediately
- Use the "Clear Optimization Cache" tool if you encounter issues

### Optimization Process
1. Original files are analyzed and processed
2. Comments and unnecessary whitespace are removed
3. Code is optimized for smaller size
4. Optimized versions are cached for reuse
5. Original files remain unchanged

## Image Settings

### Automatic Image Optimization
Images are automatically optimized when uploaded to your media library.

**Settings:**
- **Enable Image Optimization**: Turn on automatic compression
- **Image Quality**: Set compression level (60-100%)
- **Enable WebP Generation**: Create modern WebP versions
- **WebP Quality**: Set WebP compression level

**Supported Formats:**
- **JPEG**: Lossy compression with quality control
- **PNG**: Lossless optimization
- **WebP**: Modern format with better compression

### Bulk Optimization
Use the **Bulk Optimize Images** tool to process existing images in your media library.

**Process:**
1. Click "Start Optimization" in the Tools tab
2. Images are processed in batches of 10
3. Progress is shown in real-time
4. Optimization continues automatically until complete

**Results:**
- Original images are compressed in place
- WebP versions are created alongside originals
- Optimization statistics are tracked
- Space savings are calculated and displayed

### Image Quality Guidelines
- **85-95%**: High quality, minimal compression
- **75-85%**: Good balance of quality and file size
- **60-75%**: Maximum compression, some quality loss

## Database Settings

### Automatic Cleanup
The plugin can automatically clean your database weekly to remove unnecessary data.

**Settings:**
- **Auto Database Cleanup**: Enable weekly automatic cleanup
- **Database Statistics**: View current database status

**What Gets Cleaned:**
- **Post Revisions**: Keeps 3 most recent revisions per post
- **Spam Comments**: Removes all spam comments
- **Trash Posts**: Removes posts in trash for 30+ days
- **Expired Transients**: Removes outdated temporary data
- **Orphaned Data**: Removes metadata without parent records

### Manual Cleanup
Use the **Database Cleanup** tool for immediate optimization.

**Cleanup Process:**
1. Analyzes database for optimization opportunities
2. Removes unnecessary data safely
3. Optimizes database tables
4. Reports cleanup results

**Safety Notes:**
- Cleanup operations cannot be undone
- Important data is never removed
- Database is backed up before major operations

## Performance Tools

### Available Tools

#### Clear Cache
- Removes all cached pages
- Forces regeneration of cache files
- Use when content isn't updating properly

#### Clear Optimization Cache
- Removes optimized CSS/JS files
- Forces regeneration of optimized assets
- Use when styles/scripts aren't working correctly

#### Bulk Optimize Images
- Processes existing images in media library
- Creates WebP versions where supported
- Shows progress and results in real-time

#### Database Cleanup
- Removes unnecessary database entries
- Optimizes database tables
- Provides detailed cleanup report

#### Performance Test
- Analyzes current site performance
- Measures page load times
- Provides optimization recommendations

### When to Use Tools
- **After Theme Changes**: Clear optimization cache
- **After Plugin Updates**: Clear all caches
- **Before Going Live**: Run full optimization
- **Monthly Maintenance**: Run database cleanup
- **Performance Issues**: Use performance test

## Troubleshooting

### Common Issues

#### Site Looks Broken After Enabling Optimization
**Solution:**
1. Go to Tools tab
2. Click "Clear Optimization Cache"
3. If issue persists, disable CSS/JS optimization temporarily
4. Contact support with specific details

#### Images Not Displaying Correctly
**Solution:**
1. Check file permissions on uploads directory
2. Verify GD or ImageMagick is installed
3. Try disabling WebP generation
4. Re-upload problematic images

#### Cache Not Working
**Solution:**
1. Check if you're logged in (cache disabled for logged-in users)
2. Verify write permissions on uploads directory
3. Clear existing cache and test again
4. Check for conflicting caching plugins

#### Database Cleanup Errors
**Solution:**
1. Ensure adequate database permissions
2. Check available disk space
3. Try running cleanup in smaller batches
4. Contact hosting provider if issues persist

### Performance Issues
If the plugin is causing performance issues:
1. Disable all optimizations temporarily
2. Enable features one by one to identify the cause
3. Check server resources and limits
4. Review error logs for specific issues

### Getting Help
1. Check this user guide first
2. Review the FAQ section
3. Check WordPress error logs
4. Contact support with specific error messages

## Best Practices

### Optimization Strategy
1. **Start Gradually**: Enable one feature at a time
2. **Test Thoroughly**: Check your site after each change
3. **Monitor Performance**: Use tools to measure improvements
4. **Regular Maintenance**: Run cleanup tools monthly

### Cache Management
- Clear cache after major content updates
- Monitor cache size and clear if it grows too large
- Test cache exclusions for dynamic content

### Image Optimization
- Set appropriate quality levels for your content type
- Use WebP for better compression on modern browsers
- Optimize images before upload when possible
- Regular bulk optimization for existing content

### Database Maintenance
- Enable automatic cleanup for hands-off maintenance
- Run manual cleanup before major updates
- Monitor database size and growth trends
- Keep regular database backups

### Performance Monitoring
- Use the performance test tool regularly
- Monitor Core Web Vitals scores
- Test on different devices and connections
- Compare before/after optimization metrics

## FAQ

### General Questions

**Q: Will this plugin conflict with other optimization plugins?**
A: AdminX Performance is designed to work independently. However, we recommend disabling other caching plugins to avoid conflicts.

**Q: Does this plugin work with all themes?**
A: Yes, the plugin works with any properly coded WordPress theme. Some themes may require specific optimization settings.

**Q: Can I use this with WooCommerce?**
A: Absolutely! The plugin automatically excludes WooCommerce dynamic pages from caching while optimizing static content.

### Technical Questions

**Q: What happens to my original images?**
A: Original images are compressed in place. WebP versions are created as additional files. We recommend backing up your media library before bulk optimization.

**Q: How much space will caching use?**
A: Cache size depends on your site size and traffic. Typically 50-200MB for most sites. Monitor cache statistics in the dashboard.

**Q: Can I exclude specific pages from optimization?**
A: Currently, the plugin automatically excludes dynamic pages. Additional exclusion options may be added in future versions.

### Troubleshooting Questions

**Q: My site is slower after enabling the plugin. Why?**
A: This can happen during initial optimization. Clear all caches and allow time for optimization to complete. Contact support if issues persist.

**Q: Some images aren't showing after optimization. What should I do?**
A: Check file permissions and server image processing capabilities. Try disabling WebP generation if issues persist.

**Q: The database cleanup removed too much data. Can I restore it?**
A: Database cleanup only removes unnecessary data like spam and trash. Important content is never touched. Restore from backup if needed.

---

**Need More Help?**
If you can't find the answer to your question in this guide, please contact our support team through the plugin's admin interface or visit our support forum.

**Last Updated**: September 15, 2025
**Plugin Version**: 1.0.0