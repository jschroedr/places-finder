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
use wpgp\Location;
use wpgp\BlockRating;


function Wpgp_render_rating( array $block_attributes, string $content) : string
{
    // get the review data from the api
    $location = new Location(get_the_ID());  // current post id and no cache

    if ($location->reviewCount === 0) {
        if ($block_attributes['no_reviews_five_stars']) {
            return BlockRating::ratingToStars(5, 'color:yellow;');
        } else {
            return BlockRating::ratingToStars(0, 'color:yellow;');
        }
    }
    $stars = BlockRating::ratingToStars($location->rating, 'color:yellow');
    return "$stars ($location->reviewCount)";
}


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

    //$blockManager = new BlockManager('places-finder', __DIR__);
    //$blockManager->init();

    /**
    * Registers the block using the metadata loaded from the `block.json` file.
    * Behind the scenes, it registers also all assets so they can be enqueued
    * through the block editor in the corresponding context.
    *
    * @see https://developer.wordpress.org/reference/functions/register_block_type/
    */
    $blocksPath = __DIR__ . DIRECTORY_SEPARATOR . 'blocks';
    $editorScript = $blocksPath . DIRECTORY_SEPARATOR . 'rating' . DIRECTORY_SEPARATOR . 'src';
    $result = register_block_type(
        $editorScript,
        [
            'api_version' => 2,
            'render_callback' => 'Wpgp_render_rating',
        ]
    );
    if ($result === false) {
        throw new Exception('Block init failed');
    }
}

add_action('init', 'Wpgp_run');
