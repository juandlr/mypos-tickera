<?php
/*
Plugin Name: Top Speed Golf User Comments
Description: Top Speed Golf User comments
Version: 1.0.0
Author: Juan
*/


/**
 * @version 1.0
 */
class TGS_User_Comments {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Static property to hold our singleton instance
	 * @since    1.0
	 */
	static $instance = FALSE;


	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 * constructor is private to force the use of getInstance() to make this a Singleton
	 * @since    1.0
	 */
	private function __construct() {
		// register scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		//register shortcodes
		add_shortcode( 'tgs_user_comments', array( $this, 'user_comments_shortcode' ) );
		add_shortcode( 'tgs_all_user_comments', array( $this, 'all_user_comments_shortcode' ) );
	}


	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 * @since    1.0
	 */
	public static function getInstance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Enqueue scripts
	 */
	function register_scripts() {
		wp_enqueue_script( 'tgs-user-comments-js', plugins_url( '/script.js', __FILE__ ), array(), NULL, TRUE );
		wp_enqueue_style( 'tgs-user-comments-css', plugins_url( '/style.css', __FILE__ ) );
	}


	/**
	 * user comments shortcode callback
	 */
	public function user_comments_shortcode( $atts ) {
		$a            = shortcode_atts( array(
			'x' => '10',
		), $atts );
		$current_user = wp_get_current_user();
		if ( ! 0 == $current_user->ID ) {
			$comments = get_comments( array(
				'author__in' => $current_user->ID,
				'number'     => $a['x'],
				'status'     => 'approve',
				'order'      => 'DESC',
			) );
			$html = "";
			foreach ( $comments as $comment ) {
				$post_title = get_the_title( $comment->comment_post_ID );
				$comment_date = get_comment_date('M j, Y (g:ia)', $comment->comment_ID);
				$html .= "<div class='user-comment'>";
				$html .= "<h6>$post_title</h6>";
				$html .= "<div class='user-comment-body'><span>{$comment_date}</span> | ";
				$html .= "<span>";
				$html .= get_comment_excerpt( $comment->comment_ID );
				$html .= "</span> | ";
				$comment_url = get_comment_link( $comment->comment_ID );
				$html .= "<a href='$comment_url'>View Comment</a> <br />";
				if ( $replies = $this->get_comment_replies( $comment ) ) {
					remove_filter('get_comment_author', 'je_change_comment_author_name', 2);
					$reply_author = get_comment_author( $replies[0]->comment_ID );
					$reply_date =  get_comment_date('M j, Y (g:ia)', $replies[0]->comment_ID);
					$reply_link = get_comment_link($replies[0]->comment_ID);
					$html .= "<i class='fa fa-long-arrow-right' aria-hidden='true'></i> <div class='comment-by'>Replied by {$reply_author} on $reply_date | <a href='$reply_link'>View Reply </a>";
					$html .= "</div></div>";
				}
				else {
					$html .= "<span class='tgs-soon'>No replies</span></div>";
				}
				$html .= "</div>";
			}
			return $html;
		}
	}


	/**
	 * all user comments shortcode callback
	 */
	public function all_user_comments_shortcode( $atts ) {
		$current_user = wp_get_current_user();
		if ( ! 0 == $current_user->ID ) {
			define( 'DEFAULT_COMMENTS_PER_PAGE', 20 );

			$page = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
			$limit          = DEFAULT_COMMENTS_PER_PAGE;
			$offset         = ( $page * $limit ) - $limit;
			$param          = array(
				'status'    => 'approve',
				'offset'    => $offset,
				'number'    => $limit,
				'author__in' => array( $current_user->ID ),
				'order'     => 'DESC',
			);
			$total_comments = get_comments( array(
				'orderby'   => 'post_date',
				'order'     => 'DESC',
				'author__in' => array( $current_user->ID ),
				'status'    => 'approve',
				'parent'    => 0,
			) );

			$pages    = ceil( count( $total_comments ) / DEFAULT_COMMENTS_PER_PAGE );
			$comments = get_comments( $param );
			$html = "";
			foreach ( $comments as $comment ) {
				$post_title = get_the_title( $comment->comment_post_ID );
				$comment_date = get_comment_date('M j, Y (g:ia)', $comment->comment_ID);
				$html .= "<div class='user-comment'>";
				$html .= "<h6>$post_title</h6>";
				$html .= "<div class='user-comment-body'><span>{$comment_date}</span> | ";
				$html .= "<span>";
				$html .= get_comment_excerpt( $comment->comment_ID );
				$html .= "</span> | ";
				$comment_url = get_comment_link( $comment->comment_ID );
				$html .= "<a href='$comment_url'>View Comment</a> <br />";
				if ( $replies = $this->get_comment_replies( $comment ) ) {
					remove_filter('get_comment_author', 'je_change_comment_author_name', 2);
					$reply_author = get_comment_author( $replies[0]->comment_ID );
					$reply_date =  get_comment_date('M j, Y (g:ia)', $replies[0]->comment_ID);
					$reply_link = get_comment_link($replies[0]->comment_ID);
					$html .= "<i class='fa fa-long-arrow-right' aria-hidden='true'></i> <div class='comment-by'>Replied by {$reply_author} on $reply_date | <a href='$reply_link'>View Reply <i class='fa fa-long-arrow-right' aria-hidden='true'></i></a>";
					$html .= "</div></div>";
				}
				else {
					$html .= "<span class='tgs-soon'>No replies</span></div>";
				}
				$html .= "</div>";
			}
			return $html;

			$args = array(
				'base'      => @add_query_arg( 'page', '%#%' ),
				'format'    => '?page=%#%',
				'total'     => $pages,
				'current'   => $page,
				'show_all'  => FALSE,
				'end_size'  => 1,
				'mid_size'  => 2,
				'prev_next' => TRUE,
				'prev_text' => __( 'Previous' ),
				'next_text' => __( 'Next' ),
				'type'      => 'plain'
			);

			// pagination
			echo paginate_links( $args );
		}
	}


	/**
	 * Retrieves all of the replies for the given comment
	 * @param    obj $comment object
	 * @return    array                    The array of replies
	 * @since    1.0
	 */
	private function get_comment_replies( $comment = FALSE ) {
		global $wpdb;
		$author_ids = get_option( 'cnrt_ids' );
		$author_ids = explode( ',', $author_ids );
		$replies    = $wpdb->get_results( $wpdb->prepare( "SELECT comment_ID, user_id, comment_author_email, comment_post_ID FROM $wpdb->comments WHERE comment_parent = %d", $comment->comment_ID ) );
		/*foreach ( $replies as $key => $reply ) {
			$author = get_userdata( $reply->user_id );
			if ( ! $author ) {
				unset( $replies[ $key ] );
				continue;
			}
			$has_elevated_privileges = array_intersect( $author->roles, array( 'editor', 'administrator' ) );
			$comment_is_by_authors   = in_array( $author->ID, $author_ids );
			if ( $has_elevated_privileges || $comment_is_by_authors ) {
				$replies[ $key ]->user_login = $author->data->user_login;
			}
		}
		$replies = array_values( $replies );*/
		if ( ! empty( $replies ) ) {
			return $replies;
		}
		return FALSE;
	}
}


/**
 * Instantiates the plugin using the plugins_loaded hook and the
 * Singleton Pattern.
 */
function tgs_user_comments() {
	TGS_User_Comments::getInstance();
}
add_action( 'plugins_loaded', 'tgs_user_comments' );

function tgs_user_comments_dependencies() {
	if (!is_plugin_active( 'custom-comments-not-replied-to/custom-comments-not-replied-to.php' ) ) {
		// Deactivate the plugin
		deactivate_plugins(__FILE__);
		// Throw an error in the wordpress admin console
		$error_message = 'This plugin requires custom comments not replied to plugin to be active!';
		wp_die($error_message);
	}

}
register_activation_hook(__FILE__, 'tgs_user_comments_dependencies');

