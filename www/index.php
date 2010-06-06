<?php
    /**
     * An example controller file - you can add as many of these as you
     * need. It instantiates the routing code with a list of URL pattern
     * matches.                                                         
     *                                                                  
     * URLs can take one of two formats:                                
     * /index.php/news/                                                 
     * or                                                               
     * /news/                                                           
     * if you want to use the second form you'll need                   
     * mod_rewrite enabled and support for .htaccess files.             
     * To improve performance you can alternatively add the rules       
     * found in www/.htaccess to your Apache virtual host configuration.
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     **/
     
     /* debug - remove for production */
     error_reporting(E_ALL);
     ini_set('display_errors', 1);
    
    /**
     * include framework libraries and initial config
     **/
    require('../haplo-framework/haplo-init.inc.php');
    
    // set up URL mappings
    $urls = array(
        // an example of 301 redirecting URLs without trailing slash to corresponding 
        // URLs with trailing slash (exclude paths ending with .html)
        '/(?<path>.+[^/|\.html])' => array(
            'type' => 'redirect',
            'url' => '/<path>/',
            'code' => 301
        ),
        // map everyting else to static-page action
        '/(?<template>[a-z0-9/-]*)' => 'static-page',
    );
    
    // create an instance of the router
    $router = HaploRouter::get_instance($urls);
    
    /**
     * set up support for translations
     * the language you pass should probably be a locale code (e.g. en-us)
     **/
    $translations = new HaploTranslations('en-us');
    // an example of using the locale specified by the browser
    // $translations = new HaploTranslations($router->get_browser_locale('en-us'));
    
    // load selected action
    if ($action = $router->get_action()) {
        require($action);
        
        if ($actionClass = $router->get_action_class()) {
            // should be able to user $action::get_instance() but it isn't supported in PHP < 5.3
            call_user_func_array(array($actionClass, 'get_instance'), array(array(
                'router' => $router,
                'translations' => $translations,
                'config' => $config // global config object - instantiated in haplo-init.inc.php
            )));
        }
    }
?>