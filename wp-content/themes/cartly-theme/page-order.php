<?php
/*
TEMPLATE NAME: Order
*/

if (!$account->IsAuthorized())
{
	$url = get_permalink($loginPage->ID).'?redirect='.urlencode(get_permalink($post->ID));
	wp_redirect($url);
	exit;
}
else
{
	$user = $account->GetUser();
	$orders = $account->GetOrders();
	$tokens = explode('/', $_SERVER['REQUEST_URI']);
	$orderNumber = $tokens[2];
	
	$order = $store->GetOrder(Utilities::GetOrderId($orderNumber));
	$orderItems = $store->GetOrderItems(Utilities::GetOrderId($orderNumber));
}


?>

<?php get_header(); ?>
<?php if (have_posts()) while (have_posts()) : the_post(); ?>

<section class="order page-content">

	<div class="tame">
		
		<div class="account-header">
		
			<h2>You are logged in as <?php echo $user->email ?></h2>
			<h3>Order #<?php echo $orderNumber; ?></h3>
			
			<?php if (empty($order)) : ?>
			
			<p>Order not found</p>
			
			<?php else : ?>
			
			<div class="shopping-cart">
			
				<div class="row header clear">
					<div class="product-info"><label>Product</label></div>
					<div class="product-price"><label>Item Price</label></div>
					<div class="product-quantity"><label>Quantity</label></div>
					<div class="product-total-price"><label>Price</label></div>
				</div>
				
				<?php
				
				foreach($orderItems as $orderItem) :
				$image = wp_get_attachment_image_src(get_post_thumbnail_id($orderItem->product_id), 'thumbnail');
				$product = get_post($orderItem->product_id);
				
				?>

				<div class="product row clear">
					<div class="product-info clear">
						<div class="product-image">
							<a href="<?php echo get_permalink($orderItem->product_id) ?>">
								<?php if (!empty($image[0])) : ?>				
								<img src="<?php echo $image[0] ?>" alt="<?php echo $product->post_title ?>" title="<?php echo $product->post_title ?>" />
								<?php endif; ?>
							</a>
						</div>
						<div class="product-meta">
							<h4><a href="<?php echo get_permalink($product->ID) ?>"><?php echo $product->post_title ?></a></h4>
							<?php if (!empty($orderItem->option_value)) : ?>
							<h5><?php echo $orderItem->option_value ?></h5>
							<?php endif; ?>		
						</div>
					</div>
					<div class="product-price">
							<p><?php echo Utilities::PrintMoney($orderItem->total) ?></p>
					</div>
					<div class="product-quantity">
						<p><?php echo $orderItem->quantity; ?></p>
					</div>
					<div class="product-total-price" data-product-id="<?php echo $product->ID ?>">				
						<p><?php echo Utilities::PrintMoney($orderItem->total * $orderItem->quantity) ?></p>
					</div>
				</div>
				<?php endforeach; ?>
				<div class="row footer clear">
					<div class="left">
						<div class="ship-to">
							<h4>Ship To:</h4>
							<p><?php echo $order->shipping_name ?></p>
							<p><?php echo $order->address_1 ?></p>
							<p><?php echo $order->address_2 ?></p>
							<p><?php echo $order->city ?>, <?php echo $order->state_region ?></p>
							<p><?php echo $order->zip ?></p>
							<p><?php echo $order->country ?></p>
						</div>
					</div>
					<div class="right">
						<table class="totals-table">
							<tr>
								<?php if (count($orderItems) == 1) : ?>
								<td colspan="4" class="accounting">Subtotal (1 Item):</td>
								<?php else: ?>
								<td colspan="4" class="accounting">Subtotal (<span class="item-count"><?php echo count($orderItems) ?></span> Items):</td>
								<?php endif; ?>
								<td class="subtotal"><?php echo Utilities::PrintMoney($order->subtotal) ?></td>
							</tr>
							<tr>
								<td colspan="4" class="accounting">Shipping:</td>
								<td class="shipping"><?php echo Utilities::PrintMoney($order->shipping) ?></td>
							</tr>
							<tr>
								<td colspan="4" class="accounting">Tax:</td>
								<td><?php echo Utilities::PrintMoney($order->tax); ?></td>
							</tr>
							<tr>
								<td colspan="4" class="accounting total">Grand Total:</td>
								<td class="grand total"><?php echo Utilities::PrintMoney($order->total) ?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			
			<?php endif; ?>
			
		</div>
	
	</div>

</section>

<?php endwhile; ?>
<?php get_footer(); ?>
