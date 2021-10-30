<?php
/**
 * Cache class module.
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
     * Manages caching of file-based contents for the plugin
     * 
     * @category Utility
     * @package  Wpgp
     * @author   Jake Schroeder <jake_schroeder@outlook.com>
     * @license  GNU v3
     * @link     https://github.com/jschroedr/wp-google-places
     */
    class Cache
    {

        const MAX_FILE_AGE = 3600;

        /**
         * Get $data from the file indexed by $key in the Cache,
         * so long as the file is within MAX_FILE_AGE old
         * 
         * @param $key       string
         * @param $partition string
         * 
         * @return string
         */
        public static function get(string $key, string $partition = null) : string
        {
            $filename = self::_getFilePath($key, $partition);
            if (is_file($filename) === false) {
                // file does not exist
                return '';
            } elseif (self::_checkFileAge($filename) === false) {
                // remove the stale file
                unlink($filename);
                return '';
            } else {
                // file exists and is fresh enough to use
                $contents = file_get_contents($filename);
                return Encryption::decrypt($contents);
            }
        }

        /**
         * Set the $data into a file indexed by $key in the Cache
         * 
         * @param $key       string
         * @param $data      string
         * @param $partition string the directory to partition the data by
         * 
         * @return void
         */
        public static function set(
            string $key, 
            string $data, 
            string $partition = null
        ) : void {           
           
            // check that the cache is initialized
            self::_checkCacheDirectory($partition);
            
            // encode and encrypt data 
            $data = utf8_encode($data);
            $data = Encryption::encrypt($data);

            // set encrypted data in folder
            $filename = self::_getFilePath($key, $partition);
            $result = file_put_contents($filename, $data);
            if ($result === false) {
                error_log('wpgp\\Cache: Could not save file');
            }
            return;
        }

        /**
         * Get the full file path for a given $key and $partition (subdirectory)
         * 
         * @param $key       string
         * @param $partition string
         * 
         * @return string
         */
        private static function _getFilePath(
            string $key, 
            string $partition = null
        ) : string {
            $dir = self::_getDir();
            if (is_null($partition) === false) {
                $dir = $dir . DIRECTORY_SEPARATOR . $partition;
            }
            return $dir . DIRECTORY_SEPARATOR . "$key.txt";
        }

        /**
         * Calculates whether the age of the cached item is acceptable for use
         * 
         * @param $filename string
         * 
         * @return bool
         */
        private static function _checkFileAge(string $filename) : bool
        {
            $fileTime = filemtime($filename);
            $currentTime = time();
            $diff = $currentTime - $fileTime;
            return $diff <= self::MAX_FILE_AGE;
        }

        const DIR_NAME = 'wpgp';

        /**
         * Get the upload directory
         * 
         * @return string
         */
        private static function _getDir() : string
        {
            $dir = wp_get_upload_dir()['basedir']; 
            if (is_dir($dir) === false) {
                print($dir);
                mkdir($dir);
            }
            $dir .= DIRECTORY_SEPARATOR . self::DIR_NAME;
            return $dir;
        }

        /**
         * Check and create the cache directory in wp-uploads
         * 
         * @param $partition string
         * 
         * @return void
         */
        private static function _checkCacheDirectory(string $partition = null) : void
        {
            // check and create the cache directory
            $dir = self::_getDir();
            if (is_dir($dir) === false) {
                print($dir);
                mkdir($dir);
            }
            // check and create the partition
            if (is_null($partition) === false) {
                $subDir = $dir . DIRECTORY_SEPARATOR . $partition;
                if (is_dir($subDir) === false) {
                    print($subDir);
                    mkdir($subDir);
                }
            }
        }

    }
}