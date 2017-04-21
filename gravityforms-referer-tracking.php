<?php

/**
 * Plugin Name:       Gravity Forms - Referer Tracking
 * Plugin URI:        https://github.com/ANEX-Agency/Gravityforms-Referer-Tracking
 * Description:       Saves Referers within a Cookie and submits them within hidden fields from a (gravity) form submission
 * Version:           1.1.2
 * Author:            ANEX
 * Author URI:        http://anex.at
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gravityforms-referer-tracking
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'gform_loaded', function() {

    require __DIR__ . '/main.php';
    
    GFAddOn::register( 'Rebits_GF_RefTrack' );
	
});