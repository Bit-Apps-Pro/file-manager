<?php

namespace BitApps\FM\Core\Utils;

use BitApps\FM\Config;

class Common
{
    private $_cipher  = 'aes-256-cbc';

    public function encrypted($data)
    {
        $secretKey = Config::getOption('bf_secret_key');
        if (!$secretKey) {
            $secretKey = 'bf-' . time();
            Config::addOption('bf_secret_key', $secretKey, true);
        }
        $ivlen      = openssl_cipher_iv_length($this->_cipher);
        $iv         = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($data, $this->_cipher, $secretKey, 0, $iv);

        return base64_encode($iv . $ciphertext);
    }

    public function decrypted($encryptedData)
    {
        $secretKey = Config::getOption('bf_secret_key');
        if (!$secretKey) {
            $secretKey = 'bf-' . time();
            Config::addOption('bf_secret_key', $secretKey, true);
        }
        $decode     = base64_decode($encryptedData);
        $ivlen      = openssl_cipher_iv_length($this->_cipher);
        $iv         = substr($decode, 0, $ivlen);

        $ciphertext = substr($decode, $ivlen);

        return openssl_decrypt($ciphertext, $this->_cipher, $secretKey, 0, $iv);
    }
}
