<?php

// Initialize
if (!function_exists('cartly_init')):

function cartly_init()
{
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'feed_links', 2);
	
	require_once('-/src/cartly/Account.php');
	require_once('-/src/cartly/Cart.php');
	require_once('-/src/cartly/Cookie.php');
	require_once('-/src/cartly/Store.php');
	require_once('-/src/cartly/Constants.php');
	require_once('-/src/cartly/Utilities.php');
	
	if (!defined('CARTLY_DELIMITER'))
	{
		define('CARTLY_DELIMITER', '::');	
	}
	
	global
		$account,
		$cart,
		$store,
		$accountPage,
		$loginPage,
		$cartPage,
		$passwordResetPage,
		$orderPage,
		$cartlyPluginActive;
	
	$cart = new Cart();
	$account = new Account();
	$store = new Store();
		
	$accountPage = get_page_by_title('Account');
	$loginPage = get_page_by_title('Log In');
	$cartPage = get_page_by_title('Cart');
	$passwordResetPage = get_page_by_title('Password Reset');
	$orderPage = get_page_by_title('Order');
	
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	$cartlyPluginActive = is_plugin_active('cartly-plugin/cartly.php');
}

add_action('init','cartly_init');

endif;

// Scripts
if (!function_exists('cartly_scripts')) :

function cartly_scripts()
{
    wp_deregister_script('jquery');  
    wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', array(), '1.11.0', true);  
    wp_register_script('custom-script', get_template_directory_uri() . '/-/js/cartly.js', array('jquery'), '1.0', true); 
    wp_enqueue_script('custom-script');
}

add_action('wp_enqueue_scripts', 'cartly_scripts');

endif;

// Footer
if (!function_exists('cartly_footer')):

function cartly_footer()
{
	echo '<script type="text/javascript">var cartlyAjaxUrl = \''.get_template_directory_uri().'/ajax.php\';</script>'."\r\n";
}

add_action('wp_footer', 'cartly_footer');

endif;

// Set Up
if (!function_exists('cartly_setup')):

function cartly_setup()
{
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
}

add_action('after_setup_theme', 'cartly_setup');

endif;

// Page Titles
if (!function_exists('cartly_title')) :

function cartly_title()
{
	global $page, $paged;
	wp_title('|', true, 'right' );
	bloginfo('name' );
	$site_description = get_bloginfo('description', 'display' );
	if ( $site_description && !is_front_page())
		echo ' | ' . $site_description;
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __('Page %s', 'cartly' ), max( $paged, $page));
}

endif;

// Page Descriptions
if (!function_exists('cartly_description')) :

function cartly_description()
{
	global $page, $paged;
	wp_title('|', true, 'right' );
	bloginfo('name' );
	$site_description = get_bloginfo('description', 'display' );
	if ( $site_description )
		echo ' | ' . $site_description;
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __('Page %s', 'cartly' ), max( $paged, $page));
}

endif;

// Add Session Support
function cartly_start_session()
{
    if(!session_id())
    {
        session_start();
    }
}

add_action('init', 'cartly_start_session', 1);

// Stop Session
function cartly_end_session()
{
     session_destroy();
}