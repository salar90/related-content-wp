<?php
/*
Plugin Name: Related Content and Products
Description: Display Related content, articles and products from your site and optionally other web sites.
Author: Salar Gholizadeh
Version: 0.1
Author URI: http://salar.one/
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
Text Domain: related-art
Domain Path: /languages
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}
// require_once __DIR__ . "/administration.php";
// register_uninstall_hook(__FILE__, 'sg_api_stats_uninstall');
// register_activation_hook( __FILE__, 'sg_api_stats_activation' );
// register_deactivation_hook( __FILE__, 'sg_api_stats_deactivation' );

function sg_init_related_content(){
	include_once __DIR__ . "/class-wp-api-stats.php";
	global $relatedContent;
	$WP_API_Stats = new SG_API_Stats();
}