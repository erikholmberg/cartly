<?php
/*
Plugin Name: Cartly
Plugin URI: http://getcartly.com
Description: Empowering makers... The new way to add E-Commerce to WordPress
Version: 1.0
Author: Erik Holmberg
Author URI: http://erikholmberg.com
*/

if (!class_exists('Cartly'))
{
	define('CARTLY_PATH', plugin_dir_path(__FILE__));
	define('CARTLY_DIR', plugins_url('', __FILE__));
	
	if (!defined('CARTLY_DELIMITER'))
	{
		define('CARTLY_DELIMITER', '::');	
	}

	class Cartly
	{
		public function __construct()
		{
			require_once(sprintf("%s/-/src/cartly/cartly-data.php", dirname(__FILE__)));
            require_once(sprintf("%s/-/src/cartly/cartly-settings.php", dirname(__FILE__)));
            require_once(sprintf("%s/-/src/cartly/cartly-orders.php", dirname(__FILE__)));            
            require_once(sprintf("%s/-/src/cartly/cartly-post-type-product.php", dirname(__FILE__)));
            
            $CartlySettings = new CartlySettings();
            $CartlyOrders = new CartlyOrders();
            $CartlyPostTypeProduct = new CartlyPostTypeProduct();
		}
	    
		public static function Activate()
		{
			// Create Database Tables
			if ($activationSql = file_get_contents(sprintf("%s/-/sql/cartly-activation.sql", dirname(__FILE__))))
			{	
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($activationSql);
			}
			
			// Set Up Pages
			static::SetUpPages();
		}
	
		public static function Deactivate()
		{
			// Remove Database Tables
			if ($deactivationSql = file_get_contents(sprintf("%s/-/sql/cartly-deactivation.sql", dirname(__FILE__))))
			{
				global $wpdb;
				$wpdb->query($deactivationSql);
			}
		}
		
		public static function AddToFooter()
		{
			$stripeKey = '';
			$useStripe = get_option('cartly_payment_use_stripe');
			$liveStripe = get_option('cartly_payment_stripe_live');
			$testStripeKey = get_option('cartly_payment_stripe_test_publishable_key');
			$liveStripeKey = get_option('cartly_payment_stripe_live_publishable_key');
			
			if ($useStripe == 1)
			{
				echo '<script type="text/javascript" src="https://js.stripe.com/v2/"></script>'."\r\n";
				
				if ($liveStripe == 1 && !empty($liveStripeKey))
				{
					$stripeKey = $liveStripeKey;
				}
				else if (!empty($testStripeKey))
				{
					$stripeKey = $testStripeKey;
				}
				
				if (!empty($stripeKey))
				{
					echo '<script type="text/javascript">Stripe.setPublishableKey(\''.$stripeKey.'\');</script>'."\r\n";
				}
			}
		}
		
		public static function IncludeAdminScripts()
		{
			// CSS
			wp_register_style('cartly-style', plugins_url('/-/css/cartly-admin.css', __FILE__), array(), '20140319', 'all');	
			wp_enqueue_style('cartly-style');
			
			wp_register_style('cartly-print-style', plugins_url('/-/css/cartly-admin-print.css', __FILE__), array(), '20140319', 'print');	
			wp_enqueue_style('cartly-print-style');
			
			// JavaScript
			echo '<script type="text/javascript">var cartlyAjaxUrl = \''.CARTLY_DIR.'/-/src/cartly/cartly-ajax.php\';</script>'."\r\n";
		     
		    wp_register_script('cartly-script', plugins_url('/-/js/cartly-admin.js', __FILE__), array('jquery'));
		    wp_enqueue_script('cartly-script');	
		}
		
		private static function SetUpPages()
		{
			global $wpdb;
			
			// Add Store Pages
			$pageNames = array('Cart', 'Account', 'Log In', 'Password Reset', 'Order');
			
			foreach ($pageNames as $pageName)
			{
				if (!get_page_by_title($pageName))
				{	
					$newPage = array(
						'post_title'    => $pageName,
						'post_type'    => 'page',
						'post_status'   => 'publish',
						'post_author'   => 1
					);
				
					wp_insert_post($newPage);
				}
			}
			
			// Change Sample Page to Home Page
			$wpdb->update(
				'wp_posts',
				array(
					'post_title' => 'Home',
					'post_name' => 'home'
				),
				array('ID' => 2)
			);
		}
	}
}

if (class_exists('Cartly'))
{
	register_activation_hook(__FILE__, array('Cartly', 'Activate'));
	register_deactivation_hook(__FILE__, array('Cartly', 'Deactivate'));

	$cartly = new Cartly();
	
    if (isset($cartly))
    {
        function plugin_settings_link($links)
        { 
            $settings_link = '<a href="admin.php?page=cartly_settings">Settings</a>'; 
            array_unshift($links, $settings_link); 
            return $links; 
        }

        $plugin = plugin_basename(__FILE__); 
        add_filter("plugin_action_links_$plugin", 'plugin_settings_link');        
        add_action('admin_enqueue_scripts', array('Cartly', 'IncludeAdminScripts'));		
		add_action('wp_footer', array('Cartly', 'AddToFooter'));
    }
}

?>