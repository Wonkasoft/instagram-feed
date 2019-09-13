<?php
/**
 * Provide action links for the plugin
 *
 * This file is used to markup the plugin-facing aspects of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin/partials
 */

add_filter( 'plugin_action_links_' . WONKASOFT_INSTAFEED_BASENAME, 'wonkasoft_instafeed_add_settings_link_filter' , 10, 1);

function wonkasoft_instafeed_add_settings_link_filter( $links ) { 
	global $wonkasoft_instafeed_page;
	$links_addon = '<a href="' . menu_page_url( $wonkasoft_instafeed_page, 0 ) . '" target="_self">Settings</a>';
	array_unshift( $links, $links_addon );
	$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank"><img src="' . plugins_url( '../img/wonka-logo.svg', __FILE__ ) . '" style="width: 20px; height: 20px; display: inline-block;
    vertical-align: text-top; float: none;" /></a>';
 return $links; 
}

add_filter( 'plugin_row_meta', 'wonkasoft_instafeed_add_description_link_filter', 10, 2);

function wonkasoft_instafeed_add_description_link_filter( $links, $file ) {
	global $wonkasoft_instafeed_page;
	if ( strpos( $file, 'wonkasoft-instafeed.php' ) !== false ) {
		$links[] = '<a href="' . menu_page_url( $wonkasoft_instafeed_page, 0 ) . '" target="_self">Settings</a>';
		$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank">Donate <img src="' . plugins_url( '../img/wonka-logo.svg', __FILE__ ) . '" style="width: 20px; height: 20px; display: inline-block;
    vertical-align: text-top;" /></a>';
	}
 return $links; 
}