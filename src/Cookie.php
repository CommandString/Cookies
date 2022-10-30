<?php

namespace CommandString\Cookies;

use InvalidArgumentException;
use CommandString\Cookies\NullEncryption;

/**
 * An simpler way to manipulate cookies in PHP
 * 
 * @author Command_String - https://discord.dog/232224992908017664
 */
class Cookie {
    private cookieEncryptionInterface $encryptor;
        
    public function __construct(?cookieEncryptionInterface $encryptor = null)
    {
        $this->encryptor = $encryptor ?? new nullEncryption;
    }

    /**
     * Does cookie exist
     * 
     * @param string $name
     * 
     * @return bool
     */
    public function exists(string $name):bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Set cookie
     * 
     * @param string $name
     * @param string $value
     * @param int $hoursValid
     * 
     * @return cookie
     */
    public function set(string $name, string|int $value, int $hoursValid=168, int $minutesValid=0, int $secondsValid=0):cookie
    {
        $encryptedString = $this->encryptor->encrypt($value);
        setcookie($name, $encryptedString, time() + ((3600 * $hoursValid) + (60 * $minutesValid) + $secondsValid), "/");

        $_COOKIE[$name] = $encryptedString;

        return $this;
    }

    /**
     * Get cookie
     * 
     * @param string $name
     * 
     * @return string
     */
    public function get(string $name):string
    {
        if ($this->exists($name)) {
            return $this->encryptor->decrypt($_COOKIE[$name]);
        } else {
            throw new InvalidArgumentException("Cookie doesn't exist in configuration");
        }
    }

    /**
     * Delete cookie(s) specified
     * 
     * @param string $name
     */
    public function delete(string ...$cookies):cookie
    {
        foreach ($cookies as $cookie) {
            if ($this->exists($_COOKIE[$cookie])) {
                throw new InvalidArgumentException("Cookie doesn't exist in configuration");
            }

            unset($_COOKIE[$cookie]);
            setcookie($cookie, null, -1, '/');
        }

        return $this;
    }

    /**
     * Deletes all cookies
     * 
     * @return void
     */
    public function deleteAll():cookie 
    {
        $this->delete(...array_keys($_COOKIE));
        return $this;
    }
}
