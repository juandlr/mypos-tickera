<?php

namespace Arkitekt;

class Admin
{
    public function __construct()
    {

        add_action('admin_menu', [$this, 'init']);
        add_filter('manage_users_columns', [$this, 'usersColumns']);
        add_filter('manage_users_custom_column', [$this, 'customUsersColumns'], 10, 3);
        add_filter('manage_users_sortable_columns', [$this, 'userSortableColumns']);
        add_action('pre_get_users', [$this, 'sortByColumn']);
        add_action('admin_head', [$this, 'adminCss']);
    }


    public function init()
    {
        global $submenu;
        add_menu_page('Arkitekt Members', 'Arkitekt Members', 'manage_options', 'arkitekt-members',
            [$this, 'memberList']);
        //this is a submenu
        add_submenu_page('arkitekt-members',
            'Add Member Id',
            'Add Member Id',
            'manage_options',
            'member-id-create',
            [$this, 'addMemberId']);

        //this submenu is HIDDEN, however, we need to add it anyways
        add_submenu_page(null,
            'Update Member',
            'Update',
            'manage_options',
            'member-id-update',
            [$this, 'updateMember']);
        $users_url = get_site_url() . "/wp-admin/users.php?role=arkitekt_member";
        $submenu['arkitekt-members'][] = array(
            'Arkitekt WP Users',
            'manage_options',
            $users_url
        );
    }

    public function memberList()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "arkitekt_members";
        $rows = $wpdb->get_results("SELECT member_id from $table_name");
        $total = count($rows);
        $paged = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;
        $per_page = 30;
        $total_pages = intval($total / $per_page) + 1;
        $offset = $paged ? ($paged - 1) * $per_page : 0;
        $rows = $wpdb->get_results("SELECT member_id from {$table_name} ORDER BY CAST(member_id AS unsigned) ASC LIMIT {$offset}, {$per_page}");
        if (isset($_POST['memberid_search'])) {
            $member_id = $_POST['memberid_search'];
            $rows = $wpdb->get_results($wpdb->prepare("SELECT * from wp_arkitekt_members where member_id = %s",
                $member_id));
            if (!$rows) {
                $error = "No Results!";
            }
        }
        ob_start();
        include('member-list.tpl.php');
        $html = ob_get_clean();
        echo $html;
    }

    public function addMemberId()
    {
        $member_id = $_POST["member_id"];
        //insert
        if (isset($_POST['insert'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . "arkitekt_members";

            $inserted = $wpdb->insert(
                $table_name,
                array('member_id' => $member_id),
                array('%s')
            );
            if ($inserted) {
                $message = "Member ID inserted";
            } else {
                $error = "Error!";
            }
        }
        ob_start();
        include('member-create.tpl.php');
        $html = ob_get_clean();
        echo $html;
    }

    public function updateMember()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "arkitekt_members";
        $get_id = $_GET["member_id"];
        $post_id = $_POST["member_id"];
        $message = null;
        //update
        if (isset($_POST['update'])) {
            $updated = $wpdb->update(
                $table_name,
                array('member_id' => $post_id),
                array('member_id' => $get_id),
                array('%s'),
                array('%s')
            );
            if ($updated) {
                $message = "Updated!";
            } else {
                $error = "Error!";
            }
        } // delete
        else {
            if (isset($_POST['delete'])) {
                $deleted = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE member_id = %s", $post_id));
                if ($deleted) {
                    $message = "Deleted!";
                } else {
                    $error = "Error!";
                }
            } else {
                $members = $wpdb->get_results($wpdb->prepare("SELECT member_id from $table_name where member_id = %s",
                    $get_id));
                $member_id = $members[0]->member_id;
            }
        }
        ob_start();
        include('member-update.tpl.php');
        $html = ob_get_clean();
        echo $html;
    }

    public function usersColumns($columns)
    {
        if ($_GET['role'] == 'arkitekt_member') {
            unset($columns['posts']);
            unset($columns['role']);
            $columns['member_updated'] = __('Date', 'arkitekt-members');
            $columns['member_id'] = __('Nr', 'arkitekt-members');
            $columns['education'] = __('Education', 'arkitekt-members');
            $columns['work_exp'] = __('Experience', 'arkitekt-members');
            $columns['fields_interest'] = __('Interests', 'arkitekt-members');
            $columns['year_birth'] = __('Birth', 'arkitekt-members');
            $columns['languages'] = __('Languages', 'arkitekt-members');
            $columns['competencies'] = __('Skills', 'arkitekt-members');
            $columns['work_type'] = __('Work', 'arkitekt-members');
            $columns['cv'] = 'Cv';
        }
        return $columns;
    }

    /**
     * This returns the value for the column
     * @param $output
     * @param $column_name
     * @param $user_id
     * @return mixed
     */
    public function customUsersColumns($output, $column_name, $user_id)
    {
        switch ($column_name) {
            case 'member_updated':
                $date_format = 'Y/m/d g:i:s a';
                $updated = get_user_meta($user_id, 'profile_last_modified', true);
                $user_data = get_userdata($user_id);
                $registered_date = $user_data->user_registered;
                if (!$updated) {
                    update_user_meta($user_id, "profile_last_modified", $registered_date);
                    $updated = get_user_meta($user_id, 'profile_last_modified', true);
                }
                $date = strtotime(get_date_from_gmt($updated));
                $date = date_i18n( $date_format, $date );
                return $date;
            case 'member_id':
                $value = get_user_meta($user_id, 'member_id', true);
                return $value;
            case 'education':
                $value = get_user_meta($user_id, 'education', true);
                return $value;
            case 'work_exp':
                $value = get_user_meta($user_id, 'work_experience', true);
                return $value;
            case 'fields_interest':
                $value = get_user_meta($user_id, 'fields_interest', true);
                return $value;
            case 'year_birth':
                $value = get_user_meta($user_id, 'year_birth', true);
                return $value;
            case 'languages':
                $value = get_user_meta($user_id, 'spoken_languages', true);
                return $value;
            case 'competencies':
                $value = get_user_meta($user_id, 'key_competencies', true);
                return $value;
            case 'work_type':
                $value = get_user_meta($user_id, 'work_type', true);
                return $value;
            case 'cv':
                $value = get_user_meta($user_id, 'cv', true);
                $cv = wp_get_attachment_url($value);
                if ($cv) {
                    $pdf_icon = plugin_dir_url(__DIR__) . "images/pdf.svg";
                    $value = "<a target='_blank' href='{$cv}'><img style='height: 20px' src='{$pdf_icon}' alt=''></a>";
                    return $value;
                }
        }
    }

    public function userSortableColumns($columns)
    {
        unset($columns['username']);
        $columns['member_updated'] = 'member_date';
        $columns['member_id'] = 'membership';
        $columns['name'] = 'name';
        $columns['education'] = 'education';
        $columns['work_exp'] = 'experience';
        $columns['fields_interest'] = 'interests';
        $columns['year_birth'] = 'birth';
        $columns['languages'] = 'languages';
        $columns['competencies'] = 'skills';
        $columns['work_type'] = 'work';
        $columns['cv'] = 'cv';
        return $columns;
    }

    public function sortByColumn($query)
    {

        $orderby = $query->get('orderby');

        switch ($orderby) {
            case 'member_date':
                $query->set('meta_key', 'profile_last_modified');
                $query->set('orderby', 'meta_value');
                break;
            case 'membership':
                $query->set('meta_key', 'member_id');
                $query->set('orderby', 'meta_value_num');
                break;
            case 'name':
                $query->set('meta_key', 'first_name');
                $query->set('orderby', 'meta_value');
                break;
            case 'education':
                $query->set('meta_key', 'education');
                $query->set('orderby', 'meta_value');
                break;
            case 'experience':
                $query->set('meta_key', 'work_experience');
                $query->set('orderby', 'meta_value_num');
                break;
            case'interests':
                $query->set('meta_key', 'interests');
                $query->set('orderby', 'meta_value');
                break;
            case 'birth':
                $query->set('meta_key', 'year_birth');
                $query->set('orderby', 'meta_value_num');
                break;
            case 'languages':
                $query->set('meta_key', 'spoken_languages');
                $query->set('orderby', 'meta_value');
                break;
            case 'skills':
                $query->set('meta_key', 'key_competencies');
                $query->set('orderby', 'meta_value');
                break;
            case 'work':
                $query->set('meta_key', 'key_competencies');
                $query->set('orderby', 'meta_value');
                break;
            case 'cv':
                $query->set('meta_key', 'cv');
                $query->set('orderby', 'meta_value_num');
                break;
        }
    }


    public function adminCss()
    {
        echo '<style type="text/css">';
        echo '.column-member_id {text-align: center; width:60px !important;}';
        echo '.column-year_birth {width: 60px;}';
        echo '.column-cv {width: 60px;}';
        echo '.column-member_updated {width: 100px;}';
        echo '</style>';
    }
}

$Admin = new Admin();
