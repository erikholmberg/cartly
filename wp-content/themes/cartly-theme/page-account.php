<?php
/*
TEMPLATE NAME: Account
*/

if (!$account->IsAuthorized())
{
	$url = get_permalink($loginPage->ID).'?redirect='.urlencode(get_permalink($post->ID));
	wp_redirect($url);
	exit;
}
else
{
	if ($cartlyPluginActive)
	{
		$user = $account->GetUser();
		$orders = $account->GetOrders();
	}
}

?>

<?php get_header(); ?>
<?php if (have_posts()) while (have_posts()) : the_post(); ?>

<section class="account page-content">

	<div class="tame">
		
		<div class="account-header">
		
			<?php if (!empty($user)) : ?>
		
				<h2>You are logged in as <?php echo $user->email ?></h2>
				<h3>You have placed <?php echo count($orders) ?> order<?php echo count($orders) == 1 ? '' : 's' ?></h3>
				
				<?php if (empty($orders)) : ?>
				
				<p>Head to <a href="<?php echo get_bloginfo('url') ?>">the shop</a> and treat yourself!</p>
				
				<?php else : ?>
				
				<div class="orders">
					<div class="row header clear">
						<div class="date"><label>Order Date</label></div>
						<div class="order-number"><label>Order #</label></div>
						<div class="items"><label>Items</label></div>
						<div class="total"><label>Total</label></div>
						<div class="status"><label>Status</label></div>
						<div class="images"><label class="mobile-hide">Products</label></div>
					</div>
					<?php
					
					foreach($orders as $order) :
					
					$orderNumber = Utilities::GetOrderNumber($order->id);
						
					?>
					<div class="order row clear">
						<div class="date"><p><?php echo mysql2date('M j, Y', $order->create_date) ?></p></div>
						<div class="order-number"><p><a href="<?php echo get_permalink($orderPage->ID) ?><?php echo $orderNumber ?>"><?php echo $orderNumber ?></a></p></div>
						<div class="items"><p><?php echo $order->items ?></p></div>
						<div class="total"><p><?php echo Utilities::PrintMoney($order->total) ?></p></div>
						<div class="status"><p><?php echo $order->status ?></p></div>
						<div class="images">
							<?php foreach(explode(',', $order->item_ids) as $item_id) : ?>
								<?php
								$image = wp_get_attachment_image_src(get_post_thumbnail_id($item_id), 'thumbnail');
								$image_info = get_post($item_id);
								?>
								<?php if ($image != false) : ?>				
								<img src="<?php echo $image[0] ?>" alt="<?php echo $image_info->post_title ?>" title="<?php echo $image_info->post_excerpt ?>" />
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				
				<?php endif; ?>
			
			<?php else : ?>
			
			<h2>User Not Found</h2>
			
			<?php endif; ?>
			
		</div>
	
	</div>

</section>

<?php endwhile; ?>
<?php get_footer(); ?>
