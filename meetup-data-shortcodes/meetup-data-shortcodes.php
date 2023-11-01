<?php
/*
Plugin Name: Meetup Data Shortcodes
Description: Displays meetup.com information
Version: 1.0
Author: Juan
*/

namespace MeetupDataShortcodes;


class MeetupDataShortcodes {
	protected $apiHost = 'https://api.meetup.com/';
	protected $apiKey = null;
	protected $albumId = null;
	protected $urlName = null;
	protected $album = null;
	protected $eventId = null;
	protected $table = null;

	public function __construct() {
		global $wpdb;
		$this->apiKey  = get_option( 'meetup_options' )['api_key'];
		$this->urlName = get_option( 'meetup_options' )['group_url'];
		$this->table   = $wpdb->prefix . 'meetup_groups';
		// Install database
		register_activation_hook( __FILE__, [ $this, 'install' ] );

		// Admin Page
		include_once( plugin_dir_path( __FILE__ ) . 'Admin.php' );

		// register shortcodes: [meetup_photo_album] [meetup_group_stats]
		add_shortcode( 'meetup_photo_album', [ $this, 'meetupPhotoAlbum' ] );
		add_shortcode( 'meetup_group_stats', [ $this, 'meetupGroupStats' ] );

		// register styles and scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );

		// register ajax calls
		add_action( 'wp_ajax_get_photo_album', [ $this, 'getPhotoAlbum' ] );
		add_action( 'wp_ajax_nopriv_get_photo_album', [ $this, 'getPhotoAlbum' ] );


		// daily event
		add_action( 'meetup_daily_event', array( $this, 'groupDetails' ) );
	}


	public function enqueueScripts() {
		wp_enqueue_style( 'meetup-photos-css', plugins_url( 'styles.css', __FILE__ ) );
		wp_enqueue_script( 'jquery-lazy', plugins_url( 'node_modules/jquery-lazy/jquery.lazy.js', __FILE__ ),
			array( 'jquery' ), '', true );
		wp_enqueue_script( 'meetup-photos-js', plugins_url( 'script.js', __FILE__ ), array( 'jquery-lazy' ), '', true );
		// Now we can localize the script with our data.
		wp_localize_script( 'meetup-photos-js', 'WPURLS', array( 'siteurl' => get_option( 'siteurl' ) ) );
	}

	public function meetupPhotoAlbum( $atts ) {
		$atts          = shortcode_atts( array(
			'group_url' => $this->urlName,
			'id'        => '',
			'event_id'  => ''
		), $atts );
		$this->urlName = $atts['group_url'];
		$this->albumId = $atts['id'];
		$this->eventId = $atts['event_id'];

		return $this->getPhotoAlbum();
	}

	public function meetupGroupStats( $atts ) {
		global $wpdb;
		$group       = get_option( 'meetup_options' )['group_url'];
		$group_stats = $wpdb->get_results( $wpdb->prepare( "SELECT * from $this->table where group_name = %s",
			$group ) );
		ob_start();
		include( 'group-stats.tpl.php' );
		$html = ob_get_clean();

		return $html;
	}

	function getPhotoAlbum() {
		$api_url = "{$this->apiHost}{$this->urlName}/photo_albums/{$this->albumId}/photos?key={$this->apiKey}&sign=true&photo-host=public";
		$photos  = array();
		if ( $this->eventId ) {
			$api_url = "{$this->apiHost}{$this->urlName}/events/{$this->eventId}/photos?key={$this->apiKey}&sign=true&photo-host=public";
		}
		$response = wp_remote_get( $api_url );
		$response = json_decode( $response['body'] );

		if ( isset( $response->errors ) ) {
			return '';
			// return $response->errors[0]->message;
		}
		$photo_count = $response[0]->photo_album->photo_count;
		$photos      = array_merge( $photos, $response );
		if ( $photo_count > 200 ) {
			$pages = floor( $photo_count / 200 );
			for ( $i = 1; $i <= $pages; $i ++ ) {
				$api_url_offset = "$api_url&offset=$i&page=200";
				$response       = wp_remote_get( $api_url_offset );
				$response       = json_decode( $response['body'] );
				$photos         = array_merge( $photos, $response );
			}
		}
		foreach ( $response as $key => $photo ) {
			if ( $photo->member->id == 0 ) {
				unset( $response[ $key ] );
			}
		}
		$this->album = $photos;
		usort( $this->album, array( $this, 'albumSort' ) );

		return $this->getPhotoAlbumHtml();
	}

	public function groupDetails( $api_key = null, $url_name = null ) {
		global $wpdb;
		$api_key  = $api_key ? $api_key : $this->apiKey;
		$url_name = $url_name ? $url_name : $this->urlName;
		$api_url  = "{$this->apiHost}{$url_name}?key={$api_key}&sign=true&photo-host=public&fields=past_event_count";
		$response = wp_remote_get( $api_url );
		if ( isset( $response->errors ) ) {
			return;
			// return $response->errors[0]->message;
		}
		$response    = json_decode( $response['body'] );
		$group_id    = $response->id;
		$members     = $response->members;
		$who         = $response->who;
		$created     = date( "Y-m-d h:i:s", substr( $response->created, 0, 10 ) );
		$past_events = $response->past_event_count;
		$wpdb->replace(
			$this->table,
			array(
				'group_id'    => $group_id,
				'group_name'  => $url_name,
				'members'     => $members,
				'past_events' => $past_events,
				'who'         => $who,
				'created'     => $created
			),
			array(
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s'
			)
		);
	}

	public function getPhotoAlbumHtml() {
		ob_start();
		include( 'photo-album.tpl.php' );
		$html = ob_get_clean();

		return $html;
	}

	public function albumSort( $a, $b ) {
		return $b->member->event_context->host - $a->member->event_context->host;

	}

	public function install() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $this->table (
                id int(9) UNSIGNED AUTO_INCREMENT,
                group_id varchar(50) UNIQUE,
                group_name varchar(50) UNIQUE,
                members int,
                past_events int,
                who varchar(50),
                created timestamp,
                PRIMARY KEY  (id)
	) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	public function activate() {
		wp_schedule_event( time(), 'daily', 'meetup_daily_event' );
	}

	public function deactivate() {
		wp_clear_scheduled_hook( 'meetup_daily_event' );
	}

}

$MeetupPhotoAlbums = new MeetupDataShortcodes();
register_activation_hook( __FILE__, [ $MeetupPhotoAlbums, 'activate' ] );
register_deactivation_hook( __FILE__, [ $MeetupPhotoAlbums, 'deactivate' ] );



