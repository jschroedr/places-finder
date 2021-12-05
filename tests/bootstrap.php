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


xdebug_connect_to_client();


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
    $users = get_users();
    $user = $users[0];
    $posts = getTestPosts();
    $postObjects = [];
    foreach ($posts as $post) {
        $id = $post['id'];
        $wpPost = get_post($id);
        if (is_null($wpPost)) {
            $result = wp_insert_post(
                [
                    'import_id' => $id,
                    'post_title' => $post['post_title'],
                    'post_status' => $post['post_status'],
                    'post_author' => $user->ID,
                ],
                true,
            );
        } else {
            $result = wp_update_post(
                [
                    'ID' => $wpPost->ID,
                    'post_title' => $post['post_title'],
                    'post_status' => $post['post_status'],
                    'post_author' => $user->ID,
                ],
                true,
            );
        }
        if (is_wp_error($result)) {
            print($result->get_error_message());
        }
        $postObjects[] = get_post($id);
        $placeId = $post['place_id'];
        if (empty($placeId) === false) {
            MetaBox::setMetaItem($post['id'], MetaBox::PLACE_ID_KEY, $placeId);
        }
    }
    return $postObjects;
}


function getTestPlaceIdPost() : object
{
    // the first post is an acceptable test candidate
    return getAndSetTestPosts()[0];
}


function getTestPlaceIdPostEmpty() : object
{
    // the last post does not have a place id
    return getAndSetTestPosts()[3];
}
