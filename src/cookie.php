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
     * Passphrase key - must be a 31 character alphanumeric string
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
        if (strlen($passphrase) !== 31) {
            throw new InvalidArgumentException("Passphrase must be 31 character string");
        }

        foreach(openssl_get_cipher_methods() as $method) {
            if ($encryptionMethod === $method) {
                $this->encryptionMethod = $encryptionMethod;
            }
        }

        if (!isset($encryptionMethod)) {
            throw new InvalidArgumentException("Encryption method doesn't exist. Use var_dump(openssl_get_cipher_methods()); to view all available methods.");
        }

        $this->passphrase = $passphrase;

        $this->encryptionMethod = $encryptionMethod;
    }

    /**
     * Generates a 16 character alphanumeric string
     * 
     * @return string
     */
    private function generateIv():string
    {
        $characters = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMOPQRSTUVWXYZ0123456789");
        $iv = "";

        for($i=0; $i < 16; $i++) {
            $iv .= $characters[rand(0, count($characters)-1)];
        }
        
        return $iv;
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
        $iv = $this->generateIv();

        $encryptedString = openssl_encrypt($value, $this->encryptionMethod, $this->passphrase, 0, $iv);

        setcookie($name, "$iv:$encryptedString", time() + ((3600 * $hoursValid) + (60 * $minutesValid) + $secondsValid), "/");

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
        foreach ($_COOKIE as $key => $value) {
            $cookies[] = $key;
        }

        $this->delete($cookies);
        
        return $this;
    }
}