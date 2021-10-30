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


// load the same posts
function getAndSetPosts() : array
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
    }
    return $posts;
}
