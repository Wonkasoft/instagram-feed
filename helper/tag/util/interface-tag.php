<?php

/**
 * @author Wonkasoft
 * @version 2.0.0
 * This file handles core config interface.
 */

namespace Wc_Insta_Feed\Helper\Tag\Util;

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
