<?php
/**
 * Plugin Name: SafeGuard Calculator
 * Version: 1.0
 * Author: Juan
 * Text Domain: safeguard
 * Author URI:  https://admin123.net
 */

namespace SafeGuard;

class SafeCalculator
{
    public function __construct()
    {
        // shortcodes
        require_once(plugin_dir_path(__FILE__) . 'Shortcodes.php');

        // register styles
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts()
    {
        wp_enqueue_script("jquery-ui-slider", false, array("jquery"), false, true);
        wp_enqueue_script('safecalculator-scripts', plugins_url('script.js', __FILE__), array("jquery"), false, true);
        wp_enqueue_style('safecalculator-styles', plugins_url('styles.css', __FILE__));
    }
}

$SafeCalculator = new SafeCalculator();