<?php
/*
Plugin Name: Extend Theme Customizer
Plugin URI: https://github.com/devyg/extend-theme-customizer
Description: Extend Theme Customizer with a JSON file
Version: 1.0
Author: Devyg
Author URI: http://devyg.com/
GitHub Plugin URI: devyg/extend-theme-customizer
GitHub Branch: master
*/

if ( ! defined( 'WPINC' ) ) {
  exit;
}

/**
 * defined Base Dir
 */

define( 'ETC_BASE_DIR', dirname( __FILE__ ) );
define( 'ETC_DEFAULT_JSON', dirname( __FILE__ ) . '/json/theme-customizer-settings-default.json');


/**
 * Load plugin textdomain.
 *
 * @since 1.0
 */
add_action( 'plugins_loaded', 'etc_load_textdomain' );

function etc_load_textdomain() {
  load_plugin_textdomain( 'extend-theme-customizer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Include Class File
 * @since 1.0
 */

require_once( dirname( __FILE__ ) . '/inc/customizer.php' );
require_once( dirname( __FILE__ ) . '/inc/customizer_from_json.php' );

/**
 * Action Hook Plugin Loaded
 * @since 1.0
 */

add_action( 'plugins_loaded', array( 'ETC_Theme_Customizer', 'get_instance' ) );

/**
 * Load Admin Class
 * @since 1.0
 */

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

  require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );

  add_action( 'plugins_loaded', array( 'ETC_Admin', 'get_instance' ) );

}
