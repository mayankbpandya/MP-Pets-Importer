<?php

/**
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.mayankpandya.com/
 * @since             1.0.0
 * @package           MP_Pets_Importer
 *
 * @wordpress-plugin
 * Plugin Name:       MP Pets Importer
 * Plugin URI:        https://www.mayankpandya.com/
 * Description:       This plugin is designed to import data on adoptable pets, such as dogs and cats, from the PetPoint API.
 * Version:           1.0.0
 * Author:            Mayank Pandya
 * Author URI:        https://www.mayankpandya.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mp-pets-importer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'MP_PETS_IMPORTER', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mp-pets-importer-activator.php
 */
function activate_mp_pets_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mp-pets-importer-activator.php';
	Mp_Pets_Importer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mp-pets-importer-deactivator.php
 */
function deactivate_mp_pets_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mp-pets-importer-deactivator.php';
	Mp_Pets_Importer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mp_pets_importer' );
register_deactivation_hook( __FILE__, 'deactivate_mp_pets_importer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mp-pets-importer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mp_pets_importer() {

	$plugin = new Mp_Pets_Importer();
	$plugin->run();

}
run_mp_pets_importer();
