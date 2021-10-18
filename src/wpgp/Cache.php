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
         * @param $key string
         * 
         * @return string
         */
        public static function get(string $key) : string
        {
            $key = self::_sanitizeKey($key);
            $dir = self::_getDir();

            // scan the cache files
            $files = scandir($dir);
            foreach ($files as $file) {
                // upon a match on $key - check file age before
                // returning contents
                if (strpos($file, $key) !== false) {
                    $filename = $dir . DIRECTORY_SEPARATOR . $file;
                    if (self::_checkFileAge($file) === true) {
                        // get and decrypt the file contents
                        $contents = file_get_contents($filename);
                        return Encryption::decrypt($contents);
                    } else {
                        // remove the stale file
                        unlink($filename);
                    }
                }
            }
            // return empty if not found
            return '';
        }

        /**
         * Set the $data into a file indexed by $key in the Cache
         * 
         * @param $key  string
         * @param $data string
         * 
         * @return void
         */
        public static function set(string $key, string $data) : void
        {           
            $key = self::_sanitizeKey($key);
            $dir = self::_getDir();
            
            // check that the cache is initialized
            self::_checkCacheDirectory();
            
            // encrypt data 
            $data = Encryption::encrypt($data);

            // set encrypted data in folder
            $filename = $dir . DIRECTORY_SEPARATOR . self::_getFileName($key);
            $result = file_put_contents($filename, $data);
            if ($result === false) {
                error_log('wpgp\\Cache: Could not save file');
            }
            return;
        }

        /**
         * Calculates whether the age of the cached item is acceptable for use
         * 
         * @param $file string
         * 
         * @return bool
         */
        private static function _checkFileAge(string $file) : bool
        {
            $fileTime = self::_getFileTime($file);
            $currentTime = time();
            $diff = $currentTime - $fileTime;
            return $diff <= self::MAX_FILE_AGE;
        }

        const SEPARATOR = '~';

        /**
         * Remove the separator from the key to ensure accurate storage and retrieval
         * 
         * @param $key string
         * 
         * @return string
         */
        private static function _sanitizeKey(string $key) : string
        {
            return str_replace(self::SEPARATOR, '', $key);
        }

        /**
         * Parse the time from the file name as an int (utc timestamp)
         * 
         * @param $file string the filename (no directory) to parse the time from
         * 
         * @return int
         */
        private static function _getFileTime(string $file) : int
        {
            return (int)explode(self::SEPARATOR, $file)[1];
        }

        /**
         * Generate a time-bound filename using the given $key
         * 
         * @param $key string the name of the file from the caller
         * 
         * @return string
         */
        private static function _getFileName(string $key) : string
        {
            $time = time();
            return "$key~$time.txt";
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
            $dir .= DIRECTORY_SEPARATOR . self::DIR_NAME;
            return $dir;
        }

        /**
         * Check and create the cache directory in wp-uploads
         * 
         * @return void
         */
        private static function _checkCacheDirectory() : void
        {
            $dir = self::_getDir();
            if (is_dir($dir) === false) {
                mkdir($dir);
            }
        }

    }
}