<?php

/**
 * Plugin Name:       Gravity Forms - Referer Tracking
 * Plugin URI:        https://github.com/ANEX-Agency/Gravityforms-Referer-Tracking
 * Description:       Saves Referers within a Cookie and submits them within hidden fields from a (gravity) form submission
 * Version:           1.3.0
 * Author:            ANEX
 * Author URI:        http://anex.at
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gravityforms-referer-tracking
 * Domain Path:       /languages
 */

// If this file is called directly or Gravity Forms isn't loaded, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GF_REFERER_TRACKING_VERSION', '1.3.0' );

// If Gravity Forms is loaded, bootstrap the Referer Tracking Add-On.
add_action( 'gform_loaded', array( 'GF_Referer_Tracking_Bootstrap', 'load' ), 5 );

/**
 * Class GF_Referer_Tracking_Bootstrap
 *
 * Handles the loading of the Referer Tracking Add-On and registers with the Add-On Framework.
 */
class GF_Referer_Tracking_Bootstrap {
	
	/**
	 * If the Feed Add-On Framework exists, Referer Tracking Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load(){

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once 'includes/class-gf-referer-tracking.php';

		GFAddOn::register( 'GF_Referer_Tracking' );
		
	}
}

/**
 * Returns an instance of the GF_Referer_Tracking class
 *
 * @see    GF_Referer_Tracking::get_instance()
 *
 * @return object GF_Referer_Tracking
 */
function gf_referer_tracking() {
	return GF_Referer_Tracking::get_instance();
}