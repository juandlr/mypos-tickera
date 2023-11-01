<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 2018-11-09
 * Time: 01:33
 */

namespace Arkitekt;


class Shortcodes
{

    function __construct()
    {
        // register shortcodes
        // [arkitekt_register_form], [arkitekt_member_profile], [arkitekt_members]
        add_shortcode('arkitekt_register_form', [$this, 'registerProfile']);
        add_shortcode('arkitekt_member_profile', [$this, 'memberProfile']);
        add_shortcode('arkitekt_members', [$this, 'listMembers']);
    }

    function registerProfile($atts)
    {
        ob_start();
        include('templates/registration-form.tpl.php');
        $html = ob_get_clean();
        return $html;
    }

    function memberProfile($atts)
    {
        $user = wp_get_current_user();
        if ($user && in_array('arkitekt_member', $user->roles)) {
            ob_start();
            include('templates/profile.tpl.php');
            $html = ob_get_clean();
            return $html;
        } else {
            return __('Only arkitekt members can access this page', 'arkitekt-members');
        }
    }

    function listMembers()
    {
        $user = wp_get_current_user();
        if ($user && in_array('arkitekt_member', $user->roles) || current_user_can('administrator')) {
            $total_members = count_users()['avail_roles']['arkitekt_member'];
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $per_page = 10;
            $total_pages = intval($total_members / $per_page) + 1;
            $args = array(
                'role' => 'arkitekt_member',
                'offset' => $paged ? ($paged - 1) * $per_page : 0,
                'number' => $per_page,
            );
            $users = get_users($args);
            ob_start();
            include('templates/member-list.tpl.php');
            $html = ob_get_clean();
            return $html;
        } else {
            return __("Only arkitekt members can access this page", 'arkitekt-members');
        }
    }
}

new Shortcodes();


