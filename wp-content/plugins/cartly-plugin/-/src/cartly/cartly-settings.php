<?php

if (!class_exists('CartlySettings'))
{
	class CartlySettings
	{
		public function __construct()
		{
            add_action('admin_init', array(&$this, 'admin_init'));
        	add_action('admin_menu', array(&$this, 'add_menu'));
		}

        public function admin_init()
        {
        	register_setting('cartly-option-group', 'cartly_new_order_email');
        	register_setting('cartly-option-group', 'cartly_sales_tax_percentage');
        	register_setting('cartly-option-group', 'cartly_minimum_shipping');
        	register_setting('cartly-option-group', 'cartly_free_shipping');
        	register_setting('cartly-option-group', 'cartly_track_inventory');
        	register_setting('cartly-option-group', 'cartly_remove_add_to_cart');
        	register_setting('cartly-option-group', 'cartly_payment_use_stripe');
        	register_setting('cartly-option-group', 'cartly_payment_stripe_live');
        	register_setting('cartly-option-group', 'cartly_payment_stripe_test_secret_key');
        	register_setting('cartly-option-group', 'cartly_payment_stripe_test_publishable_key');
        	register_setting('cartly-option-group', 'cartly_payment_stripe_live_secret_key');
        	register_setting('cartly-option-group', 'cartly_payment_stripe_live_publishable_key');

			// Store Settings
        	add_settings_section(
        	    'cartly-section', 
        	    'Store Settings', 
        	    array(&$this, 'settings_section_cartly'), 
        	    'cartly'
        	);
            
            add_settings_field(
                'cartly-new-order-email', 
                'New Order Email Address', 
                array(&$this, 'settings_field_input_text'), 
                'cartly', 
                'cartly-section',
                array(
                    'field' => 'cartly_new_order_email',
                    'placeholder' => 'e.g you@website.com',
                    'class' => 'regular-text'
                )
            );
            
            add_settings_field(
                'cartly-sales-tax-percentage', 
                'Sales Tax Percentage', 
                array(&$this, 'settings_field_input_text'), 
                'cartly', 
                'cartly-section',
                array(
                    'field' => 'cartly_sales_tax_percentage',
                    'placeholder' => 'e.g. 7.5',
                    'class' => 'regular-text'
                )
            );
            
            add_settings_field(
                'cartly-minimum-shipping', 
                'Minimum Shipping', 
                array(&$this, 'settings_field_input_text'), 
                'cartly', 
                'cartly-section',
                array(
                    'field' => 'cartly_minimum_shipping',
                    'placeholder' => 'e.g. 10.00',
                    'class' => 'regular-text'
                )
            );
            
            add_settings_field(
                'cartly-free-shipping', 
                'Free Shipping', 
                array(&$this, 'settings_field_input_checkbox'), 
                'cartly', 
                'cartly-section',
                array(
                    'field' => 'cartly_free_shipping',
                    'label' => 'Shipping is free for all orders'
                )
            );
            
            add_settings_field(
                'cartly-track-inventory', 
                'Track Inventory', 
                array(&$this, 'settings_field_input_checkbox'), 
                'cartly', 
                'cartly-section',
                array(
                    'field' => 'cartly_track_inventory',
                    'label' => 'Reduce quantity when a product is sold'
                )
            );
            
            add_settings_field(
                'cartly-remove-add-to-cart', 
                'Sold Out Products', 
                array(&$this, 'settings_field_input_checkbox'), 
                'cartly', 
                'cartly-section',
                array(
                    'field' => 'cartly_remove_add_to_cart',
                    'label' => 'Remove Add to Cart button when a product is sold out'
                )
            );
            
            // Payment Settings
            add_settings_section(
        	    'cartly-payment-section', 
        	    'Payment Settings', 
        	    array(&$this, 'settings_section_cartly'), 
        	    'cartly'
        	);
        	
        	add_settings_field(
                'cartly-payment-use-stripe', 
                'Stripe', 
                array(&$this, 'settings_field_input_checkbox'), 
                'cartly', 
                'cartly-payment-section',
                array(
                    'field' => 'cartly_payment_use_stripe',
                    'label' => 'Use Stripe for Payments &mdash; <a href="https://stripe.com" target="_blank">Sign Up</a>'
                )
            );
            
            add_settings_field(
                'cartly-payment-stripe-live', 
                'Live', 
                array(&$this, 'settings_field_input_checkbox'), 
                'cartly', 
                'cartly-payment-section',
                array(
                    'field' => 'cartly_payment_stripe_live',
                    'label' => 'My site is live, use live Stripe keys'
                )
            );
            
            add_settings_field(
                'cartly-payment-stripe-test-secret-key', 
                'Test Secret Key', 
                array(&$this, 'settings_field_input_text'), 
                'cartly', 
                'cartly-payment-section',
                array(
                    'field' => 'cartly_payment_stripe_test_secret_key',
                    'class' => 'regular-text code'
                )
            );
            
            add_settings_field(
                'cartly-payment-stripe-test-publishable-key', 
                'Test Publishable Key', 
                array(&$this, 'settings_field_input_text'), 
                'cartly', 
                'cartly-payment-section',
                array(
                    'field' => 'cartly_payment_stripe_test_publishable_key',
                    'class' => 'regular-text code'
                )
            );
            
            
            add_settings_field(
                'cartly-payment-stripe-live-secret-key', 
                'Live Secret Key', 
                array(&$this, 'settings_field_input_text'), 
                'cartly', 
                'cartly-payment-section',
                array(
                    'field' => 'cartly_payment_stripe_live_secret_key',
                    'class' => 'regular-text code'
                )
            );
            
            add_settings_field(
                'cartly-payment-stripe-live-publishable-key', 
                'Live Publishable Key', 
                array(&$this, 'settings_field_input_text'), 
                'cartly', 
                'cartly-payment-section',
                array(
                    'field' => 'cartly_payment_stripe_live_publishable_key',
                    'class' => 'regular-text code'
                )
            );
        }
        
        public function settings_section_cartly()
        {
        }
        
        public function settings_field_input_text($args)
        {
            $field = $args['field'];            
            $value = get_option($field);
            $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
            $class = isset($args['class']) ? $args['class'] : '';
            
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" placeholder="%s" class="%s" />',
            	$field,
            	$field,
            	$value,
            	$placeholder,
            	$class);
        }
        
        public function settings_field_input_checkbox($args)
        {
            $field = $args['field'];
            $label = $args['label'];
            $value = get_option($field);
            $checked = $value == 1 ? 'checked' : '';
            
            echo sprintf('<label for="%s"><input type="checkbox" name="%s" id="%s" value="1" %s /> %s</label>',
            	$field,
            	$field,
            	$field,
            	$checked,
            	$label);
        }
        
        public function add_menu()
        {	
			add_menu_page(
				'Cartly Settings',
				'Cartly',
				'manage_options',
				'cartly_settings',
				array(&$this, 'cartly_settings_page'),
				'',
				58
			);
			
			add_submenu_page(
				'cartly_settings',
				'Cartly Product Options',
				'Product Option Sets',
				'manage_options',
				'cartly_product_options',
				array(&$this, 'cartly_product_options_page')
			);
        }
	
        public function cartly_settings_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
	
        	include(sprintf("%s/cartly-settings-html.php", dirname(__FILE__)));
        }
        
        public function cartly_product_options_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
	
        	include(sprintf("%s/cartly-product-options-html.php", dirname(__FILE__)));
        }
    }
}
?>