<?php

namespace CommandString\Cookies;

class NullEncryption implements CookieEncryptionInterface
{
    public function encrypt(string $data): string
    {
        return $data;
    }

    public function decrypt(string $data): string
    {
        return $data;
    }
}
