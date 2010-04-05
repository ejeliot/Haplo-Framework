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
    
    /**
     * include framework libraries and initial config
     * if you want to modify some of the settings specified in this file 
     * or include only parts of the Haplo Framework then you're probably 
     * best to create your own copy and link to that instead
     **/
    require('../includes/haplo-init.inc.php');
    // require('../includes/haplo-init-custom.inc.php');
    
    // set up URL mappings
    $urls = array(
        '/(?<template>[a-z0-9/-]*)' => 'static-page',
    );
    
    // check that the framework is set up correctly
    HaploSetup::validate();
    
    // create an instance of the router
    $router = HaploRouter::get_instance($urls);
    
    /**
     * filter input variables - GET, POST 
     * and REQUEST and apply other security settings
     * this line is optional but I strongly recommend adding it 
     * as it offers some basic protection against use of unfiltered 
     * content
     **/
    $securityFilter = HaploInputProtect::get_instance();
    
    // Add protection against cross site request forgeries (CSRF) in forms
    $csrfProtect = HaploCsrfProtect::get_instance();
    
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
    }
?>