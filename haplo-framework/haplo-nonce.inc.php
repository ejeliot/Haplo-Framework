<?php
    class HaploNonce extends HaploSingleton {
        protected function __construct() {
            global $config;
            
            session_name($config->get_key('sessions', 'name'));
            session_start();
        }
        
        public function get_instance() {
            $class = get_called_class();
            
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class();
            }
            return self::$instances[$class];
        }
        
        public function create($name = 'nonce', $force = false) {
            global $config;
            
            if (empty($_SESSION[$name]) || $force) {
                $_SESSION[$name] = sha1($config->get_key('nonce', 'secret').uniqid());
            
                return $_SESSION[$name];
            }
            
            return false;
        }
        
        public function check($name = 'nonce') {
            $result = (!empty($_SESSION[$name]) && $_SESSION[$name] == $_REQUEST[$name]);
            $this->create($name, true);
            
            return $result;
        }
        
        public function get($name = 'nonce') {
            return $_SESSION[$name];
        }
    }
?>