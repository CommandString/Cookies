<?php

namespace CommandString\Cookies;

class Cookie
{
    private CookieEncryptionInterface $encryptor;

    public function __construct(?CookieEncryptionInterface $encryptor = null)
    {
        $this->encryptor = $encryptor ?? new NullEncryption;
    }

    public function exists(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public function set(
        string     $name,
        string|int $value,
        int        $hoursValid = 168,
        int        $minutesValid = 0,
        int        $secondsValid = 0,
        string     $path = "/",
        string     $domain = "",
        bool       $secure = false,
        bool       $httponly = false
    ): self
    {
        $encryptedString = $this->encryptor->encrypt($value);
        setcookie($name, $encryptedString, time() + ((3600 * $hoursValid) + (60 * $minutesValid) + $secondsValid), $path, $domain, $secure, $httponly);

        $_COOKIE[$name] = $encryptedString;

        return $this;
    }

    public function get(string $name): ?string
    {
        return isset($_COOKIE[$name]) ? $this->encryptor->decrypt($_COOKIE[$name]) : null;
    }

    public function delete(string $cookie, string ...$cookies): self
    {
        $cookies = [$cookie, ...$cookies];

        foreach ($cookies as $cookie) {
            if ($this->exists($_COOKIE[$cookie])) {
                continue;
            }

            unset($_COOKIE[$cookie]);
            setcookie($cookie, null, -1, '/');
        }

        return $this;
    }

    public function deleteAll(): self
    {
        $this->delete(...array_keys($_COOKIE));
        return $this;
    }
}
