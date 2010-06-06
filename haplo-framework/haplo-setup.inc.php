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
            global $config;
            
            if (ini_get('register_globals')) {
                throw new HaploException("Please disable register_globals in your php.ini file.");
            }
            
            if (get_magic_quotes_gpc()) {
                throw new HaploException("Please disable magic_quotes_gpc in your php.ini file.");
            }
            
            if (!is_dir($config->get_key('paths', 'actions'))) {
                throw new HaploException('Actions directory ('.$config->get_key('paths', 'actions').') not found.');
            }
            
            if (!is_dir($config->get_key('paths','templates'))) {
                throw new HaploException('Templates directory ('.$config->get_key('paths', 'templates').') not found.');
            }
            
            if (!is_dir($config->get_key('paths', 'postFilters'))) {
                throw new HaploException('Template post filters directory ('.$config->get_key('paths', 'postFilters').') not found.');
            }
            
            if (!is_dir($config->get_key('paths', 'customTemplateFunctions'))) {
                throw new HaploException('Template custom functions directory ('.$config->get_key('paths', 'customTemplateFunctions').') not found.');
            }
            
            if (!is_dir($config->get_key('paths','translations'))) {
                throw new HaploException('Translations directory ('.$config->get_key('paths', 'translations').') not found.');
            }
            
            if (!is_dir($config->get_key('paths', 'cache'))) {
                throw new HaploException('Cache directory ('.$config->get_key('paths', 'cache').') not found');
            }
            
            if (!is_writable($config->get_key('paths', 'cache'))) {
                throw new HaploException('Cache directory ('.$config->get_key('paths', 'cache').') is not writeable.');
            }
        }
    }
?>