<?php

require_once('Utilities.php');
require_once('Store.php');

class Order
{
	// Public Methods
	public static function CreateOrder($userId, $orderItems, $paymentGateway, $subtotal, $shipping, $tax, $total)
	{
		Utilities::IncludeWPConfig();
		
		global $wpdb;
		$store = new Store();
		$products = $orderItems;
		
		// Create Order
		$wpdb->insert('cartly_order', array(
			'create_date' => date('Y-m-d H:i:s'),
			'user_id' => $userId,
			'status_id' => 1,
			'payment_gateway_id' => $paymentGateway,
			'subtotal' => str_replace(',', '', $subtotal),
			'shipping' => str_replace(',', '', $shipping),
			'tax' => str_replace(',', '', $tax),
			'total' => str_replace(',', '', $total)));
		
		$orderId = $wpdb->insert_id;
		
		// Create Order Items
		$values = array();
		$query = 'INSERT INTO cartly_order_item (order_id, product_id, option_id, quantity, total) VALUES ';
		
		foreach($orderItems as $orderItem)
		{	
			$amount = $store->GetProductTotal($orderItem->product_id, $orderItem->option_id, $orderItem->quantity);
			$values[] = "($orderId, $orderItem->product_id, $orderItem->option_id, $orderItem->quantity, $amount)";
		}
		
		$query .= implode(', ', $values);
		
		$wpdb->query($wpdb->prepare("$query ", $values));
		
		return $orderId;
	}
}

?>