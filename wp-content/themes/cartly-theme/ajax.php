<?php

require_once('-/src/cartly/Account.php');
require_once('-/src/cartly/Cart.php');
require_once('-/src/cartly/Order.php');
require_once('-/src/cartly/Store.php');
require_once('-/src/cartly/Utilities.php');

// Check the POST
if (isset($_POST['action']))
{
	switch ($_POST['action'])
	{
		case 'log-in' :
			LogIn();
			break;
		case 'log-out' :
			LogOut();
			break;
		case 'register' :
			Register();
			break;
		case 'reset-password-request' :
			ResetPasswordRequest();
			break;
		case 'reset-password' :
			ResetPassword();
			break;
		case 'add-to-cart' :
			AddToCart();
			break;
		case 'update-cart' :
			UpdateCart();
			break;
		case 'remove-from-cart' :
			RemoveFromCart();
			break;
		case 'place-order' :
			PlaceOrder();
			break;
	}
}

// Ajax Action Methods
function LogIn()
{
	$account = new Account();
	if ($account->LogIn($_POST['email'], $_POST['password'], $message))
	{
		Utilities::SendJSON('SUCCESS', 'Logged in.', '001', array('redirect_url' => esc_url(home_url('/'))));	
	}
	else
	{
		Utilities::SendJSON('FAILURE', 'Unable to log in.', '101', array('message' => $message));
	}
}

function LogOut()
{
	$account = new Account();
	if ($account->LogOut())
	{
		Utilities::SendJSON('SUCCESS', 'Logged out.', '001', array('redirect_url' => esc_url(home_url('/'))));	
	}
	else
	{
		Utilities::SendJSON('FAILURE', 'Unable to log out.', '101', array('message' => $message));
	}
}

function Register()
{
	$account = new Account();
	if ($account->Register($_POST['full_name'], $_POST['email'], $_POST['password'], $message))
	{
		Utilities::SendJSON('SUCCESS', 'Account registered.', '001', array('redirect_url' => esc_url(home_url('/'))));	
	}
	else
	{
		Utilities::SendJSON('FAILURE', 'Unable to register account.', '101', array('message' => $message));
	}
}

function ResetPasswordRequest()
{
	$account = new Account();
	if ($account->ResetPasswordRequest($_POST['email'], $message))
	{
		Utilities::SendJSON('SUCCESS', 'Please check you email for reset instructions.', '101');	
	}
	else
	{
		Utilities::SendJSON('FAILURE', 'Unable to reset password, please check your email address.', '101');
	}
}

function ResetPassword()
{
	$account = new Account();
	$account->LogOut();
	if ($account->ResetPassword($_POST['user_id'], $_POST['password'], $_POST['key'], $message))
	{
		Utilities::SendJSON('SUCCESS', 'Your password has been changed.', '101');	
	}
	else
	{
		Utilities::SendJSON('FAILURE', 'Unable to reset password.', '101');
	}
}

function AddToCart()
{
	$cart = new Cart();
	$optionId = empty($_POST['option_id']) ? 0 : $_POST['option_id'];
	$cartMeta = $cart->AddToCart($_POST['product_id'], $optionId, $_POST['quantity']);
	Utilities::SendJSON('SUCCESS', 'Item added to cart.', '001', $cartMeta);
}

function UpdateCart()
{
	$cart = new Cart();
	$cartMeta = $cart->UpdateCart($_POST['product_id'], $_POST['option_id'], $_POST['quantity']);
	Utilities::SendJSON('SUCCESS', 'Cart updated.', '001', $cartMeta);
}

function RemoveFromCart()
{
	$cart = new Cart();
	$cartMeta = $cart->RemoveFromCart($_POST['product_id'], $_POST['option_id']);
	Utilities::SendJSON('SUCCESS', 'Removed from cart.', '001', $cartMeta);
}

function PlaceOrder()
{
	$store = new Store();
	$code = '000';
	
	if ($store->PlaceOrder($message, $code, $extra, $guest))
	{
		Utilities::SendJSON('SUCCESS', 'Order placed.', '000', array('guest' => $guest));	
	}
	else
	{
		Utilities::SendJSON('FAILURE', $message, $code, $extra);
	}
}

?>