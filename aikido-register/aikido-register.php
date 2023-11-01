<?php

/*
 * Plugin Name: Aikido Register Form
 * Description: Registration form for aikidoshinjukai.com
 * Version: 1.0
 * Author: Juan
 * Author URI: http://admin123.co
 */
defined('ABSPATH') or die('No script kiddies please!');
require_once(ABSPATH . 'wp-admin/includes/file.php');
define("PROFILE_PAGE" , 297);
define("REGISTRATION_PAGE", 294);


add_action('wp_enqueue_scripts', 'aikido_register_scripts');
function aikido_register_scripts()
{
    wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
    wp_enqueue_style('aikido-register-css', plugins_url('aikido-register.css', __FILE__));
    wp_enqueue_script('aikido-register-js', plugins_url('aikido-register.js', __FILE__), array(), '', TRUE);
    wp_enqueue_style('inconsolata', 'https://fonts.googleapis.com/css?family=Inconsolata:400,700');
}


// hide user bar for non admin users
add_action('set_current_user', 'hide_admin_bar');
function hide_admin_bar()
{
    if (!current_user_can('edit_posts')) {
        show_admin_bar(false);
    }
}


// register form shortcode
add_shortcode('aikido-register-form', 'aikido_register_callback');
function aikido_register_callback()
{
    // only show the registration form to non-logged-in members
    if (!is_user_logged_in()) {
        // check to make sure user registration is enabled
        $registration_enabled = get_option('users_can_register');

        // only show the registration form if allowed
        if ($registration_enabled) {
            // now we put all of the HTML for the form into a PHP string
            $form = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/aikido-form.php';
            include($form);
            $form_html = ob_get_clean();
            return $form_html;
        }
    }
}

// user login form shortcode
add_shortcode('aikido-login-form', 'aikido_login_form');
function aikido_login_form()
{
    if (!is_user_logged_in()) {
        // only show the registration form if allowed
        // now we put all of the HTML for the form into a PHP string
        $form = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/aikido-login-form.php';
        include($form);
        $form_html = ob_get_clean();
        return $form_html;
    }
}

// user profile display shortcode
add_shortcode('aikido-profile', 'aikido_profile');
function aikido_profile()
{
        if (is_user_logged_in()) {
            global $user, $user_meta;
            $user = wp_get_current_user();
            $user_meta = get_user_meta($user->ID);
            unset($user_meta['session_tokens']);
            $profile = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/profile.php';
            include($profile);
            $profile_html = ob_get_clean();
            return $profile_html;
        }
}

function aikido_register_form_callback()
{
    if (isset($_POST['aikido_register_submitted']) && wp_verify_nonce($_POST['aikido_register_nonce'], 'aikido_register_nonce')) {
        global $success_message;
        global $errors;

        $errors = array();
        $thin_attire = ['Thin Gi, Size 100 – 130 ($48.20)', 'Thin Gi, Size 140 – 170 ($53.50)',
            'Thin Gi Size 180 ($58.90)', 'Thin Gi Size 190 and 200 ($64.20)'];
        $thick_attire = ['Thick Gi, Size 150 – 170 ($85.60)', 'Thick Gi Size 180 and 190 ($96.30)',
            'Thick Gi Size 200 ($101.70)'];
        $packages = ['Once a week - 1 Session @ $20.30', 'Once a week - 12 Sessions (1 term) @ $235.40',
            'All sessions package - 1 Session @ $28.90', 'All sessions package - 12 Sessions (1 term) @ $342.40'];
        $insert_user['user_login'] = $_POST['user_login'] ? sanitize_text_field($_POST['user_login']) : '';
        $insert_user['first_name'] = $_POST['firstname'] ? sanitize_text_field($_POST['firstname']) : '';
        $insert_user['last_name'] = $_POST['lastname'] ? sanitize_text_field($_POST['lastname']) : '';
        $insert_user['user_email'] = $_POST['user_email'] ? sanitize_email($_POST['user_email']) : '';
        $insert_user['user_pass'] = $_POST['pass'] ? sanitize_text_field($_POST['pass']) : '';
        $insert_user_meta['chinesename'] = $_POST['chinesename'] ? sanitize_title($_POST['chinesename']) : '';
        $insert_user_meta['gender'] = $_POST['gender'] ? sanitize_text_field($_POST['gender']) : '';
        $insert_user_meta['race'] = $_POST['race'] ? sanitize_text_field($_POST['race']) : '';
        $insert_user_meta['block'] = $_POST['block'] ? sanitize_text_field($_POST['block']) : '';
        $insert_user_meta['address2'] = $_POST['address2'] ? sanitize_text_field($_POST['address2']) : '';
        $insert_user_meta['building'] = $_POST['building'] ? sanitize_text_field($_POST['building']) : '';
        $insert_user_meta['address1'] = $_POST['address1'] ? sanitize_text_field($_POST['address1']) : '';
        $insert_user_meta['day'] = $_POST['aik_day'] ? sanitize_text_field($_POST['aik_day']) : '';
        $insert_user_meta['month'] = $_POST['aik_month'] ? sanitize_text_field($_POST['aik_month']) : '';
        $insert_user_meta['year'] = $_POST['aik_year'] ? sanitize_text_field($_POST['aik_year']) : '';
        $insert_user_meta['country'] = $_POST['country'] ? sanitize_text_field($_POST['country']) : '';
        $insert_user_meta['postcode'] = $_POST['postcode'] ? sanitize_text_field($_POST['postcode']) : '';
        $insert_user_meta['mobile'] = $_POST['mobile'] ? sanitize_text_field($_POST['mobile']) : '';
        $insert_user_meta['telephone'] = $_POST['telephone'] ? sanitize_text_field($_POST['telephone']) : '';
        $insert_user_meta['officephone'] = $_POST['officephone'] ? sanitize_text_field($_POST['officephone']) : '';
        $insert_user_meta['occupation'] = $_POST['occupation'] ? sanitize_text_field($_POST['occupation']) : '';
        $insert_user_meta['company'] = $_POST['company'] ? sanitize_text_field($_POST['company']) : '';
        $insert_user_meta['prev_exp'] = $_POST['prev_exp'] ? sanitize_text_field($_POST['prev_exp']) : '';
        $insert_user_meta['ex_student'] = isset($_POST['ex_student']) ? sanitize_text_field($_POST['ex_student']) : '';
        $insert_user_meta['student_yes'] = $_POST['student_yes'] ? sanitize_text_field($_POST['student_yes']) : '';
        $insert_user_meta['past_injuries'] = $_POST['past_injuries'] ? sanitize_text_field($_POST['past_injuries']) : '';
        $insert_user_meta['been_in_court'] = isset($_POST['been_in_court']) ? sanitize_text_field($_POST['been_in_court']) : '';
        $insert_user_meta['court_yes'] = $_POST['court_yes'] ? sanitize_text_field($_POST['court_yes']) : '';
        $insert_user_meta['pdpa'] = isset($_POST['pdpa']) ? sanitize_text_field($_POST['pdpa']) : '';
        $insert_user_meta['pdpa_date'] = $_POST['pdpa_date'] ? sanitize_text_field($_POST['pdpa_date']) : '';
        $insert_user_meta['thin_attire'] = $_POST['thin_attire'] ? $thin_attire[sanitize_text_field($_POST['thin_attire'])] : $thin_attire[0];
        $insert_user_meta['thick_attire'] = $_POST['thick_attire'] ? $thick_attire[sanitize_text_field($_POST['thick_attire'])] : $thin_attire[0];
        $insert_user_meta['membership_package'] = $_POST['membership_package'] ? $packages[sanitize_text_field($_POST['membership_package'])] : $packages[0];
        $insert_user_meta['total_paid'] = $_POST['total'] ? sanitize_text_field($_POST['total']) : '';

        // validate the form
        if (username_exists($insert_user['user_login'])) {
            $errors[] = 'Username unavailable';
        }
        if (!validate_username($insert_user['user_login']) || $insert_user['user_login'] == '') {
            $errors[] = 'Username invalid';
        }
        if (!is_email($insert_user['user_email'])) {
            $errors[] = 'Invalid Email';
        }
        if ($insert_user['user_pass'] == '') {
            $errors[] = 'please enter a password';
        }
        if ($insert_user['user_pass'] != $_POST['pass_confirm']) {
            // passwords do not match
            $errors[] = 'Password mismatch';
        }


        // only create the user in if there are no errors
        if (empty($errors)) {
            $new_user_id = wp_insert_user($insert_user);
            if (!is_wp_error($new_user_id)) {
                foreach ($insert_user_meta as $key => $value) {
                    add_user_meta($new_user_id, $key, $value);
                }
                // upload user image
                if ($_FILES["graphic"]["error"] == 0) {
                    $upload_overrides = array(
                        'test_form' => false
                    );
                    $file = $_FILES['graphic'];
                    $file = wp_handle_upload($file, $upload_overrides);
                    if ($file && !isset($file['error'])) {
                        add_user_meta($new_user_id, 'profile_image', $file['url']);
                    }
                }

                // send an email to the admin alerting them of the registration
                wp_new_user_notification($new_user_id);


                // after registration send user to paypal for payment
                $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
                $paypal_user = 'juan-facilitator@admin123.co';
                $get_paypal['cmd'] = '_xclick';
                $get_paypal['business'] = $paypal_user;
                $get_paypal['item_name'] = 'Aikido Membership';
                $get_paypal['amount'] = $insert_user_meta['total_paid'];
                $get_paypal['currency_code'] = 'USD';
                $get_paypal['return'] =  get_permalink(PROFILE_PAGE);
                $get_paypal['cancel_return'] = get_bloginfo('url');
                $get_paypal['notify_url'] = plugins_url( 'ipn.php', __FILE__ );
                $get_paypal['custom'] = $new_user_id;
                $query_string = http_build_query($get_paypal);
                header('location:' . $paypal_url . $query_string);
                exit;
            }
            if (is_wp_error($new_user_id)) {
                $errors = $new_user_id->errors;
            }
        }
    }
}

function aikido_login_form_callback()
{
    global $errors;
    if (isset($_POST['aikido_user_login']) && wp_verify_nonce($_POST['aikido_login_nonce'], 'aikido_login')) {
        $credentials = array();
        $credentials['user_login'] = $_POST['aikido_user_login'];
        $credentials['user_password'] = $_POST['aikido_user_pass'];
        $credentials['remember'] = false;
        $user = wp_signon($credentials, false);
        if (is_wp_error($user)) {
            $errors = $user->errors;
        } else {
            wp_redirect(PROFILE_PAGE);
            exit;
        }
    }
}

add_action('init', 'aikido_register_init', 20);
function aikido_register_init()
{
    aikido_register_form_callback();
}

// logs a member in after submitting a form
add_action('init', 'aikido_login_member');
function aikido_login_member()
{
    aikido_login_form_callback();
}
