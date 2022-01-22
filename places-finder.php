<?php
/**
 * Plugin Name: Places Finder
 * 
 * PHP version 7.4
 * 
 * @wordpress-plugin
 * Plugin Name: Places Finder
 * Plugin URI: https://github.com/jschroedr/places-finder
 * Description: Enrich custom location post types with places information.
 * Author: Jake Schroeder
 * Author URI: https://github.com/jschroedr/
 * Version 0.0.1
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
                break;
            }
        }
    }
);

use wpgp\MainMenu;
use wpgp\MetaBox;
use wpgp\BlockManager;

/**
 * Initialize the plugin
 * 
 * @return void
 */
function Wpgp_run() : void 
{
    // initialize menus
    MainMenu::init();
    MetaBox::init();

    $blockManager = new BlockManager('places-finder', __DIR__);
    $blockManager->init();
}
Wpgp_run();
