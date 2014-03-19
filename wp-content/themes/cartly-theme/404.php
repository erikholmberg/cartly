<?php get_header(); ?>

	<div class="page-content tame">
		<h1>404</h1>
		<h2>Page Not Found</h2>
		<p>Please head to the <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">home page</a>.</p>
	</div>
	
<?php get_footer(); ?>