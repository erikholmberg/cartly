<?php

require_once('Data.php');
require_once('Account.php');
require_once('Cart.php');
require_once('Payment.php');
require_once('Order.php');
require_once('Utilities.php');
require_once('Email.php');

class Store
{	
	// Constructor
	function __construct()
	{
	}
	
	// Public Methods
	public function GetCountries()
	{
		return Data::GetResults('SELECT code, name FROM cartly_country');
	}
	
	public function GetCartTotal($products = array(), &$cartShipping, &$cartTax)
	{
		$cartTax = 0.00;
		$total = 0.00;
		$shipping = 0.00;
		$noOptionIds = array();
		$optionIds = array();
		
		if (count($products) > 0)
		{
			foreach ($products as $product)
			{
				if (empty($product->option_id))
				{
					$noOptionIds[] = $product->product_id;
				}
				else
				{
					$optionIds[] = $product->option_id;
				}
			}
			
			// Get total for products without options
			if (!empty($noOptionIds))
			{
				$sql = "SELECT pm1.post_id as product_id, pm1.meta_value AS price, pm2.meta_value AS shipping FROM wp_postmeta pm1 LEFT OUTER JOIN wp_postmeta pm2 ON pm1.post_id = pm2.post_id AND pm2.meta_key = 'shipping' WHERE pm1.meta_key = 'price' AND pm1.post_id IN (";
				$sql .= implode(',', $noOptionIds);
				$sql .= ")";	
				
				$prices = Data::GetResultsObject($sql);
				
				foreach ($prices as $price)
				{
					foreach ($products as $product)
					{
						if ($product->product_id == $price->product_id && empty($product->option_id))
						{
							$total += $price->price * $product->quantity;
							$shipping += $price->shipping * $product->quantity;
						}
					}
				}
			}
			
			// Get total for products with options
			if (!empty($optionIds))
			{
				$sql = "SELECT * FROM wp_postmeta pm1 WHERE pm1.meta_id IN (";
				$sql .= implode(',', $optionIds);
				$sql .= ")";
				
				$options = Data::GetResultsObject($sql);
				
				foreach ($options as $option)
				{
					foreach ($products as $product)
					{
						if ($product->product_id == $option->post_id && $product->option_id == $option->meta_id)
						{
							$values = explode(CARTLY_DELIMITER, $option->meta_value);
							$total += $values[1] * $product->quantity;
							$shipping += $values[2] * $product->quantity;
						}
					}
				}
			}
		}
		
		// Calculate Shipping
		$freeShipping = get_option('cartly_free_shipping');
		
		if (empty($freeShipping))
		{
			$cartShipping = number_format(floatval($shipping), 2);
		}
		else
		{
			$cartShipping = 0.00;
		}
		
		// Calculate Sales Tax
		$salesTax = get_option('cartly_sales_tax_percentage');
		
		if (!empty($salesTax))
		{
			if ($salesTax > 0)
			{
				$salesTax = $salesTax * .01;
			}
			
			$cartTax = number_format(floatval($total * $salesTax), 2);
		}
		
		return number_format(floatval($total), 2);
	}
	
	public function GetProductTotal($productId, $optionId, $quantity)
	{
		$products = array((object)array('product_id' => $productId, 'option_id' => $optionId, 'quantity' => $quantity));
		return $this->GetCartTotal($products, $cartShipping, $cartTax);
	}
	
	public function GetProducts($status = '', $imageSize = 'full-size', $orderby = 'post_date', $order = 'DESC', $productIds = array(), $categoryId = NULL)
	{
		// TODO: Performance Implications?
		$productsSql = "SELECT p.ID, p.post_content, p.post_title, p.post_excerpt, p.post_name, p.guid, (SELECT meta_value FROM wp_postmeta pm WHERE pm.post_id = p.ID AND pm.meta_key = 'price') AS price, (SELECT meta_value FROM wp_postmeta pm WHERE pm.post_id = p.ID AND pm.meta_key = 'shipping') AS shipping, (SELECT meta_value FROM wp_postmeta pm WHERE pm.post_id = p.ID AND pm.meta_key = 'on_sale') AS on_sale, (SELECT meta_value FROM wp_postmeta pm WHERE pm.post_id = p.ID AND pm.meta_key = 'is_new') AS is_new, (SELECT meta_value FROM wp_postmeta pm WHERE pm.post_id = p.ID AND pm.meta_key = 'quantity') AS quantity, tr.term_taxonomy_id AS category_id FROM wp_posts p LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id WHERE p.post_type = 'products'";
		
		if (!empty($status))
		{
			$productsSql .= " AND p.post_status = '$status'";
		}
		
		if (!empty($productIds))
		{
			if (is_array($productIds))
			{
				$productsSql .= " AND p.ID IN (".implode(',', $productIds).")";
			}
		}
		
		if (!empty($categoryId))
		{
			$productsSql .= " AND tr.term_taxonomy_id = $categoryId";
		}
		
		if (!empty($orderby))
		{
			$productsSql .= " ORDER BY p.$orderby";
			
			if (!empty($order))
			{
				$productsSql .= " $order";
			}
		}
		
		if (count($productIds) == 1)
		{
			$productsSql .= " LIMIT 1";	
		}
		
		$products = Data::GetResultsObject($productsSql);
		
		foreach ($products as $product)
		{
			$this->GetProductMeta($product, $product->ID, $imageSize);
		}
		
		return count($products) == 1 ? $products[0] : $products;
	}
	
	public function GetProductPrice($product, $optionId)
	{
		$price = 0.00;
		
		if (empty($optionId))
		{
			$price = get_post_meta($product->ID, 'price', TRUE);
		}
		else
		{
			if (!empty($product->options))
			{
				foreach($product->options as $option)
				{
					if ($option['id'] == $optionId)
					{
						$price = $option['price'];
					}
				}
			}
		}
		
		return number_format(floatval($price), 2);
	}
	
	public function GetProductOption($product, $optionId)
	{
		$optionValue = '';
		
		if (!empty($product->options))
		{
			foreach ($product->options as $option)
			{
				if ($option['id'] == $optionId)
				{
					$optionValue = $option['name'];
					break;
				}
			}
		}
		
		return $optionValue;
	}
	
	public function GetProductMeta(&$product, $productId, $imageSize)
	{
		$soldOut = true;
		$optionsSoldOut = true;
			
		// Get product options
		$optionsSql = "SELECT * FROM wp_postmeta WHERE post_id = $productId AND meta_key LIKE 'cartly_option_%' AND meta_key != 'cartly_option_deleted'";
		$options = Data::GetResultsObject($optionsSql);
		
		foreach ($options as $option)
		{
			$values = explode(CARTLY_DELIMITER, $option->meta_value);
			
			if (!empty($values))
			{
				$product->options[] = array(
					'id' => $option->meta_id,
					'name' => $values[0],
					'price' => $values[1],
					'shipping' => $values[2],
					'quantity' => $values[3],
					'sold_out' => $values[3] == 0 ? true : false
				);
				
				if ($values[3] > 0)
				{
					$optionsSoldOut = false;
				}
			}
		}
		
		// Get post title if we don't have one
		if (empty($product->post_title))
		{
			$product->post_title = get_the_title($productId);
		}
		
		// Determine if the product is sold out
		if (empty($options))
		{
			if ($product->quantity > 0)
			{
				$soldOut = false;
			}
		}
		else
		{
			if ($product->quantity > 0)
			{
				$soldOut = false;
			}
			
			$product->options_sold_out = $optionsSoldOut;
		}
		
		// Determine if we care about sold out products
		$removeAddToCart = get_option('cartly_remove_add_to_cart');
		
		if (!empty($removeAddToCart))
		{
			$product->sold_out = $soldOut;
		}
		else
		{
			$product->sold_out = false;
		}
		
		// Get Product Images
		$image = wp_get_attachment_image_src(get_post_thumbnail_id($productId), $imageSize);
		
		if (!empty($image))
		{
			$product->featured_image = $image[0];
		}
		
		$imageThumb = wp_get_attachment_image_src(get_post_thumbnail_id($productId), 'thumbnail');
		
		if (!empty($imageThumb))
		{
			$product->featured_thumb = $imageThumb[0];
		}
	}
	
	public function GetOrder($orderId)
	{	
		$sql = "SELECT o.id, o.create_date, o.subtotal, o.shipping, o.tax, o.total, s.name AS status, (SELECT COUNT(id) FROM cartly_order_item WHERE order_id = o.id) AS items, (SELECT GROUP_CONCAT(product_id) FROM cartly_order_item WHERE order_id = o.id) AS item_ids, (SELECT GROUP_CONCAT(option_id) FROM cartly_order_item WHERE order_id = o.id) AS option_ids, (SELECT GROUP_CONCAT(quantity) FROM cartly_order_item WHERE order_id = o.id) AS item_quantities, u.full_name AS name, u.email, a.full_name AS shipping_name, a.address_1, a.address_2, a.city, a.state_region, a.zip, c.name AS country FROM cartly_order o JOIN cartly_order_status s ON o.status_id = s.id JOIN cartly_user u ON o.user_id = u.id JOIN cartly_address a ON o.id = a.order_id JOIN cartly_country c ON a.country_id = c.code WHERE o.id = $orderId";
		
		$order = Data::GetResultsObject($sql);
		
		return $order != false ? $order[0] : null;
	}
	
	public function GetOrderItems($orderId)
	{	
		$sql = "SELECT * FROM cartly_order_item WHERE order_id = $orderId";
		
		$items = Data::GetResultsObject($sql);
		
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$productOptionValue = '';
				
				if (!empty($item->option_id))
				{
					$productOption = $this->GetProductOptionDb($item->product_id, $item->option_id);
					$productOptionPieces = explode(CARTLY_DELIMITER, $productOption[0]->meta_value);
					$productOptionValue = $productOptionPieces[0];
				}
				
				$item->option_value = $productOptionValue;
			}
		}
		
		return $items;
	}
	
	public function GetProductOptionDb($productId, $optionId)
	{	
		$sql = "SELECT * FROM wp_postmeta WHERE post_id = $productId AND meta_id = $optionId";
		
		$option = Data::GetResultsObject($sql);
		
		return $option != false ? $option : null;
	}
	
	public function CheckCartProducts(&$cart)
	{
		$itemRemoved = false;
		$products = $cart->products;
		$productIndex = 0;
		
		foreach ($cart->products as $cartProduct)
		{
			$storeProduct = $this->GetProducts('publish', '', '', '', array($cartProduct->product_id));
			
			// Product no longer exists or isn't published
			if (empty($storeProduct))
			{
				unset($products[$productIndex]);
				$cart->RemoveFromCart($cartProduct->product_id, $cartProduct->option_id);
				$itemRemoved = true;
			}
			
			// Product with an option that no longer exists
			if ($cartProduct->option_id == 0 && isset($storeProduct->options))
			{
				unset($products[$productIndex]);
				$cart->RemoveFromCart($cartProduct->product_id, $cartProduct->option_id);
				$itemRemoved = true;
			}
			else if ($cartProduct->option_id != 0)
			{
				$foundOption = false;
				
				if (isset($storeProduct->options))
				{
					foreach ($storeProduct->options as $option)
					{
						if ($cartProduct->option_id == $option['id'])
						{
							$foundOption = true;
							break;	
						}
					}
					
					if (!$foundOption)
					{
						unset($products[$productIndex]);
						$cart->RemoveFromCart($cartProduct->product_id, $cartProduct->option_id);
						$itemRemoved = true;
					}
				}
			}
			
			$productIndex++;
		}
		
		$cart->products = $products;
		
		return $itemRemoved;
	}
	
	public function PlaceOrder(&$message, &$code, &$extra, &$guest)
	{
		$cart = new Cart();
	
		if (count($cart->products) == 0)
		{
			$message = 'Cart corrupted. Please fill cart and checkout again';
			return false;
		}
		
		$useStripe = get_option('cartly_payment_use_stripe');
		$usingStripe = !empty($useStripe) && !empty($_POST['stripeToken']);
		
		$paymentGateway = $usingStripe == true ? 1 : 0;
		
		$removeFromCart = get_option('cartly_remove_add_to_cart');
		
		if (!empty($removeFromCart))
		{
			$overSoldProducts = array();
			
			foreach($cart->products as $cartProduct)
			{
				$storeProduct = $this->GetProducts('publish', 'large', 'post_date', NULL, array($cartProduct->product_id));
				
				if (empty($storeProduct))
				{
					$overSoldProducts[] = array(
						'id' => $cartProduct->product_id,
						'quantity' => 0);
				}
				else if (!empty($storeProduct->options))
				{
					foreach ($storeProduct->options as $option)
					{
						if ($option['id'] == $cartProduct->option_id)
						{
							if ($cartProduct->quantity > $option['quantity'])
							{
								$overSoldProducts[] = array(
									'id' => $cartProduct->product_id,
									'quantity' => $option['quantity']);
							}
						}
					}
				}
				else
				{
					if ($cartProduct->quantity > $storeProduct->quantity)
					{
						$overSoldProducts[] = array(
							'id' => $cartProduct->product_id,
							'quantity' => $storeProduct->quantity);
					}
				}
			}
			
			if (!empty($overSoldProducts))
			{
				$code = 200;
				$message = 'You have a product error, please fix and place your order again';
				$extra = $overSoldProducts;
				return false;
			}
		}
		
		$subtotal = $cart->cartTotal;
		$shipping = $cart->cartShipping;
		$tax = $cart->cartTax;
		
		$total = $subtotal + $shipping + $tax;
		
		$account = new Account();
		
		if ($account->IsAuthorized())
		{
			$user = $account->GetUser();
			$userId = $account->GetUserId();
			
			$chargedName = $user->full_name;
			$chargedEmail = $user->email;
		}
		else if ($account->EmailExists($_POST['shipping']['email']))
		{
			$message = 'Account exists, please log in and then checkout';
			return false;
		}
		else
		{
			// If account password isn't set, this is a guest checkout.
			$password = !empty($_POST['account']['password']) ? $_POST['account']['password'] : '';
			$guest = empty($_POST['account']['password']) ? true : false;
			
			// Create User
			$userId = $account->CreateAccount(
				$_POST['shipping']['full_name'],
				$_POST['shipping']['email'],
				$password);
			
			// If user is not a guest, log them in
			if (!$guest)
			{
				$account->LogIn($_POST['shipping']['email'], $password, $message);
			}
			
			$chargedName = $_POST['shipping']['full_name'];
			$chargedEmail = $_POST['shipping']['email'];
		}
		
		// Create order and order items
		$orderId = Order::CreateOrder($userId, $cart->products, $paymentGateway, $subtotal, $shipping, $tax, $total);
		
		// Adjust inventory if we are supposed to
		$trackInventory = get_option('cartly_track_inventory');
		
		if (!empty($trackInventory))
		{
			foreach ($cart->products as $product)
			{
				if ($product->option_id != 0)
				{
					$option = $this->GetProductOptionDb($product->product_id, $product->option_id);
					$values = explode(CARTLY_DELIMITER, $option[0]->meta_value);
					
					if (isset($values))
					{
						if (isset($values[3]))
						{
							$quantity = (int) $values[3];
							$values[3] = $quantity - $product->quantity;
							update_post_meta($product->product_id, $option[0]->meta_key, implode(CARTLY_DELIMITER, $values));
						}
					}
				}
				else
				{
					$quantity = (int) get_post_meta($product->product_id, 'quantity', true);
					update_post_meta($product->product_id, 'quantity', $quantity - $product->quantity);
				}
			}
		}
		
		// Create Shipping Address
		$account->CreateAddress(
			$userId,
			$orderId,
			$_POST['shipping']['full_name'],
			false,
			true,
			$_POST['shipping']['address_1'],
			$_POST['shipping']['address_2'],
			$_POST['shipping']['city'],
			$_POST['shipping']['state_region'],
			$_POST['shipping']['zip'],
			$_POST['shipping']['country_id'],
			$_POST['shipping']['phone']
		);
		
		// Create Billing Address
		if (!isset($_POST['same_address']))
		{
			$account->CreateAddress(
				$userId,
				$orderId,
				$_POST['billing']['full_name'],
				true,
				false,
				$_POST['billing']['address_1'],
				$_POST['billing']['address_2'],
				$_POST['billing']['city'],
				$_POST['billing']['state_region'],
				$_POST['billing']['zip'],
				$_POST['billing']['country_id'],
				$_POST['billing']['phone']
			);
			
			$chargedName = $_POST['billing']['full_name'];
		}
		
		// Charge for the Order
		$stripeApiKey = 'sk_test_OL7XYxiqQK6Nk7dBH4jgC7UC'; // Test Key
		$stripeLiveApiKey = get_option('cartly_payment_stripe_live_secret_key');
		$stripeIsLive = get_option('cartly_payment_stripe_live');
		
		if ($usingStripe)
		{
			if ($stripeLiveApiKey && $stripeIsLive)
			{
				$stripeApiKey = $stripeLiveApiKey;
			}
			
			// TODO: Should this go before any DB inserts?
			$payment = new Payment($stripeApiKey);
			$payment->Charge($total, $chargedName, $chargedEmail, $_POST['stripeToken'], $userId);	
		}
		
		// Send Order Emails
		Email::SendOrderEmails($orderId, $chargedEmail);
		
		$cart->EmptyCart();
		
		return true;
	}
}

?>