<?php

namespace cmdstr\cookies;

use Exception;
use InvalidArgumentException;

/**
 * An simpler way to manipulate cookies in PHP
 * 
 * @author Command_String - https://discord.dog/232224992908017664
 */
class cookie {
    /**
     * Passphrase key - must at least a 32 character alphanumeric string
     * 
     * @property string $passphrase
     */
    private string $passphrase;

    /**
     * Encryption method
     * 
     * @property string $encryptionMethod
     */
    private string $encryptionMethod;

    /**
     * @param string $passphrase
     */
    public function __construct(string $passphrase, string $encryptionMethod) 
    {
        if (!extension_loaded('openssl')) {
            throw new Exception('The openssl extension is not installed or enabled.');
        }

        if (strlen($passphrase) < 31) {
            throw new InvalidArgumentException("Passphrase must be at least a 32 character string");
        }

        if (!in_array(strtolower($encryptionMethod), openssl_get_cipher_methods())) {
            throw new InvalidArgumentException("Encryption method doesn't exist. Use var_dump(openssl_get_cipher_methods()); to view all available methods.");
        }

        $this->passphrase = $passphrase;

        $this->encryptionMethod = $encryptionMethod;
    }

    /**
     * Decrypt data
     * 
     * @param string $data
     * @return string
     */
    private function decrypt(string $data):string
    {
        $parts = explode(":", $data);

        $iv = $parts[0];
        $encryptedString = $parts[1];

        return openssl_decrypt($encryptedString, $this->encryptionMethod, $this->passphrase, 0, $iv);
    }

    /**
     * Set cookie
     * 
     * @param string $name
     * @param string $value
     * @param int $hoursValid
     */
    public function set(string $name, string $value, int $hoursValid=168, int $minutesValid=0, int $secondsValid=0):cookie
    {
        $iv = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, openssl_cipher_iv_length($this->encryptionMethod));
        $encryptedString = openssl_encrypt($value, $this->encryptionMethod, $this->passphrase, 0, $iv);
        setcookie($name, "{$iv}:$encryptedString", time() + ((3600 * $hoursValid) + (60 * $minutesValid) + $secondsValid), "/");
        
        $_COOKIE[$name] = $value;

        return $this;
    }

    public function exists(string $name):bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Get cookie
     * 
     * @param string $name
     * @return string
     */
    public function get(string $name):string
    {
        if ($this->exists($name)) {
            return $this->decrypt($_COOKIE[$name]);
        } else {
            throw new \InvalidArgumentException("Cookie doesn't exist in configuration");
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
                throw new \InvalidArgumentException("Cookie doesn't exist in configuration");
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