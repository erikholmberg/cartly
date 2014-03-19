<?php

class Encryption
{
	private static $textKey = '(g&0C;7}UQoqYqf3|}Z-%^Y(8*Y;pgsg.@jGrb}K|r(]Y.HTzDhu-x>+QCE_';
	private static $cipher = MCRYPT_RIJNDAEL_256;
	private static $cipherMode = MCRYPT_MODE_ECB;
	private static $cipherRandom = MCRYPT_RAND;

	// Public Methods
	public static function Encrypt($plaintext)
	{
		if (!$plaintext)
		{
			return '';
		}
		
        $securekey = hash('sha256', static::$textKey, TRUE);
        $iv = mcrypt_create_iv(32);
        return base64_encode(mcrypt_encrypt(static::$cipher, $securekey, $plaintext, static::$cipherMode, $iv));
	}

	public static function Decrypt($ciphertext)
	{
		if (!$ciphertext)
		{
			return '';
		}
		
		$securekey = hash('sha256', static::$textKey, TRUE);
        $iv = mcrypt_create_iv(32);
        return trim(mcrypt_decrypt(static::$cipher, $securekey, base64_decode($ciphertext), static::$cipherMode, $iv));
	}
}

?>