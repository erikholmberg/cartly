<?php

class Email
{
	// Public Methods
	public static function SendOrderEmails($orderId, $to)
	{
		ob_start();
		include 'OrderEmail.php';
		$message = ob_get_clean();
	
		self::SendEmail($to, $message);
	}
	
	// Private Methods
	private static function SendEmail($to, $message)
	{
		add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
		
		$company = get_bloginfo('name');
		$from = get_option('cartly_new_order_email');
		
		if (!isset($from))
		{
			$parts = parse_url(home_url('/'));
			$from = 'info@'.$parts['host'];
		}
		
		$headers = 'From: '.$company.' <'.$from.'>'."\r\n";
		
		// Send Email to Customer
		wp_mail($to, $company.' Order', $message, $headers);
		
		// Send Email to Store Owner
		wp_mail($from, $company.' Order', $message, $headers);
	}
}	
	
?>