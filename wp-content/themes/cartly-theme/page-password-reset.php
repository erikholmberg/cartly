<?php
/*
TEMPLATE NAME: Password Reset
*/

// Handle redirect
if (isset($_GET['key']))
{	
	if (!$account->CheckPasswordRequest($_GET['user_id'], $_GET['key']))
	{	
		wp_redirect(get_permalink($loginPage->ID));
		exit;		
	}
}
else
{
	wp_redirect(esc_url(home_url('/')));
	exit;
}

?>

<?php get_header(); ?>
<?php if (have_posts()) while (have_posts()) : the_post(); ?>

<section class="password-reset page-content tame clear">

	<form class="password-reset-form">
	
	<h2>New Password</h2>
	
	<input type="hidden" name="user_id" value="<?php echo $_GET['user_id'] ?>" />
	<input type="hidden" name="secret_key" value="<?php echo $_GET['key'] ?>" />
	
	<table class="reset">
		<tr>
			<td class="label">
				<label for="account[password]">Password</label>
			</td>
			<td class="input">
				<input type="password" name="account[password]" maxlength="32" />
			</td>
		</tr>
		<tr>
			<td class="label"></td>
			<td class="input">
				<input type="submit" class="button" value="Reset" />
			</td>
		</tr>
		<tr>
			<td class="label"></td>
			<td class="input">
				<p class="reset-error error"></p>
			</td>
		</tr>				
	</table>
	</form>
	
	<div class="reset-success initial-hide">
		<h2>Your password has been changed</h2>
		<p>Please <a href="<?php echo get_permalink($loginPage->ID) ?>">log in</a> with your new info.</p>
	</div>
	
</section>

<?php endwhile; ?>
<?php get_footer(); ?>
