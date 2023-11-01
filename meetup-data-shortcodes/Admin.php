<?php

namespace MeetupDataShortcodes;

class Admin {
	function __construct() {
		// admin
		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
		//call register settings function
		add_action( 'admin_init', [ $this, 'adminInit' ] );
	}

	function adminMenu() {
		add_options_page( 'Meetup Photo Albums', 'Meetup Photo Albums', 'manage_options', 'meetup-photo-albums', [ $this, 'settingsPage' ] );
	}

	function adminInit() {
		global $MeetupPhotoAlbums;
		//register our settings
		register_setting( 'meetup_albums', 'meetup_options' );
		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'meetup_albums' ) {
			$MeetupPhotoAlbums->groupDetails( $_POST['meetup_options']['api_key'], $_POST['meetup_options']['group_url'] );
		}

	}

	function settingsPage() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		ob_start();
		include( 'admin.tpl.php' );
		$html = ob_get_clean();
		echo $html;
	}

}

$Admin = new Admin();
