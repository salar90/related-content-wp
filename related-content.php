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