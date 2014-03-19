<?php

include_once(sprintf('cartly-database.php', dirname(__FILE__)));

class CartlyData
{	
	// Public Methods
	public function GetOrders()
	{	
		$sql = "SELECT o.id, o.create_date, o.total, s.name AS status, s.id AS status_id, (SELECT COUNT(id) FROM cartly_order_item WHERE order_id = o.id) AS items, (SELECT GROUP_CONCAT(product_id) FROM cartly_order_item WHERE order_id = o.id) AS item_ids, (SELECT GROUP_CONCAT(option_id) FROM cartly_order_item WHERE order_id = o.id) AS option_ids, (SELECT GROUP_CONCAT(quantity) FROM cartly_order_item WHERE order_id = o.id) AS item_quantities, u.full_name AS name FROM cartly_order o JOIN cartly_order_status s ON o.status_id = s.id JOIN cartly_user u ON o.user_id = u.id ORDER BY o.create_date DESC";
		
		$orders = Database::GetResults($sql);
		
		return $orders;
	}
	
	public function GetOrder($orderId)
	{	
		$sql = "SELECT o.id, o.create_date, o.subtotal, o.shipping, o.tax, o.total, s.name AS status, (SELECT COUNT(id) FROM cartly_order_item WHERE order_id = o.id) AS items, (SELECT GROUP_CONCAT(product_id) FROM cartly_order_item WHERE order_id = o.id) AS item_ids, (SELECT GROUP_CONCAT(option_id) FROM cartly_order_item WHERE order_id = o.id) AS option_ids, (SELECT GROUP_CONCAT(quantity) FROM cartly_order_item WHERE order_id = o.id) AS item_quantities, u.full_name AS name, u.email, a.full_name AS shipping_name, a.address_1, a.address_2, a.city, a.state_region, a.zip, c.name AS country FROM cartly_order o JOIN cartly_order_status s ON o.status_id = s.id JOIN cartly_user u ON o.user_id = u.id JOIN cartly_address a ON o.id = a.order_id JOIN cartly_country c ON a.country_id = c.code WHERE o.id = $orderId";
		
		$order = Database::GetResults($sql);
		
		return $order != false ? $order[0] : null;
	}
	
	public function GetOrderItems($orderId)
	{	
		$sql = "SELECT * FROM cartly_order_item WHERE order_id = $orderId";
		
		$items = Database::GetResults($sql);
		
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$productOptionValue = '';
				
				if (!empty($item->option_id))
				{
					$productOption = $this->GetProductOption($item->product_id, $item->option_id);
					$productOptionPieces = explode(CARTLY_DELIMITER, $productOption[0]->meta_value);
					$productOptionValue = $productOptionPieces[0];
				}
				
				$item->option_value = $productOptionValue;
			}
		}
		
		return $items;
	}
	
	public function GetOptionSets()
	{
		$sql = "SELECT * FROM cartly_option_set ORDER BY id DESC";
		
		$sets = Database::GetResultsObject($sql);
		
		return $sets != false ? $sets : null;
	}
	
	public function GetOptionSetOptions($setId)
	{
		$sql = "SELECT * FROM cartly_option_set_option WHERE set_id = $setId";
		
		$options = Database::GetResultsObject($sql);
		
		return $options != false ? $options : null;
	}
	
	public function AddOptionSet($name)
	{
		return Database::Insert(
			'cartly_option_set',
			array('name' => $name));
	}
	
	public function AddOptionSetOption($setId, $name, $price, $shipping, $quantity)
	{
		$data = array(
			'set_id' => $setId,
			'name' => $name
		);
		
		$price != '' ? $data['price'] = $price : '';
		$shipping != '' ? $data['shipping'] = $shipping : '';
		$quantity != '' ? $data['quantity'] = $quantity : '';
		
		return Database::Insert(
			'cartly_option_set_option',
			$data
		);
	}
	
	public function DeleteOptionSet($setId)
	{
		$sql = "DELETE FROM cartly_option_set WHERE id = $setId";
		
		return Database::Delete($sql);
	}
	
	public function DeleteOptionSetOption($optionId)
	{
		$sql = "DELETE FROM cartly_option_set_option WHERE id = $optionId";
		
		return Database::Delete($sql);
	}
	
	public function GetPreviousOrderId($orderId)
	{
		$ordersIndex = 0;
		$orders = $this->GetOrders();
		
		foreach ($orders as $order)
		{
			if ($order->id == $orderId && $order != end($orders))
			{
				return $orders[$ordersIndex + 1];
			}
			
			$ordersIndex++;
		}
		
		return null;
	}
	
	public function GetNextOrderId($orderId)
	{
		$ordersIndex = 0;
		$orders = $this->GetOrders();
		
		foreach ($orders as $order)
		{
			if ($order->id == $orderId && $ordersIndex != 0)
			{
				return $orders[$ordersIndex - 1];
			}
			
			$ordersIndex++;
		}
		
		return null;
	}
	
	public function GetOrderStatuses()
	{	
		$sql = "SELECT * FROM cartly_order_status";
		
		$statuses = Database::GetResults($sql);
		
		return $statuses != false ? $statuses : null;
	}
	
	public function GetStatusOfOrders()
	{	
		$sql = "SELECT o.status_id, COUNT(*) AS count, s.name FROM cartly_order o JOIN cartly_order_status s ON s.id = o.status_id GROUP BY o.status_id";
		
		$statuses = Database::GetResults($sql);
		
		return $statuses != false ? $statuses : null;
	}
	
	public function UpdateOrderStatus($orderId, $statusId)
	{	
		return Database::Update('cartly_order', array('status_id' => $statusId), array('id' => $orderId));
	}
	
	public function GetProductOption($productId, $optionId)
	{	
		$sql = "SELECT * FROM wp_postmeta WHERE post_id = $productId AND meta_id = $optionId";
		
		$option = Database::GetResults($sql);
		
		return $option != false ? $option : null;
	}
	
	public function GetProductOptions($productId)
	{	
		$sql = "SELECT * FROM wp_postmeta WHERE post_id = $productId AND meta_key LIKE 'cartly_option_%' AND meta_key != 'cartly_option_deleted'";
		
		$statuses = Database::GetResults($sql);
		
		return $statuses != false ? $statuses : null;
	}
	
	public function DeleteProductOption($productId, $optionId)
	{
		return Database::Update('wp_postmeta', array('meta_key' => 'cartly_option_deleted'), array('post_id' => $productId, 'meta_key' => $optionId));
	}
	
	public function GetProductOptionSet($setId)
	{
		$sql = "SELECT * FROM cartly_option_set_option WHERE set_id = $setId ORDER BY id ASC";
		
		return Database::GetResultsObject($sql);
	}
}

?>