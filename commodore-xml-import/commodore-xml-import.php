<?php

/*
Plugin Name: Commodore xml import
Description: Periodically import vehicles from xml api
Version: 1.0
Author: Juan
Author URI: http://admin123.co
*/

include_once(ABSPATH . 'wp-admin/includes/image.php');

class  Xml_Car_Import
{
    function __construct()
    {
        add_action('xml_import_event', array($this, 'import_vehicles_xml'));
    }

    static function activation()
    {
        wp_schedule_event(time(), 'hourly', 'xml_import_event');
    }


    static function deactivation()
    {
        wp_clear_scheduled_hook('xml_import_event');
    }

    function import_vehicles_xml()
    {
        $i = 0; // one vehicle per run

        // first we get all vehicles
        $tokens['gympie'] = '6CAAED225121EA34'; #Green RV Gympie
        $tokens['melbourne'] = 'CF31C831D8B1D5F5'; #Green RV Melbourne
        $tokens['clontarf'] = 'A964CE9BD932C5FE'; #Green RV Clontarf
        $vehicles = array();
        foreach ($tokens as $value) {
            $data['token'] = $value;
            $xml_vehicles = wp_remote_get('http://service.ultimate.net.au/ubs_mtaq/member/upload/ubs_mtaq_get_stocks.asp',
                array('body' => $data, 'timeout' => 60));
            $xml_vehicles = new SimpleXMLElement($xml_vehicles['body']);
            foreach ($xml_vehicles->vehicles->vehicle as $vehicle) {
                $attrs = current($vehicle->attributes());
                $vehicles[] = $attrs;
                $stock_numbers[] = $attrs['stock-number'];
            }
        }

        // delete vehicles not on remote server
        $local_vehicles = $this->get_meta_values('stock_number');
        $local_vehicles = array_filter($local_vehicles);
        $remove_vehicles = array_diff($local_vehicles, $stock_numbers);
        foreach ($remove_vehicles as $vehicle) {
            $vehicle_id = $this->get_post_id_by_meta_key_and_value('stock_number', $vehicle);
            wp_delete_post($vehicle_id);
            $this->delete_associated_media($vehicle_id);
        }


        foreach ($vehicles as $vehicle) {
            $data['vehicle_id'] = $vehicle['id'];
            $data['pic_limit'] = 20;

            // lets check for duplicate vehicle
            $args = array(
                'post_type' => 'listings',
                'meta_query' => array(
                    array(
                        'key' => 'stock_number',
                        'value' => $vehicle['stock-number']
                    )
                ),
                'fields' => 'ids'
            );
            // perform the query
            $vid_query = new WP_Query($args);
            $vid_ids = $vid_query->posts;
            // do something if the meta-key-value-pair exists in another post
            if (!empty($vid_ids)) {
                continue;
            }


            // we get individual vehicle details
            $response = wp_remote_get('http://service.ultimate.net.au/ubs_mtaq/member/upload/ubs_mtaq_get_vehicle.asp',
                array('body' => $data, 'timeout' => 60));
            $vehicle = new SimpleXMLElement($response['body']);
            foreach ($vehicle->vehicles->vehicle as $vehicle) {
                $attrs = current($vehicle);
                $model = current($vehicle->model->attributes());
                $dates = current($vehicle->dates->attributes());
                $accessories = current($vehicle->accessories);
                $s_accessories = '';
                if ($accessories) {
                    foreach ($accessories as $accessory) {
                        $accessory = current($accessory->attributes());
                        $s_accessories .= $accessory['desc'] . ',';
                    }
                }
                $s_accessories = rtrim($s_accessories, ',');
                $pictures = current($vehicle->pictures);


                $meta = [];
                $meta['stock_number'] = $attrs['stock-number'];
                $meta['title'] = 'hide';
                $meta['vin_number'] = $attrs['vin'];
                $meta['price'] = $attrs['asking-price'];
                $meta['ca-year'] = $dates['build-year'];
                $meta['mileage'] = $attrs['odometer'];
                $meta['exterior-color'] = $attrs['colour'];
                $meta['make'] = $model['make'];
                $meta['model'] = $model['model'];
                $meta['body'] = $model['body'];
                $meta['condition'] = $attrs['type'];
                $meta['additional_features'] = esc_attr($s_accessories);

                // create term if doesn't exist
                foreach ($meta as $key => $value) {
                    if (taxonomy_exists($key)) {
                        if ($value) {
                            $inserted_term = wp_insert_term($value, $key, array('slug' => $value));
                            $meta[$key] = sanitize_title($value);
                        }
                    }
                }


                // Create new vehicle post object
                $new_vehicle = array(
                    'post_title' => $attrs['type'] . ' ' . $dates['build-year'] . ' ' . $model['make'] . ' ' . $model['model'] . ' ' .
                        $model['variant'] . ' ' . $model['body'],
                    'post_type' => 'listings',
                    'post_content' => htmlspecialchars_decode($attrs['vehicle-desc']),
                    'post_status' => 'publish',
                    'post_author' => 1,
                );

                $post_id = wp_insert_post($new_vehicle);
                if ($post_id) {
                    // process images
                    $upload_dir = wp_upload_dir();
                    if (!file_exists($upload_dir['basedir'] . '/vehicles')) {
                        mkdir($upload_dir['basedir'] . '/vehicles', 0777, true);
                    }

                    if (!empty($pictures)) {
                        $j = 0;
                        foreach ($pictures as $picture) {
                            $picture = base64_decode($picture);
                            $image = imagecreatefromstring($picture);
                            $random = uniqid();
                            imagepng($image, ABSPATH . "wp-content/uploads/vehicles/{$random}.png");
                            $file_name = ABSPATH . "wp-content/uploads/vehicles/{$random}.png";
                            $wp_filetype = wp_check_filetype(basename($file_name), null);
                            $attachment = array(
                                'post_mime_type' => $wp_filetype['type'],
                                'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );

                            $attach_id = wp_insert_attachment($attachment, $file_name, $post_id);
                            $attach_data = wp_generate_attachment_metadata($attach_id, $file_name);
                            wp_update_attachment_metadata($attach_id, $attach_data);
                            $attached_terms = wp_set_object_terms($attach_id, array('inventory'), 'media_category',
                                true);
                            if ($j > 0) {
                                $meta['gallery'][] = $attach_id;
                            } else {
                                set_post_thumbnail($post_id, $attach_id);
                            }
                            $j++;
                        }
                    }
                    // fill meta fields
                    foreach ($meta as $key => $value) {
                        update_post_meta($post_id, $key, $value);
                    }
                }
            }
            if (++$i == 1) {
                break;
            }
        }

    }


    function seoUrl($string)
    {
        // lower case everything
        $string = strtolower($string);
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }


    function get_meta_values($key)
    {
        $jhf_values = array(''); //AN EMPTY ARRAY TO STORE THE VALUES GATHERED
        $the_query = new WP_Query('post_type=listings');//CHANGE TO CUSTOM POST TYPE IF REQUIRED
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $the_answer = get_post_meta($the_query->post->ID, $key,
                    true); //'TRUE' WILL RETURN ONLY ONE VALUE FOR EACH POST.
                $the_answer = trim($the_answer); //REMOVE UNWANTED WHITESPACE FROM BEGINNING AND END
                array_push($jhf_values, $the_answer); //ADD THE RESULT TO THE EMPTY ARRAY
            }
        }
        return $jhf_values;
    }

    /**
     * Get post id from meta key and value
     *
     * @param string $key
     * @param mixed $value
     * @return int|bool
     * @author David M&aring;rtensson <david.martensson@gmail.com>
     */
    function get_post_id_by_meta_key_and_value($key, $value)
    {
        global $wpdb;
        $meta = $wpdb->get_results("SELECT * FROM `" . $wpdb->postmeta . "` WHERE meta_key='" . $wpdb->escape($key) . "' AND meta_value='" . $wpdb->escape($value) . "'");
        if (is_array($meta) && !empty($meta) && isset($meta[0])) {
            $meta = $meta[0];
        }
        if (is_object($meta)) {
            return $meta->post_id;
        } else {
            return false;
        }
    }

    /**
     * delete media attached to post
     * @param string $id
     */
    function delete_associated_media($id)
    {
        $media = get_attached_media('image', $id);

        if (empty($media)) {
            return;
        }

        foreach ($media as $file) {
            wp_delete_attachment($file->ID);
        }
    }
}

$Xml_Car_Import = new Xml_Car_Import();
register_activation_hook(__FILE__, array('Xml_Car_Import', 'activation'));
register_deactivation_hook(__FILE__, array('Xml_Car_Import', 'deactivation'));