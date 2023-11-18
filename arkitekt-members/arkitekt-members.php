<?php
/*
Plugin Name: Arkitekt Members
Version: 1.0
Author: Juan
Text Domain: arkitekt-members
Author URI:  https://admin123.net
*/

namespace Arkitekt;

define('REGISTRATION_URL', '/member-registration');
define('PROFILE_URL', '/member-profile');
define('MEMBERS_URL', '/arkitekt-members');

class ArkitektMembers
{

    public function __construct()
    {
        global $wpdb;
        session_start();

        // Install database
        register_activation_hook(__FILE__, [$this, 'install']);

        // Admin page
        include_once(plugin_dir_path(__FILE__) . 'admin/Admin.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/user.php');

        // shortcodes
        require_once(plugin_dir_path(__FILE__) . 'Shortcodes.php');

        // Members
        require_once(plugin_dir_path(__FILE__) . 'Member.php');

        // register styles and scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);


        // create member role
        remove_role('arkitekt_member');
        add_role(
            'arkitekt_member',
            __('Member')
        );

        // hide admin bar for role
        add_action('after_setup_theme', [$this, 'disableAdminBar']);

        // redirect to profile page after login
        add_filter('login_redirect', [$this, 'loginRedirect'], 10, 3);
    }

    public function enqueueScripts()
    {
        wp_enqueue_style('arkitekt-members', plugins_url('styles.css', __FILE__));
    }

    public function disableAdminBar()
    {
        if (current_user_can('arkitekt_member')) {
            // user can view admin bar
            show_admin_bar(false); // this line isn't essentially needed by default...
        }
    }

    public function loginRedirect($redirect_to, $request, $user)
    {
        //is there a user to check?
        global $user, $members_url;
        if (!is_wp_error($user) && is_array($user->roles)) {
            if (in_array('arkitekt_member', $user->roles)) {
                $redirect_to = MEMBERS_URL;
            }
        }
        return $redirect_to;
    }

    public function install()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'arkitekt_members';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                member_id varchar(30) DEFAULT '' NOT NULL UNIQUE
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}

new ArkitektMembers();
