<?php
/**
 * BlockManager class module.
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

namespace wpgp {


    /**
     * Manages Gutenberg Block Initialization.
     * 
     * @category Admin
     * @package  Wpgp
     * @author   Jake Schroeder <jake_schroeder@outlook.com>
     * @license  GNU v3
     * @link     https://github.com/jschroedr/places-finder
     */
    class BlockManager
    {

        private string $_name;
        private string $_path;

        public function __construct(string $name, string $path)
        {
            $this->_name = $name;
            $this->_path = $path;
        }

        public function init() : void
        {
            $this->_loadTranslations();
            $this->_registerAll();
        }

        public function loadTextDomain() : void 
        {
            load_plugin_textdomain(
                $this->_name,
                false,
                basename($this->_path) . '/languages'
            );
        }
    
        public function register() : void 
        {
            $this->_registerSearchResult();
            $setScriptTranslationFunc = 'wp_set_script_translations';
            if (function_exists($setScriptTranslationFunc) === true) {
                /**
                 * May be extended to wp_set_script_translations( 'my-handle', 'my-domain',
                 * plugin_dir_path( MY_PLUGIN ) . 'languages' ) ). For details see
                 * https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
                 */
                $setScriptTranslationFunc($this->name, $this->name);
            }
        }

        private function _loadTranslations() : void
        {
            add_action(
                'init',
                [$this, 'loadTextDomain'],
            );
        }
    

        private static function _registerAll() : void
        {
            add_action(
                'init',
                'gutenberg_examples_05_esnext_register_block',
            );
        }

        private function _registerSearchResult() : void
        {
            // Register the block by passing the location of block.json to register_block_type.
            register_block_type($this->_path . '/blocks/searchresult/block.json');
        }
    }
}
