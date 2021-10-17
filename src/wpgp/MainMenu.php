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
    class MainMenu
    {
        public static function init() : void 
        {
            add_action(
                'admin_init',
                [self::class, 'initSettings']
            );
        }

        const PAGE_NAME = 'wpgp';
        const SETTINGS_SECTION_NAME = 'wpgp_settings';
        const API_KEY_FIELD_NAME = 'wpgp_api_key';
        const POST_TYPE_FIELD_NAME = 'wpgp_single_location_post_type';
        const OPTIONS_GROUP_NAME = 'wpgp_options';

        public static function initSettings() : void 
        {
            // Register a new setting for "wporg" page.
            register_setting(self::PAGE_NAME, self::OPTIONS_GROUP_NAME);
 
            // Register a new section in the "wporg" page.
            add_settings_section(
                self::SETTINGS_SECTION_NAME,
                __('Settings', 'wpgp'),
                [self::class, 'headerCallback'],
                'wpgp'
            );
 
            // Register a new field in the "wporg_section_developers" section, 
            // inside the "wporg" page.
            add_settings_field(
                self::POST_TYPE_FIELD_NAME,
                __('Api Key', 'wpgp'),
                [self::class, 'postTypeCallback'],
                'wpgp',
                self::SETTINGS_SECTION_NAME,
                array(
                    'label_for'         => self::POST_TYPE_FIELD_NAME,
                    'class'             => 'wpgp_row',
                    'wpgp_custom_data'  => 'custom',
                )
            );

            add_settings_field(
                self::API_KEY_FIELD_NAME,
                __('Api Key', 'wpgp'),
                [self::class, 'apiKeyCallback'],
                'wpgp',
                self::SETTINGS_SECTION_NAME,
                array(
                    'label_for'         => self::API_KEY_FIELD_NAME,
                    'class'             => 'wpgp_row',
                    'wpgp_custom_data'  => 'custom',
                )
            );
        }

        /**
         * Renders content between the heading and the fields
         * 
         * @param $args array the settings array
         * 
         * @return void
         */
        public static function headerCallback(array $args) : void 
        {
            ?>
                <h2 id="<?php echo $args['id'];?>">Settings</h2>
            <?php
        }

        /**
         * Displays the API Key Field in the Main Options Menu
         * 
         * @param $args array the field arguments
         * 
         * @return void
         */
        public static function apiKeyCallback(array $args) : void
        {
            $options = get_option(self::OPTIONS_GROUP_NAME);
            $labelFor = esc_attr($args['label_for']);
            $value = $options[$labelFor] ?? '';
            ?>
            <input
                id="<?php echo $labelFor;?>"
                name="<?php echo self::OPTIONS_GROUP_NAME;?>[<?php echo $labelFor; ?>]"
                type="password"
                value="<?php echo $value;?>"
            >
            <?php
        }

        /**
         * 
         */
        function postTypeCallback(array $args) : void
        {
            // Get the value of the setting we've registered with register_setting()
            $options = get_option(self::OPTIONS_GROUP_NAME);
            $labelFor = esc_attr($args['label_for']);
            $value = $options[$labelFor] ?? '';
            $postTypes = get_post_types([], 'names');
            ?>
            <label for="<?php echo $labelFor;?>">Select Post Type</label>
            <select
                    id="<?php echo $labelFor; ?>"
                    name="<?php echo self::OPTIONS_GROUP_NAME;?>[<?php echo $labelFor;?>]">
                <?php 
                foreach($postTypes as $postType) {
                    $selected = selected($value, $postType, false);
                    ?>
                    <option 
                        value="<?php echo $postType;?>"
                        <?php echo $selected; ?>
                    ><?php echo $postType;?></option>
                    <?php
                }
                ?>
            </select>
            <p class="description">
                <?php
                esc_html_e(
                    'Select the Post type you want to integrate with Google Places.',
                    'wpgp'
                ); 
                ?>
            </p>
            <?php
        }

    }
}