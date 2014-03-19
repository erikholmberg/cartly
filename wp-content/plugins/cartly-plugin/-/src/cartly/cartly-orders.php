<?php

if (!class_exists('CartlyOrders'))
{
	class CartlyOrders
	{
		public function __construct()
		{
			add_action('admin_menu', array(&$this, 'add_menu'));
		}
        
        public function add_menu()
        {
        	add_object_page(
        		'Orders',
        		'Orders',
        		'manage_options',
        		'cartly_orders',
        		array(&$this, 'cartly_orders_page')
        	);
        }
        
        public function cartly_orders_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
        	
        	if (isset($_GET['order_id']))
			{
				include(sprintf("%s/cartly-order-html.php", dirname(__FILE__)));
			}
			else
			{
				include(sprintf("%s/cartly-orders-html.php", dirname(__FILE__)));
			}
        }
    }
}

?>