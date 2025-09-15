<?php
/**
 * Database Optimizer Class
 *
 * @package AdminX_Performance
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class AdminX_Performance_DB_Optimizer
 */
class AdminX_Performance_DB_Optimizer {

    /**
     * Initialize the database optimizer
     */
    public function __construct() {
        add_action('adminx_performance_scheduled_cleanup', array($this, 'run_scheduled_optimization'));
        
        // Schedule cleanup if not already scheduled
        if (!wp_next_scheduled('adminx_performance_scheduled_cleanup')) {
            wp_schedule_event(time(), 'weekly', 'adminx_performance_scheduled_cleanup');
        }
    }

    /**
     * Run scheduled database optimization
     */
    public function run_scheduled_optimization() {
        if (!get_option('adminx_performance_auto_db_cleanup', true)) {
            return;
        }

        $this->cleanup_revisions();
        $this->cleanup_spam_comments();
        $this->cleanup_trash_posts();
        $this->cleanup_expired_transients();
        $this->optimize_database_tables();
    }

    /**
     * Clean up post revisions
     *
     * @param int $keep_revisions Number of revisions to keep per post
     * @return int Number of revisions deleted
     */
    public function cleanup_revisions($keep_revisions = 3) {
        global $wpdb;

        $deleted = 0;

        // Get all posts with revisions
        $posts_with_revisions = $wpdb->get_results("
            SELECT post_parent, COUNT(*) as revision_count
            FROM {$wpdb->posts}
            WHERE post_type = 'revision'
            GROUP BY post_parent
            HAVING revision_count > {$keep_revisions}
        ");

        foreach ($posts_with_revisions as $post) {
            // Get revisions for this post, ordered by date (newest first)
            $revisions = $wpdb->get_results($wpdb->prepare("
                SELECT ID
                FROM {$wpdb->posts}
                WHERE post_parent = %d AND post_type = 'revision'
                ORDER BY post_date DESC
                LIMIT %d, 999999
            ", $post->post_parent, $keep_revisions));

            foreach ($revisions as $revision) {
                wp_delete_post_revision($revision->ID);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Clean up spam and trash comments
     *
     * @return int Number of comments deleted
     */
    public function cleanup_spam_comments() {
        global $wpdb;

        $deleted = $wpdb->query("
            DELETE FROM {$wpdb->comments}
            WHERE comment_approved IN ('spam', 'trash')
        ");

        // Clean up comment meta for deleted comments
        $wpdb->query("
            DELETE cm FROM {$wpdb->commentmeta} cm
            LEFT JOIN {$wpdb->comments} c ON cm.comment_id = c.comment_ID
            WHERE c.comment_ID IS NULL
        ");

        return (int) $deleted;
    }

    /**
     * Clean up trash posts and pages
     *
     * @return int Number of posts deleted
     */
    public function cleanup_trash_posts() {
        global $wpdb;

        // Get trash posts older than 30 days
        $trash_posts = $wpdb->get_col($wpdb->prepare("
            SELECT ID FROM {$wpdb->posts}
            WHERE post_status = 'trash'
            AND post_modified < %s
        ", date('Y-m-d H:i:s', strtotime('-30 days'))));

        $deleted = 0;
        foreach ($trash_posts as $post_id) {
            wp_delete_post($post_id, true);
            $deleted++;
        }

        return $deleted;
    }

    /**
     * Clean up expired transients
     *
     * @return int Number of transients deleted
     */
    public function cleanup_expired_transients() {
        global $wpdb;

        $deleted = $wpdb->query($wpdb->prepare("
            DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b
            WHERE a.option_name LIKE %s
            AND a.option_name NOT LIKE %s
            AND b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
            AND b.option_value < %d
        ", '_transient_%', '_transient_timeout_%', time()));

        return (int) $deleted;
    }

    /**
     * Optimize database tables
     *
     * @return array Results of optimization
     */
    public function optimize_database_tables() {
        global $wpdb;

        $tables = $wpdb->get_col("SHOW TABLES");
        $results = array();

        foreach ($tables as $table) {
            $result = $wpdb->get_row("OPTIMIZE TABLE `{$table}`", ARRAY_A);
            $results[$table] = $result;
        }

        return $results;
    }

    /**
     * Clean up orphaned post meta
     *
     * @return int Number of meta entries deleted
     */
    public function cleanup_orphaned_postmeta() {
        global $wpdb;

        $deleted = $wpdb->query("
            DELETE pm FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE p.ID IS NULL
        ");

        return (int) $deleted;
    }

    /**
     * Clean up orphaned comment meta
     *
     * @return int Number of meta entries deleted
     */
    public function cleanup_orphaned_commentmeta() {
        global $wpdb;

        $deleted = $wpdb->query("
            DELETE cm FROM {$wpdb->commentmeta} cm
            LEFT JOIN {$wpdb->comments} c ON cm.comment_id = c.comment_ID
            WHERE c.comment_ID IS NULL
        ");

        return (int) $deleted;
    }

    /**
     * Clean up orphaned term relationships
     *
     * @return int Number of relationships deleted
     */
    public function cleanup_orphaned_term_relationships() {
        global $wpdb;

        $deleted = $wpdb->query("
            DELETE tr FROM {$wpdb->term_relationships} tr
            LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID
            WHERE p.ID IS NULL
        ");

        return (int) $deleted;
    }

    /**
     * Clean up unused tags
     *
     * @return int Number of tags deleted
     */
    public function cleanup_unused_tags() {
        global $wpdb;

        $deleted = $wpdb->query("
            DELETE t, tt FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'post_tag' AND tt.count = 0
        ");

        return (int) $deleted;
    }

    /**
     * Get database statistics
     *
     * @return array Database statistics
     */
    public function get_database_stats() {
        global $wpdb;

        $stats = array();

        // Get table sizes
        $tables = $wpdb->get_results("
            SELECT table_name as 'table',
                   ROUND(((data_length + index_length) / 1024 / 1024), 2) as 'size_mb'
            FROM information_schema.TABLES
            WHERE table_schema = '{$wpdb->dbname}'
            ORDER BY (data_length + index_length) DESC
        ");

        $stats['tables'] = $tables;

        // Count various items
        $stats['counts'] = array(
            'posts' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish'"),
            'revisions' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'revision'"),
            'trash_posts' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'trash'"),
            'spam_comments' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'spam'"),
            'trash_comments' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'trash'"),
            'transients' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'"),
            'orphaned_postmeta' => $wpdb->get_var("
                SELECT COUNT(*) FROM {$wpdb->postmeta} pm
                LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE p.ID IS NULL
            "),
            'orphaned_commentmeta' => $wpdb->get_var("
                SELECT COUNT(*) FROM {$wpdb->commentmeta} cm
                LEFT JOIN {$wpdb->comments} c ON cm.comment_id = c.comment_ID
                WHERE c.comment_ID IS NULL
            "),
            'unused_tags' => $wpdb->get_var("
                SELECT COUNT(*) FROM {$wpdb->term_taxonomy}
                WHERE taxonomy = 'post_tag' AND count = 0
            ")
        );

        // Calculate total database size
        $total_size = $wpdb->get_var("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2)
            FROM information_schema.TABLES
            WHERE table_schema = '{$wpdb->dbname}'
        ");

        $stats['total_size_mb'] = $total_size;

        return $stats;
    }

    /**
     * Run complete database cleanup
     *
     * @return array Cleanup results
     */
    public function run_complete_cleanup() {
        $results = array();

        $results['revisions_deleted'] = $this->cleanup_revisions();
        $results['spam_comments_deleted'] = $this->cleanup_spam_comments();
        $results['trash_posts_deleted'] = $this->cleanup_trash_posts();
        $results['transients_deleted'] = $this->cleanup_expired_transients();
        $results['orphaned_postmeta_deleted'] = $this->cleanup_orphaned_postmeta();
        $results['orphaned_commentmeta_deleted'] = $this->cleanup_orphaned_commentmeta();
        $results['orphaned_relationships_deleted'] = $this->cleanup_orphaned_term_relationships();
        $results['unused_tags_deleted'] = $this->cleanup_unused_tags();
        $results['tables_optimized'] = $this->optimize_database_tables();

        return $results;
    }
}