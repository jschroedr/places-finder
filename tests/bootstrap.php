<?php
/**
 * The bootstrap file for PHPUnit.
 * 
 * PHP Version 7
 * 
 * @category TESTCASE
 * @package  WPGP
 * @author   Jake Schroeder <jake_schroeder@outlook.com>
 * @license  GNU v3
 * @link     https://github.com/jschroedr/wp-google-places
 */


/* load Wordpress
 * if using docker-compose, this will always be the path in the workspace
 */
require_once '/var/www/html/wp-load.php';


use wpgp\MetaBox;


function getTestPlaceId() : string 
{
    return $_ENV['WPGP_TEST_PLACE_ID'];
}


// load the same posts
function getAndSetTestPosts() : array
{
    $posts = json_decode(file_get_contents(__DIR__ . '/data/posts.json'), true);
    foreach ($posts as $post) {
        $id = $post['id'];
        $wpPost = get_post($id);
        if (is_null($wpPost)) {
            wp_insert_post(
                [
                    'import_id' => $id,
                    'post_title' => $post['post_title'],
                    'post_status' => $post['post_status'],
                ]
            );
        } else {
            wp_update_post(
                [
                    'ID' => $id,
                    'post_title' => $post['post_title'],
                    'post_status' => $post['post_status'],
                ]
            );
        }
        if ($post['use_place_data']) {
            MetaBox::setMetaItem($post['id'], MetaBox::PLACE_ID_KEY, $post['place_id']);
        }
    }
    return $posts;
}


function getTestPlaceIdPost() : array
{
    $placeId = getTestPlaceId();
    $posts = getAndSetTestPosts();
    foreach ($posts as $post) {
        if ($post['place_id'] === $placeId) {
            return $post;
        }
    }
    error_log('Could not find test post, check $_ENV');
    return [];
}
