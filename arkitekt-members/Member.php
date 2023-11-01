<?php

namespace Arkitekt;

class Member
{
    public $table_name = null;
    public $member = null;

    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'arkitekt_members';
        // register post requests
        add_action('init', [$this, 'createMember']);
        add_action('init', [$this, 'updateMember']);
    }

    function createMember()
    {
        global $wpdb;
        $errors = [];
        $this->member = wp_get_current_user();
        if (!isset($_POST['arkitekt_nonce']) || !wp_verify_nonce($_POST['arkitekt_nonce'], 'arkitekt_member')) {
            $errors[] = "Nonce is incorrect!";
        }
        if (in_array('arkitekt_member', $this->member->roles)) {
            $errors[] = "You are already registered!";
        }

        if (isset($_POST['action']) && $_POST['action'] == 'create_member') {
            $role = 'Member';
            $user_name = $_POST['user_name'];
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];
            $user_email = $_POST['email'][0];
            $member_id = $_POST['member_id'];
            $members = $wpdb->get_results(
                $wpdb->prepare("SELECT * from $this->table_name where member_id = %s",
                    $member_id));
            $check_id = $wpdb->get_results(
                $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='member_id' AND meta_value = %s",
                    $member_id));
            if ($pass1 != $pass2) {
                $errors[] = __('Passwords dont match', 'arkitekt-members');
            }
            if (empty($member_id) || empty($members) || $check_id) {
                $errors[] = __('You must have a valid member number', 'arkitekt-members');
            }

            if (empty($errors)) {
                $user_id = wp_create_user($user_name, $pass1, $user_email);
                if (!is_wp_error($user_id)) {
                    $this->member = get_user_by('id', $user_id);
                    $this->member->set_role('arkitekt_member');
                    $errors = array_merge($errors, $this->updateMeta());
                    if (empty($errors)) {
                        //login user after registration
                        $login_data = [];
                        $login_data['user_login'] = $user_name;
                        $login_data['user_password'] = $pass1;
                        $login_data['remember'] = true;
                        wp_signon($login_data, false);
                        $_SESSION['success'] = __("Thank you for registering", 'arkitekt-members');
                        wp_redirect(MEMBERS_URL);
                        exit;
                    }
                }
                $errors[] = $user_id->get_error_message();
            }
            $_SESSION['errors'] = $errors;
        }
    }

    function updateMember()
    {
        global $wpdb;
        if (isset($_POST['update_button'])) {
            $errors = [];
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];
            $member_id = $_POST['member_id'];
            $members = $wpdb->get_results($wpdb->prepare("SELECT member_id from $this->table_name where member_id = %s",
                $member_id));
            if ($members) {
                $errors[] = __('You must have a valid member number', 'arkitekt-members');
            }
            if (!empty($pass1)) {
                if ($pass1 == $pass2) {
                    wp_set_password($pass1, $this->member->ID);
                } else {
                    $errors[] = __('Passwords dont match', 'arkitekt-members');
                }
            }
            if (empty($errors)) {
                $errors = array_merge($errors, $this->updateMeta());
            }
            if ($errors) {
                $_SESSION['errors'] = $errors;
            } else {
                $_SESSION['success'] = __("Your profile has been updated.", 'arkitekt-members');
            }
        } elseif (isset($_POST['delete_button'])) {
            wp_delete_user($this->member->ID);
            wp_redirect(PROFILE_URL);
            exit;
        }
    }

    private function updateMeta()
    {
        global $wpdb;
        $errors = [];
        $member_id = $_POST['member_id'] ?? get_user_meta($this->member->ID, 'member_id', true);
        $user_meta = array(
            'first_name' => array(
                $_POST['first_name'][0],
                $_POST['first_name'][1],
            ),
            'last_name' => array(
                $_POST['last_name'][0],
                $_POST['last_name'][1],
            ),
            'user_email' => array(
                $_POST['email'][0],
                $_POST['email'][1],
            ),
            'member_id' => array(
                $member_id,
            ),
            'education' => array(
                $_POST['education'][0],
                $_POST['education'][1],
            ),
            'work_experience' => array(
                $_POST['work_exp'][0],
                $_POST['work_exp'][1],
            ),
            'fields_interest' => array(
                $_POST['fields_int'][0],
                $_POST['fields_int'][1],
            ),
            'year_birth' => array(
                $_POST['year_birth'][0],
                $_POST['year_birth'][1],
            ),
            'spoken_languages' => array(
                $_POST['spoken'][0],
                $_POST['spoken'][1],
            ),
            'key_competencies' => array(
                $_POST['key_comp'][0],
                $_POST['key_comp'][1],
            ),
            'work_type' => array(
                $_POST['work_type'][0],
                $_POST['work_type'][1],
            ),
            'profile_last_modified' => array(
                current_time('mysql'),
            ),
        );

        // delete files
        if (isset($_POST['delete_photo'])) {
            $photo_id = $this->member->profile_photo;
            $profile_photo = get_attached_file($photo_id);
            unlink($profile_photo);
            update_user_meta($this->member->ID, "profile_photo", null);
        }

        if (isset($_POST['delete_cv'])) {
            $cv_id = $this->member->cv;
            $cv = get_attached_file($cv_id);
            unlink($cv);
            update_user_meta($this->member->ID, "cv", null);
        }

        // upload cv
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
            $cv = $_FILES['cv'];

            // Use the wordpress function to upload
            $cv = media_handle_upload('cv', 0);
            // Error checking using WP functions
            if (is_wp_error($cv)) {
                $errors[] = $cv->get_error_message();
            } else {
                update_user_meta($this->member->ID, "cv", $cv);
            }
            $user_meta['cv'] = array(
                $cv,
                $_POST['cv_priv'],
            );
        }

        // upload profile photo
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $profile_photo = $_FILES['profile_photo'];

            // Use the wordpress function to upload
            $profile_photo = media_handle_upload('profile_photo', 0);
            // Error checking using WP functions
            if (is_wp_error($profile_photo)) {
                $errors[] = $profile_photo->get_error_message();
            } else {
                update_user_meta($this->member->ID, "profile_photo", $profile_photo);
            }
            $user_meta['profile_photo'] = array(
                $profile_photo,
                $_POST['profile_photo_priv'],
            );
        }


        foreach ($user_meta as $key => $meta) {
            $priv = $key . "_priv";
            update_user_meta($this->member->ID, $key, $meta[0]);
            update_user_meta($this->member->ID, $priv, $meta[1]);
        }

        return $errors;
    }
}

$Member = new Member();