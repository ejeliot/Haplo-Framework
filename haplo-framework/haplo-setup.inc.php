<?php
    /**
     * HaploSetup - checks the environment supports the Haplo Framework and is properly configured
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploSetup
     **/
    class HaploSetup {
        /**
         * Run checks
         *
         * @static
         * @return void
         * @author Ed Eliot
         **/
        public static function validate() {
            if (ini_get('register_globals')) {
                throw new HaploException("Please disable register_globals in your php.ini file.");
            }
            
            if (get_magic_quotes_gpc()) {
                throw new HaploException("Please disable magic_quotes_gpc in your php.ini file.");
            }
            
            if (!is_dir(HAPLO_ACTIONS_PATH)) {
                throw new HaploException('Actions directory ('.HAPLO_ACTIONS_PATH.') not found.');
            }
            
            if (!is_dir(HAPLO_TEMPLATE_PATHS)) {
                throw new HaploException('Templates directory ('.HAPLO_TEMPLATE_PATHS.') not found.');
            }
            
            if (!is_dir(HAPLO_TEMPLATE_POST_FILTERS_PATH)) {
                throw new HaploException('Template post filters directory ('.HAPLO_TEMPLATE_POST_FILTERS_PATH.') not found.');
            }
            
            if (!is_dir(HAPLO_TEMPLATE_CUSTOM_FUNCTION_PATH)) {
                throw new HaploException('Template custom functions directory ('.HAPLO_TEMPLATE_CUSTOM_FUNCTION_PATH.') not found.');
            }
            
            if (!is_dir(HAPLO_CACHE_PATH)) {
                throw new HaploException('Cache directory ('.HAPLO_CACHE_PATH.') not found');
            }
            
            if (!is_writable(HAPLO_CACHE_PATH)) {
                throw new HaploException('Cache directory ('.HAPLO_CACHE_PATH.') is not writeable.');
            }
        }
    }
?>