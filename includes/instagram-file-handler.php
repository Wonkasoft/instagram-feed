<?php

/**
 * @author Webkul
 * @version 2.0.0
 * This file handles all file includes.
 */

use Wc_Insta_Feed\Includes\Front;
use Wc_Insta_Feed\Includes\Admin;

if (!defined('ABSPATH')) {
    exit;
}

require_once(Insta_Feed_FILE . 'includes/class-insta-template-loader.php');

require_once(Insta_Feed_FILE . 'includes/class-insta-defaults.php');

require_once(Insta_Feed_FILE . 'inc/autoload.php');
 
$script_loader = new Front\Instagram_Script_Loader();

$script_loader->wkcInit();

if (!is_admin()) {

    new Front\Insta_Front_Hook_Handler(); 
    
} else {

    new Admin\Insta_Feed_Hook_Handler(); 
    new Admin\Insta_Admin_Ajax_Hooks();
    new Admin\Insta_Custom_Tab();
    
}
