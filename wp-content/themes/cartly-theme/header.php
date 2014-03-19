<?php

global
	$account,
	$cart,
	$store,
	$accountPage,
	$loginPage,
	$cartPage,
	$passwordResetPage,
	$orderPage,
	$cartlyPluginActive;

?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<title><?php cartly_title(); ?></title>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="description" content="<?php cartly_description(); ?>" />
<meta name="viewport" content="width=device-width" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
<!--[if lt IE 9]><script src="<?php echo get_template_directory_uri(); ?>/-/js/html5.js"></script><![endif]-->
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php if (!$cartlyPluginActive) : ?>
<div class="message error">
	<p>The Cartly Plugin is not installed.</p>
</div>
<?php endif; ?>

<div class="wrap">
	<header class="masthead tame clear" role="banner">
		<h1 class="site-title">
			<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><?php bloginfo('name'); ?></a>
		</h1>
		<nav class="site-nav" role="navigation">
			<ul>
				<?php if ($account->IsAuthorized() == false) : ?>
				<?php echo wp_list_pages("title_li=&include=$loginPage->ID") ?>
				<?php else : ?>
				<?php echo wp_list_pages("title_li=&include=$accountPage->ID") ?>
				<?php endif; ?>
				<li class="cart"><a href="<?php echo get_permalink($cartPage->ID) ?>">Cart <span class="item-count"><?php echo $cart->itemCount ?></span></a></li>
				<?php if ($account->IsAuthorized() == true) : ?>
				<li><a href="#" class="log-out">Log Out</a></li>
				<?php endif; ?>
			</ul>
		</nav>
	</header>