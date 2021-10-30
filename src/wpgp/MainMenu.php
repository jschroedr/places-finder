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
 * 
 * see: https://developer.wordpress.org/plugins/settings/custom-settings-page/
 */

namespace wpgp 
{

    /**
     * Manages main options menu display and data collection
     * 
     * @category Admin
     * @package  Wpgp
     * @author   Jake Schroeder <jake_schroeder@outlook.com>
     * @license  GNU v3
     * @link     https://github.com/jschroedr/wp-google-places
     */
    class MainMenu
    {

        /**
         * Initialize the main menu as part of WP admin initialization
         * 
         * @return void
         */
        public static function init() : void 
        {
            add_action(
                'admin_init',
                [self::class, 'initSettings']
            );
            add_action(
                'admin_menu',
                [self::class, 'addMenuPage']
            );
        }

        const PAGE_NAME = 'wpgp';
        const SETTINGS_SECTION_NAME = 'wpgp_settings';
        const SERVER_API_KEY_FIELD_NAME = 'wpgp_server_api_key';
        const POST_TYPE_FIELD_NAME = 'wpgp_single_location_post_type';
        const IV_FIELD_NAME = 'wpgp_iv';
        const KEY_FIELD_NAME = 'wpgp_key';
        const OPTIONS_GROUP_NAME = 'wpgp_options';

        /**
         * Initialize the main options menu
         * 
         * @return void
         */
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
                self::SERVER_API_KEY_FIELD_NAME,
                __('Server Api Key', 'wpgp'),
                [self::class, 'apiKeyCallback'],
                'wpgp',
                self::SETTINGS_SECTION_NAME,
                array(
                    'label_for'         => self::SERVER_API_KEY_FIELD_NAME,
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
            $message = 'Configure the behavior of WP Google Places.';
            ?>
                <p id="<?php echo $args['id'];?>"><?php echo $message;?></p>
            <?php
        }

        /**
         * Gets the wp options name from the option label
         * 
         * @param $labelFor string
         * 
         * @return string
         */
        private static function _getFieldName(string $labelFor) : string
        {
            return self::OPTIONS_GROUP_NAME . '[' . $labelFor . ']';
        }
        /**
         * Get a menu option by name
         * 
         * @param $fieldName string
         * 
         * @return string
         */
        public static function getOptionValue(string $fieldName) : string
        {
            $options = get_option(self::OPTIONS_GROUP_NAME);
            return (string) ($options[$fieldName] ?? '');
        }
        
        /**
         * Set a menu option by name
         * 
         * @param $fieldName string
         * @param $value     string
         * 
         * @return void
         */
        public static function setOptionValue(
            string $fieldName, 
            string $value
        ) : void {
            $options = get_option(self::OPTIONS_GROUP_NAME);
            $options[$fieldName] = $value;
            update_option(self::OPTIONS_GROUP_NAME, $options, false);
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
                name="<?php echo self::_getFieldName($labelFor);?>"
                type="password"
                value="<?php echo $value;?>"
            >
            <?php
        }

        /**
         * Display the post type selector.
         * 
         * @param $args array the arguments for the field
         * 
         * @return void
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
                    name="<?php echo self::_getFieldName($labelFor);?>">
                <?php 
                foreach ($postTypes as $postType) {
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

        /**
         * Register the main menu option under the Settings menu
         * 
         * @return void
         */
        public static function addMenuPage() : void
        {
            add_submenu_page(
                'options-general.php',
                'WP Google Places',
                'WP Google Places',
                'manage_options',
                self::PAGE_NAME,
                [self::class, 'renderPage']
            );
        }

        /**
         * Show the main options menu settings page
         * 
         * @return void
         */
        public static function renderPage() : void 
        {
            // check user capabilities
            if (!current_user_can('manage_options')) {
                return;
            }
            
            // check if the user have submitted the settings
            // WordPress will add the "settings-updated" $_GET parameter
            // to the url
            if (isset($_GET['settings-updated'])) {
                // add settings saved message with the class of "updated"
                add_settings_error(
                    'wpgp_messages',
                    'wpgp_message',
                    __('Settings Saved', 'wpgp'),
                    'updated'
                );
            }
            // show error/update messages
            settings_errors('wporg_messages');
            ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "wpgp"
                settings_fields(self::PAGE_NAME);
                // output setting sections and their fields
                // (sections are registered for "wpgp", 
                // each field is registered to a specific section)
                do_settings_sections(self::PAGE_NAME);
                // output save settings button
                submit_button('Save Settings');
                ?>
                </form>
            </div>
            <?php
        }
    }
}