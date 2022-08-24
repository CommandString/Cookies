<?php

namespace cmdstr\cookies;

interface cookieEncryptionInterface {  
    public function encrypt(string $data) : string;
    public function decrypt(string $data) : string;
}