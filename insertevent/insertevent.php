<?php

/*
 * Plugin Name: insert event
 * Description: insert event
 * Version: 1.0
 * Author: Juan
 * Author URI: http://admin123.co
 */
defined('ABSPATH') or die('No script kiddies please!');
require_once (ABSPATH . 'wp-admin/includes/image.php');

function insertevent_scripts()
{
    wp_enqueue_style('inserteventtcss', plugins_url('insertevent.css', __FILE__));
    wp_enqueue_script('inserteventjs', plugins_url('insertevent.js', __FILE__), array(), '', TRUE);
}
add_action('wp_enqueue_scripts', 'insertevent_scripts');

// register a new shortcode
add_shortcode('insertevent-form', 'insertevent_callback');

function insertevent_callback()
{
    // now we put all of the HTML for the form into a PHP string
    $form = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/insertevent-form.php';
    include ($form);
    $form_html = ob_get_clean();
    return $form_html;
}

function insertevent_form()
{
    if (isset($_POST['event_submitted']) && wp_verify_nonce($_POST['insertevent-nonce'], 'insertevent-nonce')) {
        global $error_message;
        global $success_message;
        $human = $_POST['message_human'];
        if ($human == 2) {
            $event_name = $_POST['event_name'] ? sanitize_user($_POST['event_name']) : '';
            $event_email = $_POST['event_email'] ? sanitize_email($_POST['event_email']) : '';
            $event_title = $_POST['event_title'] ? sanitize_title($_POST['event_title']) : '';
            $event_body = $_POST['event_body'] ? sanitize_text_field($_POST['event_body']) : '';
            $start_date = $_POST['start_date'] ? sanitize_text_field($_POST['start_date']) : '';
            $end_date = $_POST['end_date'] ? sanitize_text_field($_POST['end_date']) : '';
            $start_time = $_POST['start_time'] ? sanitize_text_field($_POST['start_time']) : '';
            $end_time = $_POST['end_time'] ? sanitize_text_field($_POST['end_time']) : '';
            $event_address = $_POST['event_address'] ? sanitize_text_field($_POST['event_address']) : '';
            $event_phone = $_POST['event_phone'] ? sanitize_text_field($_POST['event_phone']) : '';
            $event_tags = $_POST['event_tags'] ? sanitize_text_field($_POST['event_tags']) : '';
            $event_video = $_POST['event_video'] ?  sanitize_text_field($_POST['event_video']) : '';
            
            if (isset($_FILES['event_image'])) {
                $upload_overrides = array(
                    'test_form' => false
                );
                $file = $_FILES['event_image'];
            }
            
            // create post object
            $new_event = array(
                'post_title' => $event_title,
                'post_content' => $event_body,
                'post_status' => 'pending',
                'post_author' => $event_name,
                'tags_input' => $event_tags,
                'post_date' => date('Y-m-d H:i:s'),
                'post_type' => 'event'
            );
            
            // Insert the post into the database
            $post_id = wp_insert_post($new_event);
            if ($post_id) {
                // set category 
                $categories = wp_set_object_terms($post_id,  'all-events', 'event-category', true);
                // upload event image
                $file = wp_handle_upload($file, $upload_overrides);
                if ($file && ! isset($file['error'])) {
                    $file_name = $file['file'];
                    $file_type = $file['type'];
                    $attachment = array(
                        'post_mime_type' => $file_type,
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    
                    $attach_id = wp_insert_attachment($attachment, $file_name, $post_id);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file_name);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    set_post_thumbnail($post_id, $attach_id);
                }
                update_post_meta($post_id, 'imic_event_start_dt', $start_date);
                update_post_meta($post_id, 'imic_event_end_dt', $end_date);
                update_post_meta($post_id, 'imic_event_start_tm', $start_time);
                update_post_meta($post_id, 'imic_event_end_tm', $end_time);
                update_post_meta($post_id, 'imic_event_address', $event_address);
                update_post_meta($post_id, 'imic_event_contact', $event_phone);
                update_post_meta($post_id, 'event_video', $event_video);
                $success_message = 'new event is in review and will be published when approved';
            } else {
                $error_message = 'the event could not be created';
            }
        } else {
            $error_message = 'the code entered is invalid';
        }
    }
}

add_action('init', 'insertevent_init', 20);
function insertevent_init()
{
    insertevent_form();
}

add_action("admin_init", "admin_init");
function admin_init()
{
    add_meta_box('event_video', 'Event Video', 'event_video', 'event');
}

// The Event Video
function event_video()
{
    global $post;
    
    // Get the location data if its already been entered
    $event_video = get_post_meta($post->ID, 'event_video', true);
    
    // Echo out the field
    echo '<input type="text" size="100" name="event_video" value="' . $event_video . '" class="" />';
}

add_action('save_post', 'add_event_video', 10, 2);
function add_event_video($post_id, $post)
{
    // Check post type for movie reviews
    if ($post->post_type == 'event') {
        // Store data in post meta table if present in post data
        if (isset($_POST['event_video']) && $_POST['event_video'] != '') {
            $event_video = $_POST['event_video'] ?  sanitize_text_field($_POST['event_video']) : '';
            update_post_meta($post_id, 'event_video', $event_video);
        }
    }
}

