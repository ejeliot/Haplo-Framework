<?php
    class HaploSession {
        public static function create($name, $store = 'file', $servers = '') {
            if ($store == 'memcache') {
                self::use_memcache($servers);
            }
            
            session_name($name);
            session_start();
        }
        
        // call this before logging a user in or calling other 
        // important functions
        public static function regenerate() {
            session_regenerate_id();
        }
        
        protected static function use_memcache($servers) {
            ini_set('session.save_handler', 'memcache');
            ini_set('session.save_path', $servers);
        }
    }
?>