<?php get_header(); ?>
<?php if (have_posts()) while (have_posts()) : the_post(); ?>

<?php

$product = $store->GetProducts('publish', 'large', 'post_date', NULL, array($post->ID));

$productImages = get_posts(array(
	'post_type' => 'attachment',
	'numberposts' => -1,
	'post_parent' => $post->ID,
	'post_mime_type' => 'image',
	'orderby' => 'menu_order',
	'order' => 'ASC'
));

?>

<section class="page-content tame clear">

	<?php if (!empty($product)) : ?>
	
	<article class="product large span16 clear">
		<div class="span8 clear product-images">
			<?php if (!empty($product->featured_image)) : ?>			
			<img src="<?php echo $product->featured_image ?>" alt="<?php echo $product->post_title ?>" title="<?php echo $product->post_title ?>" class="featured" />
			<?php endif; ?>
			<div class="thumbs clear">
				<?php
				
				foreach($productImages as $productImage) :
				
				$full = wp_get_attachment_image_src($productImage->ID, 'full-size');
				$thumb = wp_get_attachment_image_src($productImage->ID, 'thumbnail');
				$imageInfo = get_post($productImage->ID);
				
				?>
				<a href="<?php echo $full[0]; ?>" class="thumb-link">
					<img src="<?php echo $thumb[0] ?>" alt="<?php echo $imageInfo->post_title ?>" title="<?php echo $imageInfo->post_title ?>" class="thumb" />
				</a>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="span8 clear">
		
			<form method="post" action="#" class="product-form">
			
			<footer class="product-footer">
			
				<?php if (!empty($product->post_title)) : ?>
				<h2><?php echo $product->post_title ?></h2>
				<?php endif; ?>
				
				<?php echo wpautop($product->post_content) ?>
				
				<?php if (!empty($product->price)) : ?>
					<?php if (!empty($product->on_sale)) : ?>
					<h5><?php echo Utilities::PrintMoney($product->price) ?> <br /><span class="alert">Sale!</span></h5>
					<?php else : ?>
					<h5><?php echo Utilities::PrintMoney($product->price) ?></h5>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php $removeFromCart = get_option('cartly_remove_add_to_cart'); ?>
								
				<?php if ($product->sold_out === true) : ?>
				
					<?php if (!empty($removeFromCart)) : ?>
					<p class="message"><em>We're sorry, but this product is currently sold out.</em></p>
					<?php endif; ?>
				
				<?php else : ?>
				
				<?php if (!empty($product->options) && !$product->options_sold_out) : ?>
				<div class="product-options clear">
					<select name="product_options">
						<?php foreach($product->options as $option) : ?>
						<?php if ($option['quantity'] > 0) : ?>
						<option value="<?php echo $option['id'] ?>"><?php echo $option['name'] ?> &ndash; <?php echo $option['price'] ?></option>
						<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
				<?php endif; ?>
				
				<div class="quantity-box">
					<input type="text" name="product_quantity" value="1" /> <label>quantity</label>
				</div>
				
				<button class="add-to-cart" data-product-id="<?php echo $product->ID ?>">Add to Cart</button>
				
				<p class="message add-to-cart-message initial-hide">Item added to cart. You can <a href="<?php echo get_permalink($cartPage->ID) ?>">checkout</a> if you are ready.</p>
				
				<?php endif; ?>
				
			</footer>
			
			</form>
			
		</div>
	</article>
		
	<?php else : ?>
	
	<h2>Product Not Found</h2>
	
	<?php endif; ?>

</section>

<?php endwhile; ?>
<?php get_footer(); ?>