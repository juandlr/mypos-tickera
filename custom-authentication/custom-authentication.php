<?php

/*
  Plugin Name: custom authentication
  Description: custom authentication
  Version: 1.0
  Author: Juan
  Author URI: http://admin123.co
 */
defined('ABSPATH') or die('No script kiddies please!');

function custom_authentication_scripts() {
//  // remove all session variables
//  session_unset();
//
//// destroy the session 
//  session_destroy();
  // optional: here we load any css or javascript files we require
  wp_enqueue_style('custom-authentication-css', plugins_url('custom-authentication.css', __FILE__));
  wp_enqueue_script('custom-authentication-js', plugins_url('custom-authentication.js', __FILE__), array(), '', TRUE);
// Now we can localize the script with our data. we add siteurl variable so its accessible from javascript
  wp_localize_script('custom-authentication-js', 'WPURLS', array('siteurl' => get_option('siteurl')));
}

// we load all scripts
add_action('wp_enqueue_scripts', 'custom_authentication_scripts');
// we execute custom_authentication_post function when wordpress loads
add_action('init', 'custom_authentication_post');
// register shortcodes 
add_shortcode('customauthform', 'custom_authentication_form');
add_shortcode('userdetails', 'display_user_details');
add_shortcode('autologinform', 'autologin_form');

function custom_authentication_form() {
// now we put all of the HTML for the form into a PHP string
  $content = <<<EOT
        <div>
          <form id="custom-authentication-form" method='post' action="">
            <label>user</label></br>
            <input type="text" name="custom-authentication-user" value="" /><br />
            <label>password</label><br />
            <input type="password" name="custom-authentication-pass" value="" /><br />
            <input type="submit" value="Authenticate" name='custom-authentication-submit' />
          </form>
        </div>
EOT;
  return $content;
}

/**
 * this is our autologinform, it checks for the access token, if valid the form is returned and can be printed in a shortcode
 * @return string 
 */
function autologin_form() {
  if (isset($_SESSION['access_token'])) {
    $content = <<<EOT
   <form id="gototheexchange" name="gototheexchange" action="http://exchange.ix-one.net/AutoLogin" method="post">
        <div>
            <input type="hidden" name="SecurityToken" value="{$_SESSION['access_token']}" />
            <input type="submit" value="click here to submit" name="submit">
        </div>
    </form>
EOT;
    return $content;
  }
}

/**
 * this is our main function, it executes everytime wordpress is loaded, it checks for the access token in the session 
 * or for posted values from the login form
 */
function custom_authentication_post() {
  // we declare our sites urls $url_0 is the main site, $url is the current site and $url1 is the second site
  $url_0 = 'http://local.wordpress.com:8888';
  $url = 'http://' . $_SERVER["HTTP_HOST"];
  $url1 = 'http://local.wordpress2.com:8888';
  // if there is no session, start one
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  // check for access token in session or in get request, we validate it and if invalid we redirect to site 1
  if (isset($_GET['access_token']) || isset($_SESSION['access_token'])) {
    $access_token = isset($_GET['access_token']) ? $_GET['access_token'] : $_SESSION['access_token'];
    $validated = token_validate($access_token);
    if (!$validated && $url != $url_0) {
      header("Location: $url_0");
      die();
    }
  }
  elseif ($url == $url1) {
    header("Location: $url_0");
    die();
  }

  // check for posted values from site 1 and try to login user by submitting values to https://preweb.ix-one.net/GetSecurityToken
  if (isset($_POST['custom-authentication-submit'])) {
    // we retrieve the posted values and build the body of our api request
    $username = $_POST['custom-authentication-user'];
    $password = $_POST['custom-authentication-pass'];
    $api_url = 'https://exchange.ix-one.net/GetSecurityToken';
    $body = array(
      'grant_type' => 'password',
      'username' => $username,
      'password' => $password,
    );
    $args = array(
      'body' => $body,
      'timeout' => '5',
    );
// Send the request & save response to $response
    $response = wp_remote_post($api_url, $args);
    $response = json_decode($response['body']);
    // if we get an access token in the response we validate it
    if (isset($response->access_token)) {
      $access_token = $response->access_token;
      $validated = token_validate($access_token);
      // if validated we set the access token in a session, now we are logged in
      if ($validated) {
        // Set session variables
        $_SESSION["access_token"] = $access_token;
        // if the current url is not site 2, we redirect to site 2
        if ($url != $url1) {
          $newURL = "$url1/?" . "access_token=$access_token";
          header("Location: {$newURL}");
          die();
        }
      }
      // if the token doesnt validate we alert the user
      else {
        $alert = <<<EOT
        <script>
        alert('Invalid username/password combination. Please try again or contact the administrator');
        </script>
EOT;
        print $alert;
      }
    }
    // if we dont get an access token we alert the user
    else {
      $alert = <<<EOT
        <script>
        alert('Invalid username/password combination. Please try again or contact the administrator');
        </script>
EOT;
      print $alert;
    }
  }
}

/**
 * this function validates the token and gets the user details
 * @param type $access_token
 * @return boolean
 */
function token_validate($access_token) {
  // we declare our request variables
  $url = 'https://exchange.ix-one.net/api/security/get_user_name/';
  $headers = array(
    'Authorization' => "Bearer $access_token",
  );
  $args = array(
    'headers' => $headers,
    'timeout' => '5',
  );
  // send the request 
  $response = wp_remote_get($url, $args);
  // check for response code in the response
  if ($response['response']['code'] == 200) {
    // decode json string
    $response = json_decode('{
  "FirstName": "",
  "LastName": "BARRETT"
}');
    // if we dont have a first name we assign N/A to it
    if (!$response->FirstName) {
      $response->FirstName = 'N/A';
    }
    $_SESSION['access_token'] = $access_token;
    return $response;
  }
  else {
    return false;
  }
}

/**
 * this function displays user details in a shortcode
 * @return string 
 */
function display_user_details() {
  // check for token in session
  if (isset($_SESSION['access_token'])) {
    // validate session token
    if ($response = token_validate($_SESSION['access_token'])) {
      // if token valid return user details
      $content = <<<EOT
        Logged in as $response->FirstName $response->LastName
        Access Token {$_SESSION['access_token']}
EOT;
      return $content;
    }
  }
}
