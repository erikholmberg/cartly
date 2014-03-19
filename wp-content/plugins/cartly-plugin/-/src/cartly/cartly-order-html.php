<?php

$data = new CartlyData();
$order = $data->GetOrder($_GET['order_id']);
$orderItems = $data->GetOrderItems($_GET['order_id']);
$orderItemCount = 0;
$orderItemIndex = 1;

$previousOrderId = $data->GetPreviousOrderId($_GET['order_id']);
$nextOrderId = $data->GetNextOrderId($_GET['order_id']);

$orderNumber = isset($order->id) ? '#1000'.$order->id : '';

?>

<div class="cartly-admin cartly-order wrap">

	<?php if (isset($order)) : ?>
	
	<div class="header clear">
		<div class="controls">
			<?php if (isset($previousOrderId)) : ?>
			<a href="<?php echo CartlyUtilities::GetBaseURL() ?>&amp;order_id=<?php echo $previousOrderId->id ?>" class="previous  add-new-h2">Older</a>
			<?php endif; ?>
			<?php if (isset($nextOrderId)) : ?>
			<a href="<?php echo CartlyUtilities::GetBaseURL() ?>&amp;order_id=<?php echo $nextOrderId->id ?>" class="next  add-new-h2">Newer</a>
			<?php endif; ?>
			<a href="javascript:window.print();" class="print-link">Print</a>
		</div>
		<h2>Order <?php echo $orderNumber ?></h2>
		<h3 class="<?php echo strtolower($order->status) ?>"><?php echo $order->status ?></h3>
	</div>
	
	<div class="order-box">
		<div class="top products clear">
			<div class="products-header clear">
			
				<div class="product-info">
					<p>Product</p>
				</div>
				<div class="product-price">
					<p>Item Price</p>
				</div>
				<div class="product-quantity">
					<p>Quantity</p>
				</div>
				<div class="product-total">
					<p>Total</p>
				</div>
			
			</div>
			
			<?php foreach($orderItems as $orderItem) : ?>
			
			<div class="product row clear <?php echo $orderItemIndex % 2 == 0 ? 'even' : 'odd' ?>">
				<div class="product-info">
					<?php
					$image = wp_get_attachment_image_src(get_post_thumbnail_id($orderItem->product_id), 'thumbnail');
					$image_info = get_post($orderItem->product_id);
					?>
					<?php if ($image != false) : ?>		
					<div class="product-image">
						<a href="<?php echo get_permalink($orderItem->product_id) ?>">
							<img src="<?php echo $image[0] ?>" alt="<?php echo $image_info->post_title ?>" title="<?php echo $image_info->post_excerpt ?>" />
						</a>
					</div>
					<?php endif; ?>		
					<div class="product-meta">
						<h2><a href="<?php echo get_permalink($orderItem->product_id) ?>"><?php echo $image_info->post_title ?></a></h2>
						<h3><?php echo $orderItem->option_value ?></h3>
					</div>
				</div>
				<div class="product-price">
					<h2><?php echo CartlyUtilities::PrintMoney($orderItem->total / $orderItem->quantity) ?></h2>
				</div>
				<div class="product-quantity">
					<h2><?php echo $orderItem->quantity ?></h2>
				</div>
				<div class="product-total">
					<h2><?php echo CartlyUtilities::PrintMoney($orderItem->total) ?></h2>
				</div>
			</div>
			<?php $orderItemCount += $orderItem->quantity; $orderItemIndex++; endforeach; ?>
		</div>
		<div class="bottom clear">
			<div class="left">
				<h3>Ship To:</h3>
				<p><?php echo $order->shipping_name ?></p>
				<p><?php echo $order->address_1 ?></p>
				<p><?php echo $order->address_2 ?></p>
				<p><?php echo $order->city ?>, <?php echo $order->state_region ?></p>
				<p><?php echo $order->zip ?></p>
				<p><?php echo $order->country ?></p>
			</div>
			<div class="right">
				<h3><?php echo $order->name ?></h3>
				<p><a href="mailto:<?php echo $order->email ?>?subject=Order <?php echo $orderNumber ?> from <?php echo get_bloginfo('title') ?>"><?php echo $order->email ?></a></p>
				<div class="order-total">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<?php if ($orderItemCount == 1) : ?>
							<td class="accounting">Subtotal (1 Item):</td>
							<?php else: ?>
							<td class="accounting">Subtotal (<span class="item-count"><?php echo $orderItemCount; ?></span> Items):</td>
							<?php endif; ?>
							<td class="subtotal"><?php echo CartlyUtilities::PrintMoney($order->subtotal) ?></td>
						</tr>
						<tr>
							<td class="accounting">Shipping:</td>
							<td><?php echo CartlyUtilities::PrintMoney($order->shipping) ?></td>
						</tr>
						<tr>
							<td class="accounting">Tax:</td>
							<td><?php echo CartlyUtilities::PrintMoney($order->tax) ?></td>
						</tr>
						<tr>
							<td class="accounting total"><strong>Grand Total:</strong></td>
							<td class="grand total"><strong><?php echo CartlyUtilities::PrintMoney($order->total) ?></strong></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<?php else : ?>
	<h2>Order Not Found</h2>
	<?php endif; ?>
	
</div>