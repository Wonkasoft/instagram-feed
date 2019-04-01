<?php

/**
 * @author Webkul
 * @version 2.0.0
 * This file handles all front end actions.
 */

namespace Wc_Insta_Feed\Includes\Front;

use Wc_Insta_Feed\Includes\Front;
use Wc_Insta_Feed\Helper;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Front_Hook_Handler')) {
    
    /**
     *
     */

    class Insta_Front_Hook_Handler
    {
        public $function_handler = '';

        public function __construct()
        {
         
            $this->function_handler = new Front\Insta_Front_Function_Handler();
               
            add_action('wp_head', array($this->function_handler, 'insta_feed_initialize'));
 
        }

 

    }
}
