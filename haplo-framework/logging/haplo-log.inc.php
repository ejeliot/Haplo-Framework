<?php
    /**
     * HaploLog
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploLog
     **/
    
    class HaploLog {
        static public function log_error($message) {
            global $config;
            
            if ($config->get_key_or_default('logging', 'errors', true)) {
                error_log($message, 3, $config->get_key('logging', 'errorFile'));
            }
        }
        
        static public function log_info() {
            global $config;
            
            if ($config->get_key_or_default('logging', 'info', true)) {
                error_log($message, 3, $config->get_key('logging', 'infoFile'));
            }
        }
    }
?>