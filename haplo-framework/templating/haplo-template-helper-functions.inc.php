<?php
    /****************************************************************************************/
    /* Haplo Template Helper Functions                                                      */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
    
    function get_nonce_field($name = 'nonce', $trailingSlash = false) {
        global $nonce;
        return sprintf('<input type="hidden" name="nonce" value="%s"%s>', $nonce->get($name), ($trailingSlash ? ' /' : ''));
    }
    
    function get_config_key($section, $key, $default = '') {
        global $config;
        return $config->get_key_or_default($section, $key, $default);
    }
?>