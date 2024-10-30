<?php
/**
 * Plugin Name: JW Player 7 for Wordpress
 * Plugin URI: http://www.ilghera.com/product/jw-player-7-for-wordpress/
 * Description:  JW Player 7 for Wordpress gives you all what you need to publish videos on your Wordpress posts and pages, with the new JW7. Change skin, position and dimensions of your player. Allow users share and embed your contents.
 * Do you want more? Please check out the premium version.
 * Author: ilGhera
 * Version: 1.0.0
 * Author URI: http://ilghera.com 
 * Requires at least: 3.0
 * Tested up to: 4.3
 */


//HEY, WHAT ARE UOU DOING?
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'plugins_loaded', 'jwppp_load', 100 );	

function jwppp_load() {
	
	//INTERNATIONALIZATION
	load_plugin_textdomain('jwppp', false, basename( dirname( __FILE__ ) ) . '/languages' );

	//FILES REQUIRED
	include( plugin_dir_path( __FILE__ ) . 'includes/jwppp-admin.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/jwppp-functions.php');
}