<?php

/**
 * @author Webkul
 * @version 2.0.0
 * This file handles assets interface.
 */

namespace Wc_Insta_Feed\Includes\Front\Util;

if (!defined('ABSPATH')) {
    exit;
}

interface Assets_Interface
{
    public function wkcInit();
    public function wkcEnqueueScripts_Admin();
    public function wkcEnqueueScripts_Public();
}
