<?php
/*
Plugin Name: infusionsoft custom
Description: infusionsoft retrieval form shortcode
Version: 1.0
Author: Juan
Author URI: http://admin123.co
*/

/**
 * Check if the SDK is loaded
 */
function infusionsoft_custom_missing_sdk() {
	if ( ! is_plugin_active( 'infusionsoft-sdk/infusionsoft-sdk.php' ) ) {
		echo "<div class=\"error\"><p><strong><em>Infusionsoft custom plugin</em> requires the <em>Infusionsoft SDK</em> plugin. Please install and activate the <em>Infusionsoft SDK</em> plugin.</strong></p></div>";
	}
}

add_action( 'admin_notices', 'infusionsoft_custom_missing_sdk' );

function infusionsoft_scripts() {
	wp_enqueue_style( 'infusionsoftcss', plugins_url( 'styles.css', __FILE__ ) );
	wp_enqueue_script( 'infusionsoftjs', plugins_url( 'script.js', __FILE__ ), array(), '', TRUE );
	// Now we can localize the script with our data.
	wp_localize_script( 'infusionsoftjs', 'WPURLS', array( 'siteurl' => get_option( 'siteurl' ) ) );
}

add_action( 'wp_enqueue_scripts', 'infusionsoft_scripts' );

// register a new shortcode: infusionsoft]
add_shortcode( 'pbn verify', 'infusionsoft_callback' );
// The callback function that will replace [book]
function infusionsoft_callback() {
// now we put all of the HTML for the form into a PHP string
	$content = <<<EOT
        <div id="infusionsoft-custom">
          <form id="infusionsoft-contact" action="">
            <input type="text" placeholder="member number" id="memberid" name="memberid"><br />
            <input type="submit" value="Verify">
          </form>
			<div id='result'>
            </div>
        </div>
EOT;

	return $content;
}

function infusionsoft_form() {
	if ( isset( $_GET['memberid'] ) ) {
		try {
			$contact = new Infusionsoft_Contact( $_GET['memberid'] );
		} catch ( Exception $e ) {
			$html = "<div class='error'>Record not found</div>";
			echo $html;
			die();
		}
		if ( $contact->Id ) {

			$groups   = explode( ",", $contact->Groups );
			$a_groups = array();
			foreach ( $groups as $group_id ) {
				$a_groups[] = new Infusionsoft_ContactGroup( $group_id );
			}
			$s_groups = '';
			foreach ( $a_groups as $group ) {
				if ( stripos( $group->GroupName, 'status' ) !== FALSE ) {
					$s_groups .= $group->GroupName . '<br />';
				}
			}
			if (!$s_groups) {
				$html = "<div class='error'>Invalid User</div>";
				echo $html;
				die();
			}
			$html = <<<EOT
			<div id='infusions-entry'>
				<b>Member Number</b> : {$contact->Id} <br />
				<b>First Name</b> :  {$contact->FirstName} <br />
				<b>Last Name </b> :  {$contact->LastName} <br />
				{$s_groups}
			</div>
EOT;
			echo $html;
		} else {
			$html = "<div class='error'>Record not found</div>";
			echo $html;
		}
	}
	die();
}

add_action( 'wp_ajax_infusionsoft_form', 'infusionsoft_form' );
add_action( 'wp_ajax_nopriv_infusionsoft_form', 'infusionsoft_form' );

