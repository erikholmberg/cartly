<?php

include(dirname(dirname(__FILE__)).'/stripe/lib/Stripe.php');
require_once('Account.php');
require_once('Utilities.php');

class Payment
{
	private $apiKey;
	
	// Constructor
	function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
	}
	
	// Public Methods
	public function Charge($amount, $fullName, $email, $stripeToken, $userId)
	{		
		$amount = $this->ConvertToCents($amount, true);
		$message = $fullName . ' ( '.$email.' )';
		
		$this->SendToStripe($amount, $stripeToken, $message, $userId, $fullName, $email);
	}
	
	// Private Methods
	private function ConvertToCents($value, $to_cents = true)
	{
	    // Strip out commas
	    $value = preg_replace("/\,/i","",$value);
	    
	    // Strip out all but numbers, dash, and dot
	    $value = preg_replace("/([^0-9\.\-])/i","",$value);
	    
	    // Make sure we are dealing with a proper number now, no +.4393 or 3...304 or 76.5895,94
	    if (!is_numeric($value))
	    {
	        return 0;
	    }
	    
	    // Convert to a float explicitly
	    $value = (float)$value;
	    
	    if ($to_cents)
	    {
		    return round($value, 2) * 100;
	    }
	    else
	    {
		    return $value;
	    }
	}
	
	private function SendToStripe($amount, $token, $message, $userId, $fullName, $email)
	{	
		Stripe::setApiKey($this->apiKey);
		$account = new Account();
		
		$error_message = '';
		
		try
		{
			// Check for Stripe Customer ID
			$stripeCustomerId = $account->GetUserMeta('stripe_customer_id', $userId);
			
			// Create a Customer
			if (empty($stripeCustomerId))
			{
				$customer = Stripe_Customer::create(array(
					'card' => $token,
					'description' => $fullName,
					'email' => $email)
				);
				
				$stripeCustomerId = $customer->id;
				
				$account->SetUserMeta('stripe_customer_id', $stripeCustomerId, $userId);
			}
			
			// Charge the Customer instead of the card
			$charge = Stripe_Charge::create(array(
				'amount' => $amount, // amount in cents
				'currency' => "usd",
				'customer' => $stripeCustomerId)
			);
			
			if (!$charge->paid)
			{
				$error_message = $charge->failure_message;
			}
		}
		catch(Stripe_CardError $e)
		{
			$error_message = $this->FormatStripeError($e); 
		}
		catch (Stripe_InvalidRequestError $e)
		{
			$error_message = $this->FormatStripeError($e);
		}
		catch (Stripe_AuthenticationError $e)
		{
			$error_message = $this->FormatStripeError($e);
		}
		catch (Stripe_ApiConnectionError $e)
		{
			$error_message = $this->FormatStripeError($e);
		}
		catch (Stripe_Error $e)
		{
			$error_message = $this->FormatStripeError($e);
		}
		catch (Exception $e)
		{
			$error_message = $e->getMessage();
		}
		
		if ($error_message != '')
		{
			Utilities::LogError($error_message);
			Utilities::SendJSON('FAILURE', $error_message, '001');
		}
	}
	
	private function FormatStripeError($e)
	{
		$body = $e->getJsonBody();
		$err  = $body['error'];
		return $e->getHttpStatus()."\t".$err['type']."\t".$err['code']."\t".$err['param']."\t".$err['message'];
	}
}

?>