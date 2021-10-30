<?php
/**
 * Plugin Name: WP Google Places
 * 
 * PHP version 7.4
 * 
 * @category Admin
 * @package  Wpgp
 * @author   Jake Schroeder <jake_schroeder@outlook.com>
 * @license  GNU v3
 * @link     https://github.com/jschroedr/wp-google-places
 */

spl_autoload_register(
    function (string $className) {
        if (DIRECTORY_SEPARATOR !== '\\') {
            $className = str_replace('\\', '/', $className);
        }
        $dirs = ['src', 'tests'];
        foreach ($dirs as $dir) {
            $directory = __DIR__ . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
            $file = $directory . $className . '.php';
            if (file_exists($file)) {
                include_once $file;
            }
        }
    }
);

use wpgp\MainMenu;
use wpgp\MetaBox;


/**
 * Initialize the plugin
 * 
 * @return void
 */
function Wpgp_run() : void 
{
    MainMenu::init();
    MetaBox::init();
}
Wpgp_run();
