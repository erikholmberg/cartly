<?php

require_once('Cookie.php');
require_once('Utilities.php');
require_once('Constants.php');
require_once('Data.php');

Utilities::IncludeWPConfig();

class Account
{	
	private $authCookie;
	
	// Constructor
	function __construct()
	{
		$this->authCookie = new AuthCookie();
	}
	
	// Public Methods
	public function LogIn($email, $password, &$message)
	{	
		if ($this->UserExists($email, $password, $userId))
		{
			session_destroy();
			
			$this->Authorize($email, $password, $userId);
	
			return true;
		}
		else
		{
			$message = 'Account does not exist';
			return false;
		}
	}
	
	public function LogOut()
	{
		$this->authCookie->DestroyCookie();
		return true;
	}
	
	public function Register($fullName, $email, $password, &$message)
	{
		if (!$this->EmailExists($email))
		{
			$this->CreateAccount($fullName, $email, $password, $message);
			$this->LogIn($email, $password, $message);
			return true;
		}
		else
		{
			$message = 'Account exists, please log in';
			return false;
		}
	}
	
	public function ResetPasswordRequest($email, &$message)
	{
		global $wpdb;
		
		if ($this->UserExistsByEmail($email, $user))
		{
			$key = md5(Utilities::RandomString());
			
			$wpdb->insert('cartly_password_reset', array(
				'create_date' => date('Y-m-d H:i:s'),
				'user_id' => $user->id,
				'secret_key' => $key));
				
			$this->SendPasswordResetEmail($user, $key);
			
			return true;
		}
		else
		{
			$message = 'Account does not exist';
			return false;
		}
	}
	
	public function ResetPassword($userId, $password, $key, &$message)
	{
		global $wpdb;
			
		$success = $wpdb->update('cartly_user',
			array('password' => md5($password)),
			array('id' => $userId));
			
		if ($success >= 0)
		{
			$wpdb->query($wpdb->prepare("DELETE FROM cartly_password_reset WHERE secret_key = %s", $key));
	        
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function CheckPasswordRequest($userId, $key)
	{
		global $wpdb;
		
		$sql = "SELECT id FROM cartly_password_reset WHERE user_id = $userId AND secret_key = '$key' LIMIT 1";
		
		$reset = $wpdb->get_results($sql);
		
		return count($reset) == 1 ? true : false;
	}
	
	public function SendPasswordResetEmail($user, $key)
	{
		$passwordResetPage = get_page_by_title('Password Reset');
		
		$subject = get_bloginfo('title') . ' Password Reset';
		$message = 'Please visit this page to change your password:' . PHP_EOL;

		$parts = parse_url(home_url('/'));
		$from = 'info@'.$parts['host'];
		
		$headers = 'From: '.get_bloginfo('name').' <'.$from.'>' . PHP_EOL;
		
		$url = get_permalink($passwordResetPage->ID)."?user_id=$user->id&key=$key";
		
		$message .= $url;
		
		wp_mail($user->email, $subject, $url, $headers);
	}
	
	public function UserExists($email, $password, &$userId)
	{
		global $wpdb;
	
		$password = md5(trim($password));
		
		$sql = "SELECT id FROM cartly_user WHERE email = '$email' AND password = '$password' LIMIT 1";
		
		$user = $wpdb->get_results($sql);
		
		if (count($user) == 1)
		{
			$userId = $user[0]->id;
			return true;
		}
		else
		{
			$userId = 0;
			return false;	
		}
	}
	
	public function UserExistsByEmail($email, &$user)
	{
		global $wpdb;
		
		$sql = "SELECT * FROM cartly_user WHERE email = '$email' LIMIT 1";
		
		$users = $wpdb->get_results($sql);
		
		if (count($users) == 1)
		{
			$user = $users[0];
			return true;
		}
		else
		{
			$user = 0;
			return false;	
		}
	}
	
	public function EmailExists($email)
	{
		global $wpdb;
		
		$sql = "SELECT id FROM cartly_user WHERE email = '$email' LIMIT 1";
		
		$user = $wpdb->get_results($sql);
		
		return count($user) == 1;
	}
	
	public function CreateAccount($fullName, $email, $password, &$message = '')
	{	
		global $wpdb;
		
		$password = $password != '' ? md5($password) : '';
		
		$wpdb->insert('cartly_user', array(
			'create_date' => date('Y-m-d H:i:s'),
			'full_name' => $fullName,
			'email' => $email,
			'password' => $password));
		
		return $wpdb->insert_id;
	}

	public function CreateAddress(
		$userId,
		$orderId,
		$fullName,
		$billing = false,
		$shipping = false,
		$address1,
		$address2,
		$city,
		$stateRegion,
		$zip,
		$countryId,
		$phone)
	{	
		global $wpdb;
		
		$billing = $billing == false ? 0 : 1;
		$shipping = $shipping == false ? 0 : 1;
		
		$wpdb->insert('cartly_address', array(
			'user_id' => $userId,
			'order_id' => $orderId,
			'create_date' => date('Y-m-d H:i:s'),
			'full_name' => $fullName,
			'billing' => $billing,
			'shipping' => $shipping,
			'address_1' => $address1,
			'address_2' => $address2,
			'city' => $city,
			'state_region' => $stateRegion,
			'zip' => $zip,
			'country_id' => $countryId,
			'phone' => $phone));
	}
	
	public function IsAuthorized()
	{
		return $this->authCookie->IsAuthorized();
	}
	
	public function GetUserId()
	{
		return $this->authCookie->GetUserID();
	}
	
	public function GetUser()
	{	
		$user_id = $this->authCookie->GetUserID();
		
		$sql = "SELECT * FROM cartly_user WHERE id = $user_id LIMIT 1";
		
		$user = Data::GetResultsObject($sql);
		
		return empty($user) ? '' : $user[0];
	}
	
	public function GetOrders()
	{
		global $wpdb;
		
		$user_id = $this->authCookie->GetUserID();
		
		$sql = "SELECT o.id, o.create_date, o.total, s.name AS status, (SELECT COUNT(id) FROM cartly_order_item WHERE order_id = o.id) AS items, (SELECT GROUP_CONCAT(product_id) FROM cartly_order_item WHERE order_id = o.id) AS item_ids, (SELECT GROUP_CONCAT(quantity) FROM cartly_order_item WHERE order_id = o.id) AS item_quantities FROM cartly_order o JOIN cartly_order_status s ON o.status_id = s.id WHERE o.user_id = $user_id ORDER BY o.create_date DESC";
		
		$orders = $wpdb->get_results($sql);
		
		return $orders;
	}
	
	public function GetUserMeta($name, $userId)
	{
		$value = '';
		
		if (!empty($name) && !empty($userId))
		{
			$sql = "SELECT * FROM cartly_user_meta WHERE name = '$name' AND user_id = $userId LIMIT 1";
		
			$values = Data::GetResultsObject($sql);
		
			$value = empty($values) ? '' : $values[0]->value;
		}
		
		return $value;
	}
	
	public function SetUserMeta($name, $value, $userId)
	{	
		global $wpdb;
		
		if (!empty($name) && !empty($value) && !empty($userId))
		{
			$create_date = date('Y-m-d H:i:s');
			
			$sql = "INSERT INTO cartly_user_meta (`create_date`, `update_date`, `user_id`, `name`, `value`) VALUES ('$create_date', '$create_date', $userId, '$name', '$value') ON DUPLICATE KEY UPDATE `value` = '$value', `update_date` = '$create_date';";
			
			$wpdb->query($sql);
		}
	}
	
	// Private Methods
	private function Authorize($email, $password, $userId)
	{	
		$this->authCookie->SetCookie($userId, true);
		
		//TODO: set_user_session($userId);
	}
}

?>