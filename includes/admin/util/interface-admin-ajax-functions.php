<?php

/**
 * @author Wonkasoft
 * @version 2.0.0
 * This file handles Admin end ajax functions interface.
 */

namespace Wc_Insta_Feed\Includes\Admin\Util;

if (!defined('ABSPATH')) {
    exit;
}

interface Admin_Ajax_Functions_Interface
{ 

    /**
     * import images in db
     */
    public function import_selected_insta_images();
    public function object_to_array($result);
    public function insert_insta_records_to_db($data, $tag);

   

  
}
