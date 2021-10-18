<?php
/**
 * Encryption class module.
 * 
 * PHP version 7.4
 * 
 * @category Utility
 * @package  Wpgp
 * @author   Jake Schroeder <jake_schroeder@outlook.com>
 * @license  GNU v3
 * @link     https://github.com/jschroedr/wp-google-places
 * 
 * see: https://developer.wordpress.org/plugins/settings/custom-settings-page/
 */
namespace wpgp 
{
    /**
     * Manages two-way encryption operations for the plugin
     * 
     * @category Utility
     * @package  Wpgp
     * @author   Jake Schroeder <jake_schroeder@outlook.com>
     * @license  GNU v3
     * @link     https://github.com/jschroedr/wp-google-places
     */
    class Encryption 
    {

        const ALGORITHM = 'aes-256-gcm';
        const DATA_LEN = 50;

        /**
         * Encrypt $data using ALGORITHM
         * 
         * @param $data string - decrypted data
         * 
         * @return string
         */
        public static function encrypt(string $data) : string
        {
            $iv = self::_getIV();
            $key = self::_getKey();
            return (string)openssl_encrypt($data, self::ALGORITHM, $key, 0, $iv);
        }

        /**
         * Decrypt $data using ALGORITHM
         * 
         * @param $data string - encrypted data
         * 
         * @return string
         */
        public static function decrypt(string $data) : string 
        {
            $iv = self::_getIV();
            $key = self::_getKey();
            return (string)openssl_decrypt($data, self::ALGORITHM, $key, 0, $iv);
        }

        /**
         * Get the intialization vector
         * 
         * @return string
         */
        private static function _getIV() : string 
        {
            $iv = MainMenu::getOptionValue(MainMenu::IV_FIELD_NAME);
            if (empty($iv) === true) {
                $cipherLength = openssl_cipher_iv_length(self::ALGORITHM);
                $bytes = openssl_random_pseudo_bytes($cipherLength);
                MainMenu::setOptionValue(MainMenu::IV_FIELD_NAME, $bytes);
            }
            return $bytes;
        }

        /**
         * Get the encryption key
         * 
         * @return string
         */
        private static function _getKey() : string 
        {
            $key = MainMenu::getOptionValue(MainMenu::KEY_FIELD_NAME);
            if (empty($key) === true) {
                $data = openssl_random_pseudo_bytes(self::DATA_LEN);
                $key = hash('sha256', $data, false);
                MainMenu::setOptionValue(MainMenu::KEY_FIELD_NAME, $key);
            }
            return $key;
        }
    }
}