<?php
    /**
     * HaploSetup - checks the environment supports the Haplo Framework and is properly
     * configured
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
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
                throw new HaploPhpConfigException('Please disable register_globals in your php.ini file.');
            }
            
            if (get_magic_quotes_gpc()) {
                throw new HaploPhpConfigException('Please disable magic_quotes_gpc in your php.ini file.');
            }
            
            if (!is_dir($config->get_key('paths', 'actions'))) {
                throw new HaploDirNotFoundException('Actions directory ('.$config->get_key('paths', 'actions').') not found.');
            }
            
            if (!is_dir($config->get_key('paths','templates'))) {
                throw new HaploDirNotFoundException('Templates directory ('.$config->get_key('paths', 'templates').') not found.');
            }
            
            if (!is_dir($config->get_key('paths', 'postFilters'))) {
                throw new HaploDirnotFoundException('Template post filters directory ('.$config->get_key('paths', 'postFilters').') not found.');
            }
            
            if (!is_dir($config->get_key('paths','translations'))) {
                throw new HaploDirNotFoundException('Translations directory ('.$config->get_key('paths', 'translations').') not found.');
            }
            
            if (!is_dir($config->get_key('paths', 'cache'))) {
                throw new HaploDirNotFoundException('Cache directory ('.$config->get_key('paths', 'cache').') not found');
            }
            
            if (!is_writable($config->get_key('paths', 'cache'))) {
                throw new HaploDirNotWritableException('Cache directory ('.$config->get_key('paths', 'cache').') is not writeable.');
            }
        }
    }
    
    // check that the framework is set up correctly
    HaploSetup::validate();
?>