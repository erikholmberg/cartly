<?php

/*
require_once('Cookie.php');
require_once('Encryption.php');

// Test Encryption

// Test Cookies
$cookie = new Cookie();
$cookie->SetCookie('cookie', 'sugar, flour', true);

$encryptedCookie = new EncryptedCookie();
$encryptedCookie->SetCookie('encrypted_cookie', 'secret ingredients', true);

// You can only set cookies before output to the browser, keep these here.
echo 'Cookie: ' . $cookie->GetCookie('cookie') . '<br/>';
echo 'Encrypted Cookie: ' . $encryptedCookie->GetCookie('encrypted_cookie');
*/

echo "<pre>Running the test suite.</pre>";

$ok = @include_once(dirname(__FILE__).'/simpletest/autorun.php');
if (!$ok) {
  echo "<pre>MISSING DEPENDENCY: These test cases depend on SimpleTest. ".
       "Download it at <http://www.simpletest.org/>, and either install it ".
       "in your PHP include_path or put it in the test/ directory.</pre>\n";
  exit(1);
}

// Throw an exception on any error
function exception_error_handler($errno, $errstr, $errfile, $errline) {
  throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler('exception_error_handler');
error_reporting(E_ALL | E_STRICT);

require_once(dirname(__FILE__) . '/../lib/Stripe.php');

//require_once(dirname(__FILE__) . '/Stripe/TestCase.php');

//require_once(dirname(__FILE__) . '/Stripe/ApiRequestorTest.php');
//require_once(dirname(__FILE__) . '/Stripe/AuthenticationErrorTest.php');

?>