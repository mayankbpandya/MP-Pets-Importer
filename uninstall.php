<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * @link       https://www.mayankpandya.com/
 * @since      1.0.0
 *
 * @package    MP_Pets_Importer
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

class Mp_Pets_Importer_Uninstaller {

    /**
     * Removes custom post types and associated data.
     *
     * @since 1.0.0
     */
    public static function uninstall() {
        // Delete all posts of custom post type 'cat'.
        self::delete_posts_by_type('cat');

        // Delete all posts of custom post type 'dog'.
        self::delete_posts_by_type('dog');

        // Flush rewrite rules to clean up permalinks.
        flush_rewrite_rules();
    }

    /**
     * Deletes all posts of a specific post type.
     *
     * @param string $post_type The post type to delete.
     */
    private static function delete_posts_by_type($post_type) {
        $posts = get_posts(array(
            'post_type'      => $post_type,
            'numberposts'    => -1,
            'post_status'    => 'any',
        ));

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true); // Delete the post permanently.
        }
    }
}

// Trigger uninstallation process.
Mp_Pets_Importer_Uninstaller::uninstall();