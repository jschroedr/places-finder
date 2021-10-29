<?php
/**
 * MetaBox class module.
 * 
 * PHP version 7.4
 * 
 * @category Admin
 * @package  Wpgp
 * @author   Jake Schroeder <jake_schroeder@outlook.com>
 * @license  GNU v3
 * @link     https://github.com/jschroedr/wp-google-places
 */
namespace wpgp
{
    use \WP_Post;
    use \add_action;

    /**
     * Manages rendering of the Place ID meta box, and saving 
     * user-entered data on the single-location admin screen
     * 
     * @category Admin
     * @package  Wpgp
     * @author   Jake Schroeder <jake_schroeder@outlook.com>
     * @license  GNU v3
     * @link     https://github.com/jschroedr/wp-google-places
     */
    class MetaBox
    {
        /**
         * Register MetaBox action hooks
         * 
         * @return void
         */
        public static function init() : void
        {
            add_action(
                'add_meta_boxes',
                [self::class, 'add'],
                10,
                0
            );
            add_action(
                'save_post',
                [self::class, 'save'],
                10,
                3
            );
        }

        /**
         * Add the meta box to view in WP Admin
         * 
         * @return void
         */
        public static function add() : void
        {
            $screen = MainMenu::getOptionValue(MainMenu::POST_TYPE_FIELD_NAME);
            if (empty($screen) === true) {
                $screen = 'post';
            }
            add_meta_box(
                'wpgp-location-meta-box',
                'WP Google Places - Location',
                [self::class, 'render'],
                $screen,
                'side',
                'high'
            );
        }

        const PLACE_ID_KEY = 'wpgp-google-place-id';

        const NONCE_KEY = 'wpgp-location-nonce';
        
        public static function getMetaItem(int $postId, string $key) : string
        {
            return (string)get_post_meta($postId, $key, true);
        }

        public static function setMetaItem(
            int $postId, 
            string $key, 
            string $value
        ) : void {
            update_post_meta($postId, $key, $value);
        }

        /**
         * Shows the MetaBox on the pre-configred post type
         * 
         * @param WP_Post $object the active post in the wp admin menu
         * 
         * @return void
         */
        public static function render(WP_Post $object) : void
        {
            wp_nonce_field(basename(__FILE__), self::NONCE_KEY);
            $value = get_post_meta($object->ID, self::PLACE_ID_KEY, true);
            ?>
            <div>
                <label for="<?php echo self::PLACE_ID_KEY;?>">Google Place ID</label>
                <input
                    type="text"
                    name="<?php echo self::PLACE_ID_KEY;?>"
                    value="<?php echo $value;?>"
                >
            </div>
            <?php
        }

        /**
         * Saves the MetaBox data and binds it to the $post object
         * 
         * @param $postId int the ID of the post being saved
         * @param $post   WP_Post the post object being saved
         * @param $update bool whether or not this is an update or create
         * 
         * @return void
         */
        public static function save(int $postId, WP_Post $post, bool $update) : void
        {
            $nonce = $_POST[self::NONCE_KEY] ?? '';
            if (!wp_verify_nonce($nonce)) {
                error_log('wpgp\\MetaBox: Invalid Nonce on save()');
                return;
            }
            if (!current_user_can('edit_post', $postId)) {
                error_log('wpgp\\MetaBox: User not allowed to edit');
                return;
            }
            $value = $_POST[self::PLACE_ID_KEY] ?? '';
            update_post_meta($postId, self::PLACE_ID_KEY, $value);
        }

    }
}