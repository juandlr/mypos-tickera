<?php
/*
Plugin Name: ND Google Link
Description: Retrieves values from google spreadsheet
Version: 1.0
Author: Juan
Author URI: http://admin123.co
*/

require_once(plugin_dir_path( __FILE__ )  . '/google-spreadsheet-to-array.php');


// add google spreadsheet values
function nd_google_link_content_filter($content) {
	$a_sheet = google_spreadsheet_to_array_v3('https://docs.google.com/spreadsheets/d/1ivMayNRHHyZyquInveCdiAht2BsRFl5pG9HKS_oZffU/pubhtml?gid=0&single=true');
	$pattern = '/###(.+?)###/';
	$a_replacement = array();
	preg_match_all($pattern, $content, $matches);
	foreach ($matches[1] as $key => $cell) {
		$a_cell = explode('_', $cell);
		$y = $a_cell[0];
		$x = $a_cell[1];
		array_push($a_replacement, $a_sheet[$y][$x]);
	}
	$content = str_replace($matches[0], $a_replacement, $content);
	return $content;
}

add_filter( 'the_content', 'nd_google_link_content_filter' );


