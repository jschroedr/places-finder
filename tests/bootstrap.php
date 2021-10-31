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


function getTestPosts() : array
{
    return json_decode(file_get_contents(__DIR__ . '/data/posts.json'), true);
}

// load the same posts
function getAndSetTestPosts() : array
{
    $posts = getTestPosts();
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
        $placeId = $post['place_id'];
        if (empty($placeId) === false) {
            MetaBox::setMetaItem($post['id'], MetaBox::PLACE_ID_KEY, $placeId);
        }
    }
    return $posts;
}


function getTestPlaceIdPost() : array
{
    // the first post is an acceptable test candidate
    return getTestPosts()[0];
}


function getTestPlaceIdPostEmpty() : array
{
    // the last post does not have a place id
    return getTestPosts()[3];
}
