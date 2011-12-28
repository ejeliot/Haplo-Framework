<?php
    /**
     * Example index.php
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework 
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     **/
    
    ini_set('display_errors', true); // remove this line in production environment
    error_reporting(E_ALL | E_STRICT);
    
    /**
     * --------------------------------------------------------------
     * define system paths
     * --------------------------------------------------------------
     **/
     
    // path to the folder which contains everything
    define('BASE_PATH', __DIR__.'/../..');
    // path to the site folder
    define('SITE_BASE', __DIR__.'/..');
    // path to the haplo-framework files
    define('HAPLO_FRAMEWORK_BASE', BASE_PATH.'/haplo-framework');
    // path to the folder that contains config ini files
    define('HAPLO_CONFIG_PATH', SITE_BASE.'/config');
    
    /**
     * --------------------------------------------------------------
     * include haplo framework files
     * --------------------------------------------------------------
     **/
    require(HAPLO_FRAMEWORK_BASE.'/haplo-init.inc.php');
    
    /**
     * --------------------------------------------------------------
     * include custom files
     * --------------------------------------------------------------
     **/
     
     // add your files here
    
     /**
      * --------------------------------------------------------------
      * set up URL mappings
      * --------------------------------------------------------------
      **/
    $urls = array(
        '/home/' => array( // rewrite default home action
            'type' => 'redirect',
            'url' => '/',
            'code' => 301
        ),
        '/(?<template>[a-z0-9/-]*)' => 'static-page'
    );
    
    /**
     * --------------------------------------------------------------
     * create an instance of the router and pass in URL mappings
     * --------------------------------------------------------------
     **/
    $router = HaploRouter::get_instance($urls);
    
    /**
     * --------------------------------------------------------------
     * create new session
     * --------------------------------------------------------------
     **/
    HaploSession::create(
        $config->get_key('sessions', 'name'),
        $config->get_key('sessions', 'store'),
        $config->get_key('sessions', 'servers')
    );
    
    /**
     * --------------------------------------------------------------
     * set up support for translations
     * --------------------------------------------------------------
     **/
    $locale = 'en-us'; // hard coded locale
    //$locale = $router->get_browser_locale('en-us'); // set based on browser
    $translations = new HaploTranslations($locale);
    
    /**
     * --------------------------------------------------------------
     * load selected action
     * --------------------------------------------------------------
     **/
    if ($action = $router->get_action()) {
        require($action);
        
        if ($actionClass = $router->get_action_class()) {
            $actionClass::get_instance(
                // pass any other objects that need to be accessible to the 
                // action class in this array
                array(
                    'router' => $router,
                    'translations' => $translations,
                    'config' => $config // global config object - instantiated in haplo-init.inc.php
                )
            );
        } else {
            throw new HaploClassNotFoundException("Action class doesn't exist for action ".$action);
        }
    }
?>