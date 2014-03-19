<?php

require_once('Encryption.php');

class Cookie
{
	private $path;
	private $domain;
	
	// Constructor
	function __construct()
	{
		$this->path = '/';
		$this->domain = $_SERVER['SERVER_NAME'];
	}
	
	// Public Methods
	public function GetCookie($name)
	{
		return $_COOKIE[$name];
	}
	
	public function SetCookie($name, $data, $remember = false)
	{
		// Cookie expires in 2 weeks if remembered, otherwise 48 hours
		$expiration = $remember == true ? time() + 1209600 : time() + 172800;
        
		setcookie($name, $data, $expiration, $this->path, $this->domain);
	}
	
	public function DestroyCookie($name)
	{
		setcookie($name, '', time() - 3600, $this->path, $this->domain);
	}
}

class EncryptedCookie extends Cookie
{
	// Public Methods
	public function GetCookie($name)
	{
		if (isset($_COOKIE[$name]))
		{
			return Encryption::Decrypt($_COOKIE[$name]);	
		}
		else
		{
			return '';
		}
	}
	
	public function SetCookie($name, $data, $remember = false)
	{
		$encryptedData = Encryption::Encrypt($data);
		parent::SetCookie($name, $encryptedData, $remember);
	}
	
	public function DestroyCookie($name)
	{
		parent::DestroyCookie($name);
	}
}

class AuthCookie extends EncryptedCookie
{
	private $authCookieName;
	
	function __construct()
	{
		$this->authCookieName = 'cartly_auth';
		parent::__construct();
	}
	
	// Public Methods
	public function GetCookie($name = '')
	{
		return explode('|', parent::GetCookie($name));
	}
	
	public function SetCookie($userId, $data, $remember = false)
	{
		$cookie_values = array(
			'user_id' => $userId,
			'auth_key' => CARTLY_AUTH_KEY
		);
		
		$encryptedData = implode('|', $cookie_values);

		parent::SetCookie($this->authCookieName, $encryptedData, $remember);
	}
	
	public function DestroyCookie($name = '')
	{
		parent::DestroyCookie($this->authCookieName);
	}
	
	public function GetUserId()
	{
		$pieces = $this->GetCookie($this->authCookieName);
		
		return count($pieces) > 0 ? $pieces[0] : 0;
	}
	
	public function IsAuthorized()
	{
		$pieces = $this->GetCookie($this->authCookieName);
		
		if (count($pieces) > 1)
		{
			if ($pieces[1] == CARTLY_AUTH_KEY)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}

?>