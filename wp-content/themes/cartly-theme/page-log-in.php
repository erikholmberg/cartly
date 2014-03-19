<?php
/*
TEMPLATE NAME: Log In / Register
*/

// Handle redirect
if ($account->IsAuthorized())
{
	// Log In
	if ($post->ID == 38)
	{
		wp_redirect(esc_url(home_url('/')));
		exit;
	}
}

?>

<?php get_header(); ?>
<?php if (have_posts()) while (have_posts()) : the_post(); ?>

<section class="log-in-register page-content tame clear">
	<div class="left">
		<h2>Log In</h2>
		<form class="log-in-form">
		
		<?php if (isset($_GET['redirect'])) : ?>
		<input type="hidden" name="redirect" value="<?php echo urldecode($_GET['redirect']) ?>" />
		<?php endif; ?>
		
		<table class="log-in">
			<tr>
				<td class="label">
					<label for="account[email]">Email</label>
				</td>
				<td class="input">
					<input type="text" name="account[email]" maxlength="128" />
				</td>
			</tr>
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
					<input type="submit" class="button" value="Log In" />
				</td>
			</tr>
			<tr>
				<td class="label"></td>
				<td class="input forgot-password">
					<label><a href="#" class="forgot-password-link">Forgot password?</a></label>
				</td>
			</tr>	
			<tr>
				<td class="label"></td>
				<td class="input">
					<p class="log-in-error alert"></p>
				</td>
			</tr>			
		</table>
		</form>
		
		<form class="reset-password-form initial-hide">
		<table class="reset-password">
			<tr>
				<td colspan="2">
					<p>Please enter your email address so we can send you an email to reset your password.</p>
				</td>
			</tr>
			<tr>
				<td class="label">
					<label for="account[email]">Email</label>
				</td>
				<td class="input">
					<input type="text" name="account[email]" maxlength="128" />
				</td>
			</tr>
			<tr>
				<td class="label"></td>
				<td class="input">
					<input type="submit" class="button green" value="Send" />
				</td>
			</tr>
			<tr class="reset-password-messages">
				<td class="label"></td>
				<td class="input">
					<p class="reset-message"></p>
					<p class="reset-error error"></p>
				</td>
			</tr>	
		</table>
		</form>
		
	</div>
	<div class="right">
		<h2>Register</h2>
		<form class="register-form">
		<table class="register">
			<tr>
				<td class="label">
					<label for="account[full_name]">Full Name</label>
				</td>
				<td class="input">
					<input type="text" name="account[full_name]" maxlength="50" />
				</td>
			</tr>
			<tr>
				<td class="label">
					<label for="account[email]">Email</label>
				</td>
				<td class="input">
					<input type="text" name="account[email]" maxlength="128" />
				</td>
			</tr>
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
					<input type="submit" class="button green" value="Register" />
				</td>
			</tr>
			<tr>
				<td class="label"></td>
				<td class="input">
					<p class="register-error error"></p>
				</td>
			</tr>				
		</table>
		</form>
	</div>
</section>

<?php endwhile; ?>
<?php get_footer(); ?>
