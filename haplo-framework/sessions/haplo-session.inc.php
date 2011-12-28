<?php
    /**
     * HaploSession - sets up basic handling of sessions
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework 
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploSession
     **/
    
    class HaploSession {
        /**
         * Create a new session with a given name
         *
         * @param string $name Name of session
         * @param string $store Type of storage to use - file or memcache
         * @param array $servers Comma list of servers to use (memcache)
         * @return void
         * @author Ed Eliot
         **/
        public static function create($name, $store = 'file', $servers = array()) {
            if ($store == 'memcache') {
                self::use_memcache($servers);
            }
            
            session_name($name);
            session_start();
        }
        
        /**
         * Regenerates the session
         * Call this before logging a user in or calling other important functions
         *
         * @return void
         * @author Ed Eliot
         **/
        public static function regenerate() {
            session_regenerate_id();
        }
        
        /**
         * Specify memcache servers to use
         *
         * @param string $servers Comma separated list of servers to use
         * @return void
         * @author Ed Eliot
         **/
        protected static function use_memcache($servers) {
            ini_set('session.save_handler', 'memcache');
            ini_set('session.save_path', implode(',', $servers));
        }
    }
?>