<?php
/*
TEMPLATE NAME: Home
*/
?>

<?php get_header(); ?>
<?php if (have_posts()) while (have_posts()) : the_post(); ?>

<section class="hero tame">
	<h2><?php echo $post->post_content; ?></h2>
</section>

<?php $categories = get_categories(array('type' => 'products')); ?>

<?php if (!empty($categories)) : ?>

<section class="products tame clear">

	<?php foreach($categories as $category) : ?>
		<?php if ($category->parent == 0) : ?>
			<h2><?php echo $category->name; ?></h2>
			<?php $subCategories = get_categories(array('type' => 'products', 'parent' => $category->term_id)); ?>
			<?php foreach($subCategories as $subCategory) : ?>
			
				<section class="product-category clear">
				
					<h3><?php echo $subCategory->name; ?></h3>
					<?php $products = $store->GetProducts('publish', 'large', 'post_date', 'DESC', NULL, $subCategory->term_id); ?>
					
					<?php foreach($products as $product) : ?>
			
					<article class="product small span4">
						<?php if (!empty($product->featured_image)) : ?>						
						<a href="<?php echo get_permalink($product->ID) ?>" class="featured">
							<img src="<?php echo $product->featured_image ?>" alt="<?php echo $product->post_title ?>" title="<?php echo $product->post_title ?>" />
						</a>
						<?php endif; ?>
						<footer class="product-footer">
							
							<?php if (!empty($product->post_title)) : ?>
							<h2><a href="<?php echo get_permalink($product->ID) ?>"><?php echo $product->post_title ?></a></h2>
							<?php endif; ?>
							
							<?php echo wpautop($product->post_excerpt) ?>
							
							<?php if (!empty($product->price)) : ?>
							<h5><?php echo Utilities::PrintMoney($product->price) ?></h5>
							<?php endif; ?>
							
						</footer>
					</article>
					
					<?php endforeach; ?>
				
				</section>
				
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
	
</section>

<?php else : ?>

<?php $products = $store->GetProducts('publish', 'large', 'post_date'); ?>

<section class="products tame clear">

	<?php if (!empty($products)) : ?>
	
		<?php foreach($products as $product) : ?>
		
		<article class="product small span4">
			<a href="<?php echo get_permalink($product->ID) ?>">
				<img src="<?php echo $product->featured_image ?>" alt="<?php echo $product->post_title ?>" title="<?php echo $product->post_title ?>" />
			</a>
			<footer class="product-footer">
				
				<?php if (!empty($product->post_title)) : ?>
				<h2><?php echo $product->post_title ?></h2>
				<?php endif; ?>
				
				<?php echo wpautop($product->post_excerpt) ?>
				
				<?php if (!empty($product->price)) : ?>
				<h5><?php echo Utilities::PrintMoney($product->price) ?></h5>
				<?php endif; ?>
				
			</footer>
		</article>
		
		<?php endforeach; ?>
		
	<?php else : ?>
	
	<h2>No Products Found</h2>
	
	<?php endif; ?>

</section>

<?php endif; ?>

<?php endwhile; ?>
<?php get_footer(); ?>
