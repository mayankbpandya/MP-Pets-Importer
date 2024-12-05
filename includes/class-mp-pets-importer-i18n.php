<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.mayankpandya.com/
 * @since      1.0.0
 *
 * @package    MP_Pets_Importer
 * @subpackage MP_Pets_Importer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    MP_Pets_Importer
 * @subpackage MP_Pets_Importer/includes
 * @author     Mayank Pandya <mayankbpandya@hotmail.com>
 */
class Mp_Pets_Importer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mp-pets-importer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
