<?php
/*
Plugin Name: Custom Comments Not Replied To
Description: Top speed golf comments not replied to
Version: 1.0.0
Author: Juan
*/

if (!defined('CNRT_VERSION')) {
    define('CNRT_VERSION', '2.0.0');
} // end if

/**
 * @version 1.0
 */
class Custom_Comments_Not_Replied_To
{

    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/

    /**
     * Static property to hold our singleton instance
     *
     * @since    1.0
     */
    static $instance = false;

    /**
     * Lib URL
     *
     * @since    1.0
     */
    private $lib_url = '';


    /**
     * database table
     *
     * @since    2.0
     */
    private $table = '';


    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     * constructor is private to force the use of getInstance() to make this a Singleton
     *
     * @since    1.0
     */
    private function __construct()
    {

        global $wpdb;
        $this->lib_url = plugin_dir_url(__FILE__) . 'lib';
        $this->table = $wpdb->prefix . 'cnrt';


        // register scripts
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));


        // Load plugin textdomain
        add_action('init', array($this, 'plugin_textdomain'));
        add_action('init', array($this, 'no_reply_needed'));

        // add or remove the comment meta to comments on entry
        add_action('comment_post', array($this, 'add_missing_meta'));
        add_action('comment_post', array($this, 'remove_missing_meta'));

        // return just the missing replies in the comment table
        add_action('pre_get_comments', array($this, 'return_missing_list'));
        add_action('pre_get_comments', array($this, 'return_missing_list_hot'));

        // Add the 'Missing Reply' custom column
        add_filter('manage_edit-comments_columns', array($this, 'missing_reply_column'));
        add_filter('manage_comments_custom_column', array($this, 'missing_reply_display'), 10, 2);


        // Add settings field to discussion admin screen
        add_action('admin_init', array($this, 'register_settings'));

        // add 'Missing Reply' link in status row
        add_filter('comment_status_links', array($this, 'status_links'));

        // Add CSS to admin_head
        add_action('admin_head', array($this, 'admin_css'));

    }


    /**
     * If an instance exists, this returns it.  If not, it creates one and
     * retuns it.
     *
     * @since    1.0
     */

    public static function getInstance()
    {

        if (!self::$instance) {
            self::$instance = new self;
        } // end if

        return self::$instance;

    } // end getInstance

    /**
     * on plugin install create database tables
     */
    static function install()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'cnrt';
        $sql = "CREATE TABLE $table_name (
		id int NOT NULL AUTO_INCREMENT,
		user_id int NOT NULL,
		comment_id int NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * on plugin uninstall delete database tables
     */
    static function uninstall()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cnrt';
        $sql = "DROP TABLE IF_EXISTS $table_name";
        $wpdb->query($sql);
    }

    /**
     * Enqueue scripts
     */
    function register_scripts()
    {
        wp_enqueue_script('cnrt-js', plugins_url('/cnrt.js', __FILE__), array(), null, true);
    }

    /*--------------------------------------------*
     * Dependencies
     *--------------------------------------------*/

    /**
     * Loads the plugin text domain for translation
     *
     * @since    1.0
     */
    public function plugin_textdomain()
    {

        // Set filter for plugin's languages directory
        $lang_fir = dirname(plugin_basename(__FILE__)) . '/lang/';
        $lang_fir = apply_filters('cnrt_languages_directory', $lang_fir);

        // Traditional WordPress plugin locale filter
        $locale = apply_filters('plugin_locale', get_locale(), 'cnrt');
        $mofile = sprintf('%1$s-%2$s.mo', 'cnrt', $locale);

        // Setup paths to current locale file
        $mofile_local = $lang_fir . $mofile;
        $mofile_global = WP_LANG_DIR . '/cnrt/' . $mofile;

        if (file_exists($mofile_global)) {
            // Look in global /wp-content/languages/cnrt folder
            load_textdomain('cnrt', $mofile_global);
        } elseif (file_exists($mofile_local)) {
            // Look in local /wp-content/plugins/comments-not-replied-to/languages/ folder
            load_textdomain('cnrt', $mofile_local);
        } else {
            // Load the default language files
            load_plugin_textdomain('cnrt', false, $lang_fir);
        } // end if/else

    } // end plugin_textdomain


    /*--------------------------------------------*
     * Actions and Filters
     *--------------------------------------------*/

    /**
     * Adds a new column to the 'All Comments' page for indicating whether or not
     * the given comment has not received a reply from the post author.
     *
     * @param    array $columns The array of columns for the 'All Comments' page
     * @return    array                The array of columns to display
     *
     * @since    1.0
     */
    public function missing_reply_column($columns = array())
    {

        $columns['missing-reply'] = __('Comment Replies', 'cnrt');

        return $columns;

    }

    /**
     * Calls function for new page to the under the 'Comments' page for indicating whether or not
     * the given comment has not received a reply from the post author.
     *
     * @return    array                The array of columns to display
     *
     * @since    1.0
     */
    public function missing_reply_display($column_name = '', $comment_id = 0)
    {
        global $wpdb;

        // If we're looking at the 'Missing Reply' column...
        if ('missing-reply' !== trim($column_name))
            return;

        $comment = get_comment($comment_id);
        $replies = $this->get_comment_replies($comment);
        $comment_is_by_authors = $this->comment_is_by_authors($comment);
        $no_reply_needed = $wpdb->get_row("SELECT * FROM $this->table WHERE comment_id = $comment_id");
        $row_color = strtotime($comment->comment_date) < strtotime('-2 days') ? 'light-red-bg' : 'light-yellow-bg';
        // if the comment has been replied by any of the authors specified
        if ($replies) {
            $author = get_userdata($replies[0]->user_id);
            $avatar = get_avatar($replies[0]->user_id);
            $row_color = 'light-green-bg';
            $html = "<div class='$row_color'>";
            $html .= "$avatar <br />";
            $html .= $author->data->display_name . "<br />";
            $html .= "has replied";
            $html .= "</div>";
            // if the comment is by any of the specified authors or any with privileges
        } elseif ($comment_is_by_authors) {
            $avatar = get_avatar($comment_is_by_authors->data->ID);
            $row_color = 'light-green-bg';
            $html = "<div class='$row_color'>";
            $html .= "$avatar <br />";
            $html .= $comment_is_by_authors->data->display_name . "<br />";
            $html .= "made this comment";
            $html .= "</div>";
            // if the comment has been marked as no needing reply
        } elseif ($no_reply_needed) {
            $marked_by = get_userdata($no_reply_needed->user_id);
            $avatar = get_avatar($no_reply_needed->user_id);
            $row_color = 'light-green-bg';
            $html = "<div class='$row_color'>";
            $html .= "$avatar <br />";
            $html .= $marked_by->data->display_name . "<br />";
            $html .= "marked as no reply needed";
            $html .= "</div>";
        } else {
            $user_id = get_current_user_id();
            $html = "<div class='$row_color'>";
            $html .= "<a href='?comment_id_no_reply=$comment_id&marked_by=$user_id'>Mark as No Reply Needed</a>";
            $html .= "</div>";
        }
        echo $html;
    }


    /**
     * mark comment as no reply needed by current user
     */
    public function no_reply_needed()
    {
        global $wpdb;
        if (isset($_GET['comment_id_no_reply'])) {
            $user_id = $_GET['marked_by'];
            $comment_id = $_GET['comment_id_no_reply'];

            // check if comment exists already
            $comment_exists = $wpdb->get_row("SELECT * FROM $this->table WHERE comment_id = $comment_id");
            if (!$comment_exists) {
                $insert_row = $wpdb->insert(
                    $this->table,
                    array(
                        'user_id' => $user_id,
                        'comment_id' => $comment_id
                    ),
                    array(
                        '%d',
                        '%d'
                    )
                );
                delete_comment_meta($comment_id, '_cnrt_missing');
                if ($insert_row) {
                    header('location: ' . $_SERVER['PHP_SELF']);
                }
            }
        }
    }

    /**
     * Registers settings
     * @since    2.0
     */
    public function register_settings()
    {
        register_setting('discussion', 'cnrt_ids');
        add_settings_field(
            'cnrt_ids',
            'Author ids',
            array($this, 'cnrt_settings'),
            'discussion',
            'default',
            array('label_for' => 'cnrt-ids')
        );
    }


    /**
     * Register field for author ids
     * @since    2.0
     */
    public function cnrt_settings($args)
    {
        $value = get_option('cnrt_ids', '');
        echo "<input type='text' id='cnrt-ids' name='cnrt_ids' value='$value' /> <span>Enter comma separated ids</span>";
    }



    /*--------------------------------------------*
     * Helper Functions
     *--------------------------------------------*/
    /**
     * Retrieves all of the replies for the given comment belonging to author ids
     *
     * @param    int $comment_id The ID of the comment for which to retrieve replies.
     * @return    array                    The array of replies
     * @since    1.0
     */
    private function get_comment_replies($comment = false)
    {

        global $wpdb;
        $author_ids = get_option('cnrt_ids');
        $author_ids = explode(',', $author_ids);
        $replies = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT comment_ID, user_id, comment_author_email, comment_post_ID FROM $wpdb->comments WHERE comment_parent = %d",
                $comment->comment_ID
            )
        );
        $replied = array();
        foreach ($replies as $reply) {
            $author = get_userdata($reply->user_id);
            if (!$author) {
                array_push($replied, 'false');
                continue;
            }
            $has_elevated_privileges = array_intersect($author->roles, array('editor', 'administrator'));
            $comment_is_by_authors = in_array($author->ID, $author_ids);
            if ($has_elevated_privileges || $comment_is_by_authors) {
                array_push($replied, 'true');
                continue;
            }
        }
        if (in_array('true', $replied)) {
            return $replies;
        }
        return false;
    }


    /**
     * Determines whether the comment is by any of the selected authors or any with privileges
     *
     * @param    object $comment
     * @return    bool                    Whether or not the post author has replied.
     * @since    1.0
     */
    private function comment_is_by_authors($comment = false)
    {
        global $wpdb;
        $author_ids = get_option('cnrt_ids');
        $author = get_userdata($comment->user_id);
        if (!$author) return false;
        $has_elevated_privileges = array_intersect($author->roles, array('editor', 'administrator'));
        $comment_is_by_authors = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $wpdb->comments WHERE user_id IN ($author_ids) AND comment_id = %d",
                $comment->comment_ID
            )
        );
        if ($has_elevated_privileges || $comment_is_by_authors) {
            return $author;
        }
        return false;
    } // end comment_is_by_authors


    /**
     * Retrieves the email address for the author of the post.
     *
     * @param    int $post_id The ID of the post for which to retrieve the email address
     * @return    string                    The email address of the post author
     * @since    1.0
     */
    private function get_post_author_email($post_id = 0)
    {

        // Get the author information for the specified post
        $post = get_post($post_id);
        $author = get_user_by('id', $post->post_author);

        // Let's store the author data as the author
        $author = $author->data;

        return $author->user_email;

    } // end get_post_author_email

    /**
     * Adds a new item in the comment status links to select those missing a reply
     *
     * @return    array                The array of columns to display
     *
     * @since    2.0
     */

    public function status_links($status_links = array())
    {

        // add check for including 'current' class
        $current_missing = isset($_GET['missing_reply']) ? 'class="current"' : '';
        $current_missing_hot = isset($_GET['missing_reply_hot']) ? 'class="current"' : '';
        if ($current_missing || $current_missing_hot) {
            $status_links['all'] = str_replace('current', '', $status_links['all']);
        }
        // get missing count
        $missing_num = $this->get_missing_count();
        $missing_num_hot = $this->get_missing_count_hot();


        // missing reply
        $status_link = '<a href="edit-comments.php?comment_status=all&missing_reply=1" ' . $current_missing . '>';
        $status_link .= __('Missing Reply', 'cnrt');
        $status_link .= ' <span class="count">(<span class="pending-count">' . $missing_num . '</span>)</span>';
        $status_link .= '</a>';

        // hot missing reply
        $status_link_hot = '<a href="edit-comments.php?comment_status=all&missing_reply_hot=1" ' . $current_missing_hot . '>';
        $status_link_hot .= __('HOT Missing Reply', 'cnrt');
        $status_link_hot .= ' <span class="count">(<span class="pending-count">' . $missing_num_hot . '</span>)</span>';
        $status_link_hot .= '</a>';

        // set new link
        $status_links['missing_reply'] = $status_link;
        $status_links['missing_reply_hot'] = $status_link_hot;

        // return all the status links
        return $status_links;

    } // end missing_reply_status_link


    /**
     * Return the missing replies in a list on the comments table
     * @param    int $comments The object array of comments
     * @return    array                The filtered comment data
     *
     * @since    1.0
     */

    public function return_missing_list($comments = array())
    {

        // bail on anything not admin
        if (!is_admin() || !function_exists('get_current_screen'))
            return;

        // only run this on the comments table
        $current_screen = get_current_screen();

        if ('edit-comments' !== $current_screen->base)
            return;

        // check for query param
        if (!isset($_GET['missing_reply']))
            return;

        // now run action to show missing
        $comments->query_vars['meta_key'] = '_cnrt_missing';
        $comments->query_vars['meta_value'] = '1';

        // Because at this point, the meta query has already been parsed,
        // we need to re-parse it to incorporate our changes
        $comments->meta_query->parse_query_vars($comments->query_vars);

    } // end missing_reply_list

    /**
     * Return the missing replies in a list on the comments table
     * that are more than 48 hours old
     * @param    int $comments The object array of comments
     * @return    array                The filtered comment data
     *
     * @since    1.0
     */

    public function return_missing_list_hot($comments = array())
    {

        // bail on anything not admin
        if (!is_admin() || !function_exists('get_current_screen'))
            return;

        // only run this on the comments table
        $current_screen = get_current_screen();

        if ('edit-comments' !== $current_screen->base)
            return;

        // check for query param
        if (!isset($_GET['missing_reply_hot']))
            return;

        // now run action to show missing
        $comments->query_vars['meta_key'] = '_cnrt_missing';
        $comments->query_vars['meta_value'] = '1';
        $comments->query_vars['date_query'] = array(
            'before' => '-2 days'
        );

        // Because at this point, the meta query has already been parsed,
        // we need to re-parse it to incorporate our changes
        $comments->meta_query->parse_query_vars($comments->query_vars);

    } // end missing_reply_list


    /**
     * Add the meta tag to comments for query logic later
     * @param    int $comment_id
     * @return    bool Whether or not the post author has replied.
     *
     * @since    1.0
     */

    public function add_missing_meta($comment_id = 0)
    {

        // get comment object array to run author comparison
        $comment = get_comment($comment_id);
        $comment_is_by_authors = $this->comment_is_by_authors($comment);
        if ($comment_is_by_authors) {
            return false;
        }

        // set an inital false tag on comment set
        add_comment_meta($comment_id, '_cnrt_missing', true);

    } // end add_missing_meta

    /**
     * Remove the meta tag to comments for query logic later
     * @param    int $comment_id
     * @return    bool                    Whether or not the post author has replied.
     *
     * @since    1.0
     */

    public function remove_missing_meta($comment_id = 0)
    {

        // get comment object array
        $comment = get_comment($comment_id);

        // get comment parent ID, post ID, and user ID
        $parent_id = $comment->comment_parent;

        // check for meta key first, bail if not present
        $missing = get_comment_meta($parent_id, '_cnrt_missing', true);

        if (empty($missing))
            return;

        $comment_is_by_authors = $this->comment_is_by_authors($comment);

        if ($comment_is_by_authors) {
            delete_comment_meta($parent_id, '_cnrt_missing');
        }
    } // end remove_missing_meta

    /**
     * Return number of comments with missing replies, either global or per post
     * @param    int $post_id optional post ID for which to retrieve count.
     * @return    int                        the count
     *
     * @since    1.0
     */
    public function get_missing_count()
    {
        global $wpdb;
        $args = array(
            'meta_key' => '_cnrt_missing',
            'meta_value' => '1',
            'count' => true
        );

        $sql = "SELECT COUNT(*) FROM wp_commentmeta INNER JOIN wp_comments ON wp_comments.comment_id = wp_commentmeta.comment_id WHERE wp_commentmeta.meta_key =
        '_cnrt_missing' AND
        wp_comments.comment_approved NOT IN ('post-trashed', 'trash')";
        $count = $wpdb->get_var($sql);

        // $count = get_comments($args);
        return $count;
    }

    /**
     * Return number of comments with missing replies that are more than 48 hours old
     * @param    int $post_id optional post ID for which to retrieve count.
     * @return    int                        the count
     *
     * @since    2.0
     */
    public function get_missing_count_hot()
    {
        $args = array(
            'meta_key' => '_cnrt_missing',
            'meta_value' => '1',
            'date_query' => array(
                'before' => '-2 days'
            ),
            'count' => true
        );

        $count = get_comments($args);
        return $count;
    }

    /**
     * Add CSS to the admin head
     *
     * @return    void
     *
     * @since    1.0
     */
    public function admin_css()
    {

        $current_screen = get_current_screen();

        if ($current_screen->base !== 'edit-comments')
            return;

        echo '<style type="text/css">
			span.cnrt {
				padding: 3px 0 0;
				display: block;
			}
			.cnrt img {
				display: inline-block;
				vertical-align: top;
				margin: 0 4px 0 0;
			}
			.column-missing-reply {
			text-align: center;
			}
			.td-light-green-bg {
			    background: rgba(36, 250, 84, 0.45) !important;
			}
			.td-light-yellow-bg {
			    background: #fff4a0 !important;
			}
			.td-light-red-bg {
			    background: #ffa1a2 !important;
			}
			</style>';

    } // end admin_css

} // end class


/**
 * Instantiates the plugin using the plugins_loaded hook and the
 * Singleton Pattern.
 */
function Comments_Not_Replied_To()
{
    Custom_Comments_Not_Replied_To::getInstance();
} // end Comments_Not_Replied_To
add_action('plugins_loaded', 'Comments_Not_Replied_To');

// install and uninstall hooks
register_activation_hook(__FILE__, array('Custom_Comments_Not_Replied_To', 'install'));
// register_uninstall_hook( __FILE__, array($this, 'uninstall'));
