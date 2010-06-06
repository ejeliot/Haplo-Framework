<?php
    /**
     * Haplo init - default framework config file - create your own in you 
     * want to modify default set up
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package Haplo init file
     **/
     
    /**
     * path to haplo framework directory
     **/
    define('HAPLO_FRAMEWORK_PATH', '../haplo-framework');
      
    /**
     * path to haplo config directory
     **/
    define('HAPLO_CONFIG_PATH', '../config');
    
    // include Haplo Framework files - enable or disable as required
    require(HAPLO_FRAMEWORK_PATH.'/haplo-exception.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-error.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-singleton.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-setup.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-timing.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-router.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-template.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-translations.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-cache.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-action.inc.php');
    require(HAPLO_FRAMEWORK_PATH.'/haplo-config.inc.php');
    
    // get instance of config helper
    $config = HaploConfig::get_instance();
    
    // check that the framework is set up correctly
    HaploSetup::validate();
?>