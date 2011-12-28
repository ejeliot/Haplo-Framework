<?php
    /**
     * Haplo init file - default framework config file - create your own and
     * link to that in index.php instead if you want to modify the default
     * set up - don't forget most configuration can be changed by creating
     * override ini files instead
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package Haplo Init File
     **/
    
    require(HAPLO_FRAMEWORK_BASE.'/exceptions/haplo-exceptions.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/patterns//haplo-singleton.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/config/haplo-config.inc.php');
    
    // get instance of config helper
    // has to be instantated before the next set of includes 
    // as they use the config object
    $config = HaploConfig::get_instance();
    
    // include Haplo Framework files - enable or disable as required
    require(HAPLO_FRAMEWORK_BASE.'/sessions/haplo-session.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/misc/haplo-timing.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/routing/haplo-router.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/templating/haplo-template.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/templating/haplo-template-helper-functions.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/translations/haplo-translations.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/caching/haplo-cache.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/actions/haplo-action.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/security/haplo-nonce.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/db/haplo-db.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/db/haplo-model.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/logging/haplo-log.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/misc/haplo-http.inc.php');
    require(HAPLO_FRAMEWORK_BASE.'/misc/haplo-rss.inc.php');
    
    // this checks that certain options are set correctly, directories 
    // are writable where necessary etc - once you're happy everything is 
    // ok this can be safely commmented out or removed - no harm leaving it 
    // here if you want to though
    require(HAPLO_FRAMEWORK_BASE.'/setup/haplo-setup.inc.php');
    
    // autoload certain classes - at the moment only DB models
    spl_autoload_register(function($class) {
        // trying to load a model class
        if (strpos($class, 'Model') !== false) {
            $class = preg_replace('/([a-z0-9])?([A-Z])/', '$1-$2', $class);
            $class = strtolower($class);
            require_once(SITE_BASE.'/models/'.$class.'.inc.php');
        }
    });
?>