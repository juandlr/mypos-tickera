<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 2018-11-09
 * Time: 01:33
 */

namespace SafeGuard;


class Shortcodes
{
    public function __construct()
    {
        // register shortcodes
        add_shortcode('pest_inspection', [$this, 'pestInspection']);
    }

    public function pestInspection($atts)
    {
        ob_start();
        include('templates/pest-inspection.tpl.php');
        $html = ob_get_clean();
        return $html;
    }
}

new Shortcodes();


