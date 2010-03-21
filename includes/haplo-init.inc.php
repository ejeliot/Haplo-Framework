<?php
    /**
     * Haplo init - default framework config file - create your own in you 
     * want to modify default set up
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package Haplo init file
     **/
    
    /** path to Haplo Framework- the same files can be used by multiple projects so 
     * move the haplo-framework directory wherever best suits your set up and update 
     * this value accordingly
     **/
    define('HAPLO_FRAMEWORK_PATH', '../haplo-framework');
    
    /** whether or not to show exceptions - this should always be set to false or error 
     * types set to file (rather than screen) in production environments
     **/
    define('HAPLO_SHOW_EXCEPTIONS', true);
    define('HAPLO_SHOW_EXCEPTIONS_TYPE', 'screen'); // one of "screen" or "file"
    
    /** whether or not to show errors - this should always be set to false or error 
     * types set to file (rather than screen) in production environments
     **/
    define('HAPLO_SHOW_ERRORS', true);
    define('HAPLO_SHOW_ERRORS_TYPE', 'screen'); // one of "screen" or "file"
    
    /** show script timing information - this should really only be enabled during 
     * debugging to help you to analyse page load time
     **/
    define('HAPLO_SHOW_TIMING', false);
    
    /** defines path to action files - this should work fine for a default set up
     * but modify to suit your set up
     **/
    define('HAPLO_ACTIONS_PATH', '../actions');
    
    /**
     * defines path(s) to template files - you can specify multiple search paths
     * the first path containing the specified file will be used - this is useful 
     * for providing localised templates for example
     * when using multiple paths separate with a comma. For example:
     * ../templates/locales/en,../templates
     * if necessary you can build up paths dynamically
     **/
    define('HAPLO_TEMPLATE_PATHS', '../templates');

    // should HTML be stripped from assigned variables by default
    define('HAPLO_TEMPLATE_STRIP_HTML', false);

    /**
     * should HTML special characters be converted automatically
     * be careful when disabling this - you may be exposing your app to XSS exploits
     **/
    define('HAPLO_TEMPLATE_CONVERT_ENTITIES', true);
    // if so what charset to use
    define('HAPLO_TEMPLATE_ENCODING', 'UTF-8');
    
    // defines path to post filter functions
    define('HAPLO_TEMPLATE_POST_FILTERS_PATH', '../post-filters');
    
    // defines path to custom template functions
    define('HAPLO_TEMPLATE_CUSTOM_FUNCTION_PATH', '../custom-template-functions');
    
    // path to translation files
    define('HAPLO_TRANSLATIONS_PATH', '../translations');
    
    // default language to use
    define('HAPLO_TRANSLATIONS_DEFAULT_LANG', 'en-us');
    
    /** determines whether or not someone can add ?showKeys=true 
     * to the end of a URL (assuming suitable URL routing) to view translation keys 
     * instead of the acutal translations
     **/
    define('HAPLO_TRANSLATIONS_ALLOW_SHOW_KEYS', true);
    
    /**
     * what cache library to use - for the moment only file is supported 
     * but memcached will be added in due course
     **/
    define('HAPLO_CACHE_LIBRARY', 'file');
    
    // length of time to cache items for - default is 5 minutes
    define('HAPLO_CACHE_LENGTH', 300);
    
    /** location of cache files - this directory should be writable 
     * by the web  server process
     **/
    define('HAPLO_CACHE_PATH', '../cache');
    
    // include Haplo Framework files - enable or disable as required
    require(HAPLO_FRAMEWORK_PATH.'/haplo-setup.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-timing.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-exception.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-error.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-router.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-input-protect.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-csrf-protect.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-template.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-translations.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-cache.inc.php');
?>