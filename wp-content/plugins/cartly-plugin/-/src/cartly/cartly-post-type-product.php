<?php

if (!class_exists('CartlyPostTypeProduct'))
{
	class CartlyPostTypeProduct
	{
		const POST_TYPE	= "products";
		
		private $_meta	= array(
			'price',
			'shipping',
			'quantity',
			'on_sale',
			'is_new'
		);
		
    	public function __construct()
    	{
    		add_action('init', array(&$this, 'init'));
    		add_action('admin_init', array(&$this, 'admin_init'));
    	}

    	public function init()
    	{
    		$this->create_post_type();
    		add_action('save_post', array(&$this, 'save_post'));
    	}

    	public function create_post_type()
    	{
    		register_post_type(self::POST_TYPE,
    			array(
    				'labels' => array(
    					'name' => 'Products',
    					'singular_name' => 'Product'
    				),
    				'public' => true,
    				'has_archive' => true,
    				'description' => __("The Product post type is used to hold your store's products."),
    				'supports' => array(
    					'title', 'editor', 'excerpt', 'thumbnail', 
    				),
    				'taxonomies' => array('category'),
    			)
    		);
    	}
	
    	public function save_post($post_id)
    	{
            if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !isset($_POST['post_type']))
            {
                return;
            }
            
    		if ($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    		{
    			// Static Meta
    			foreach($this->_meta as $field_name)
    			{
    				if (isset($_POST[$field_name]))
    				{
    					update_post_meta($post_id, $field_name, $_POST[$field_name]);
    				}
    			}
    			
    			// Dynamic Meta
    			$optionIndex = 0;
    			
    			if (!empty($_POST['option_name']))
    			{
	    			foreach ($_POST['option_name'] as $optionName)
	    			{
	    				$optionPrice = $_POST['option_price'][$optionIndex];
	    				$optionShipping = $_POST['option_shipping'][$optionIndex];
	    				$optionQuantity = $_POST['option_quantity'][$optionIndex];
	    				$optionKey = $_POST['option_key'][$optionIndex];
	    				
	    				if ($optionName != '' && $optionPrice != '')
	    				{	
							$optionIndex++;
							update_post_meta(
								$post_id,
								$optionKey,
								$optionName.CARTLY_DELIMITER.$optionPrice.CARTLY_DELIMITER.$optionShipping.CARTLY_DELIMITER.$optionQuantity
							);
						}
	    			}
    			}
    		}
    		else
    		{
    			return;
    		}
    	}

    	public function admin_init()
    	{			
    		add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
    	}
			
    	public function add_meta_boxes()
    	{
    		add_meta_box( 
    			sprintf('cartly_product_%s_section', self::POST_TYPE),
    			'Product Details',
    			array(&$this, 'add_inner_meta_boxes'),
    			self::POST_TYPE
    	    );					
    	}

		public function add_inner_meta_boxes($post)
		{		
			include(sprintf("%s/cartly-post-type-product-html.php", dirname(__FILE__)));			
		}

	}
}