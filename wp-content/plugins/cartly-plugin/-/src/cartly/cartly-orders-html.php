<?php

$data = new CartlyData();
$orders = $data->GetOrders();
$orderStatuses = $data->GetOrderStatuses();
$statuses = $data->GetStatusOfOrders();
$orderIndex = $orderStatusIndex = 1;

?>
<div class="cartly-admin cartly-orders wrap">
	<div class="header clear">
		<div id="icon-options-general" class="icon32 icon-sprocket"><br></div>
		<h2>Orders</h2>
		<?php if (count($orders) == 1) : ?>
		<h3>1 Order</h3>
		<?php else : ?>
		<h3><?php echo count($orders) ?> Orders</h3>
		<?php endif; ?>
	</div>
	
	<?php if (count($orders) > 0) : ?>
	
	<table class="status-table cartly-table" cellpadding="0" cellspacing="0">
		<tr class="header">
			<th>Count</th>
			<th>Status</th>
		</tr>
		<?php foreach($orderStatuses as $orderStatus) : ?>
		<tr class="status <?php echo strtolower($orderStatus->name) ?>  <?php echo $orderStatusIndex % 2 == 0 ? 'even' : 'odd' ?>">
			<?php
			foreach($statuses as $status)
			{
				$statusFound = false;
				if ($status->status_id == $orderStatus->id)
				{
					$statusFound = true;
					break;
				}
			}
			?>
			<?php if ($statusFound) : $orderStatusIndex++; ?>
			<td data-status-id="<?php echo $orderStatus->id; ?>"><?php echo $status->count; ?></td>
			<?php else : ?>
			<td data-status-id="<?php echo $orderStatus->id; ?>">0</td>
			<?php endif; ?>
			<td><?php echo $orderStatus->name ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<table class="orders-table cartly-table" cellpadding="0" cellspacing="0">
		<tr class="header">
			<th>Name</th>
			<th>Order #</th>
			<th>Date</th>
			<th>Items</th>
			<th>Total</th>
			<th>Status</th>
		</tr>
		<?php foreach($orders as $order) : ?>
		<tr class="order <?php echo $orderIndex % 2 == 0 ? 'even' : 'odd' ?>" data-id="<?php echo $order->id ?>" data-status-id="<?php echo strtolower($order->status_id) ?>">
			<td class="name"><?php echo $order->name ?></td>
			<td class="order-number"><a href="<?php echo CartlyUtilities::GetURL() ?>&amp;order_id=<?php echo $order->id ?>"><?php echo CartlyUtilities::GetOrderNumber($order->id) ?></a></td>
			<td class="date"><?php echo mysql2date('M d, Y h:i:s a', $order->create_date) ?></td>
			<td class="items"><?php echo $order->items ?></td>
			<td class="total">$<?php echo $order->total ?></td>
			<td class="status">
				<div class="status-meta">
					<span><?php echo $order->status ?></span> <a href="#" class="change-order">Change</a>
				</div>
			</td>
		</tr>
		<?php $orderIndex++; endforeach; ?>
	</table>
	<div class="order-controls template initial-hide">
		<select name="status">
			<?php foreach($orderStatuses as $orderStatus) : ?>
			<option value="<?php echo $orderStatus->id ?>"><?php echo $orderStatus->name ?></option>
			<?php endforeach; ?>
		</select>
		<a href="#" class="save">Save</a>
		<a href="#" class="cancel">Cancel</a>
	</div>
	
	<?php endif; ?>
	
</div>