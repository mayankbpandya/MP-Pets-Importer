<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.mayankpandya.com/
 * @since      1.0.0
 *
 * @package    MP_Pets_Importer
 * @subpackage MP_Pets_Importer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MP_Pets_Importer
 * @subpackage MP_Pets_Importer/includes
 * @author     Mayank Pandya <mayankbpandya@hotmail.com>
 */
class Mp_Pets_Importer_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Register custom post types.
		self::register_custom_post_types();

		// Flush rewrite rules to ensure proper permalinks.
		flush_rewrite_rules();
	}
/**
	 * Registers the custom post types for Cats and Dogs.
	 *
	 * @since 1.0.0
	 */
	private static function register_custom_post_types() {
		// Register "Cats" custom post type.
		register_post_type('cat', array(
			'labels' => array(
				'name'          => __('Cats', 'mp-pets-importer'),
				'singular_name' => __('Cat', 'mp-pets-importer'),
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_menu'  => true,
			'supports'      => array('title', 'editor', 'thumbnail'),
			'rewrite'       => array('slug' => 'cats'),
		));

		// Register "Dogs" custom post type.
		register_post_type('dog', array(
			'labels' => array(
				'name'          => __('Dogs', 'mp-pets-importer'),
				'singular_name' => __('Dog', 'mp-pets-importer'),
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_menu'  => true,
			'supports'      => array('title', 'editor', 'thumbnail'),
			'rewrite'       => array('slug' => 'dogs'),
		));
	}
}
