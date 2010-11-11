<?php
    /**
     * Haplo init - default framework config file - create your own and 
     * link to that in index.php instead if you want to modify the default 
     * set up - don't forget most configuration can be changed by creating 
     * override ini files instead
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package Haplo init file
     **/
    
    require('haplo-singleton.inc.php');
    require('haplo-config.inc.php');
    
    // get instance of config helper
    // has to be instantated before the next set of includdes 
    // as they use the config object
    $config = HaploConfig::get_instance();
    
    // include Haplo Framework files - enable or disable as required
    require('haplo-error.inc.php');
    require('haplo-exception.inc.php');
    require('haplo-timing.inc.php');
    require('haplo-router.inc.php');
    require('haplo-template.inc.php');
    require('haplo-translations.inc.php');
    require('haplo-cache.inc.php');
    require('haplo-action.inc.php');
    require('haplo-nonce.inc.php');
    
    // this checks that certain options are set correctly, directories 
    // are writable where necessary etc - once you're happy everything is 
    // ok this can be safely commmented out or removed - no harm leaving it 
    // here if you want to though
    require('haplo-setup.inc.php');
?>