<?php

namespace cmdstr\cookies;

use cmdstr\cookies\cookieEncryptionInterface;

class nullEncryption implements cookieEncryptionInterface {
    public function encrypt(string $data):string
    {
      return $data;
    }
  
    public function decrypt(string $data):string
    {
      return $data;
    }
}