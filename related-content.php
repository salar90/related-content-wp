<?php
/*
Plugin Name: Related Content and Products
Description: Display Related content, articles and products from your site and optionally other web sites.
Author: Salar Gholizadeh
Version: 0.1
Author URI: http://salar.one/
Requires at least: 4.4
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
Text Domain: related-content
Domain Path: /languages
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

function related_content_load_plugin_textdomain() {
    load_plugin_textdomain( 'related-content', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'related_content_load_plugin_textdomain' );

include_once __DIR__ . "/class-related-content.php";
function SG_related_content(){
	static $rc;
	if(empty($rc)){
		$rc = new SG_Related_Content();
		$rc->init();
	}
	return $rc;
}
SG_related_content();