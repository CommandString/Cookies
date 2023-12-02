
# [CommandString/Cookies](https://packagist.org/packages/commandstring/cookies) - A simpler way to manipulate cookies in PHP #

### Install with Composer using `composer require commandstring/cookies` ###

## Requirements ##
- PHP >=8.0
- Basic understanding of PHP OOP
- Composer 2

## Basic Usage ##
```php
require  __DIR__."/vendor/autoload.php";
use CommandString\Cookies\Cookie;

$cookies = new Cookie();

#                              v hours 
#                              v valid   v seconds valid
$cookies->set("name", "value", 168, 10, 30); // by default cookies expire in a week
#                                   ^ minutes valid

// After page refresh //
echo $cookies->get("name"); // output: value

// Delete cookie //
$cookie->delete("name"); // remove the cookie

// Delete all cookies
$cookie->deleteAll();

// Check if a cookie exists
$cookie->exists("name"); // returns bool
```

## Using CommandString/Encrypt with CommandString/Cookies ##
### *[I recommend checking out the README for CommandString/Encrypt](https://github.com/CommandString/encrypt#basic-usage)* ###
```php
use CommandString\CookieEncryption\CookieEncryption;
use CommandString\Cookies\Cookie;

// use the cookieEncryption class that wraps around cmdstr/encrypt/encryption class
$cookies = new Cookie(new CookieEncryption("MZCdg02STLzrsj05KE3SIL62SSlh2Ij", "AES-256-CTR"));
// ... now cmdstr/encrypt will handle encrypting cookies
```