<?php

/**
 * @author Webkul
 * @version 2.0.0
 * This file handles assets interface.
 */

namespace Wc_Insta_Feed\Templates\Admin\Tag\Util;

if (!defined('ABSPATH')) {
    exit;
}

interface Tag_Interface
{
   /**
     * Get insta add tag template
     */
    public function get_insta_add_tag_template();
}
