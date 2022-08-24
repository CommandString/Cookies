
# [cmdstr/cookies](https://packagist.org/packages/cmdstr/cookies) - A simpler way to manipulate cookies in PHP #

### Install with Composer using `composer require cmdstr/cookies` ###

## Requirements ##
- PHP >=8.0
- Basic understanding of PHP OOP
- Composer 2
- cmdstr/encrypt

## Basic Usage ##
```php
require  __DIR__."/vendor/autoload.php";
use cmdstr\cookies\cookie;
use cmdstr\encrypt\encryption;

#                            v >=32 character string            v Encryption method #
$encryptor = new encryption("MZCdg02STLzrsj05KE3SIL62SSlh2Ij", "AES-256-CTR");
$cookies = new cookie($encryptor);

#                              v hours 
#                              v valid   v seconds valid
$cookies->set("name", "value", 168, 10, 30); // by default cookies expire in a week
#                                   ^ minutes valid

// After page refresh (An exception will be thrown if the cookie doesn't exist) //
echo $cookies->get("name"); // output: value

// Delete cookie (An exception will be thrown if the cookie doesn't exist) //
$cookie->delete("name"); // remove the cookie

// Delete all cookies
$cookie->deleteAll();

// Check if a cookie exists
$cookie->exists("name"); // returns bool
```

## Comparing regular cookie manipulation with cmdstr/cookies ##
### cmdstr/cookies ###
```php
// config.php
require  __DIR__."/vendor/autoload.php";
use cmdstr\cookies\cookie;
use cmdstr\encrypt\encryption;

$encryptor = new encryption("MZCdg02STLzrsj05KE3SIL62SSlh2Ij", "AES-256-CTR");
$cookies = new cookie($encryptor);
// ...

// login.php
require_once "config.php";

if ($userIsReadyToBeLoggedIn) {
	$cookies->set("username", "Command_String");
	header("location: home.php");
}
// ...

// home.php
<?php
require_once "config.php";

if (!$cookies->exists("username")) {
	header("location: login.php");
}
```
```html
// ...
<h1>Welcome back, <?= $cookies->get("username")); ?></h1>
// ...
```
```php
// logout.php
require_once "config.php";

$cookies->deleteAll();
header("location: login.php");
```
### Regular Cookie Manipulation ###
```php
// config.php
$cookies = [
	"passphrase" => "MZCdg02STLzrsj05KE3SIL62SSlh2Ij",
	"method" => "AES-256-CTR"
];

// ...

// login.php
require_once "config.php";

if ($userIsAbleToBeLoggedIn){
	$alphabet = [
		...range(0, 9),
		...range('a', 'z'),
		...range('A', 'Z')
	];
    
	$length = openssl_cipher_iv_length($cookies['method']);
	$bytes = openssl_random_pseudo_bytes($length);
	$iv = '';

	foreach (str_split($bytes) as $byte) {
		$offset = hexdec(bin2hex($byte)) % count($alphabet);
		$iv .= $alphabet[$offset];
	}
	
	$encryptedString = openssl_encrypt("Command_String", $cookies['method'], $cookies['passphrase'], 0, $iv);

	setcookie($name, "$iv:$encryptedString", time() + (3600 * 168), "/");
	header("location: home.php");
}

// home.php
require_once "config.php";

if (isset($_COOKIE['username'])) {
	header("location: login.php");
}

// ...
```
```html
<h1>Welcome back, <?= $_COOKIE['username'] ?><h1>
<!-- ... -->
```
```php
// logout.php
foreach ($_COOKIE as $key => $value) {
	unset($_COOKIE[$key]);
	setcookie($key, null, -1, '/');
}

header("location: login.php");
```
