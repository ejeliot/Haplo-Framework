<?php
    /**
     * An example routing file - you can add as many of these as you
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
    
    /**
     * include framework libraries and initial config
     **/
    require('../includes/init.inc.php');
    
    // set up URL mappings, expressions are processed in order 
    // until a match is found
    $urls = array(
        // an example of 301 redirecting URLs without trailing slash to corresponding 
        // URLs with trailing slash to prevent duplicate URLs 
        // (exclude paths ending with .html)
        '/(?<path>.+[^/|\.html])' => array(
            'type' => 'redirect',
            'url' => '/<path>/',
            'code' => 301
        ),
        // redirect default /welcome/ page to / to prevent duplicate URLs
        '/welcome/' => array(
            'type' => 'redirect',
            'url' => '/',
            'code' => 301
        ),
        '/nonce-example/' => 'nonce-example',
        // map everyting else to static-page action
        '/(?<template>[a-z0-9/-]*)' => 'static-page',
    );
    
    // create an instance of the router and pass in URL mappings
    $router = HaploRouter::get_instance($urls);
    
    // create new session
    HaploSession::create(
        $config->get_key('sessions', 'name'),
        $config->get_key('sessions', 'store'),
        $config->get_key('sessions', 'servers')
    );
    
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
        
        // actions can optionally contain a class which is instantiated if present, otherwise 
        // straight PHP is assumed
        if ($actionClass = $router->get_action_class()) {
            $actionClass::get_instance(
                // pass any other objects that need to be accessible to the 
                // action classes in this array
                array(
                    'router' => $router,
                    'translations' => $translations,
                    'config' => $config // global config object - instantiated in haplo-init.inc.php
                )
            );
        }
    }
?>