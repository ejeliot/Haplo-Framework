<?php
    class HaploDb extends HaploSingleton {
        protected function __construct() {
            
        }
        
        public static function get_instance() {
            $class = get_called_class();
            
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class();
            }
            return self::$instances[$class];
        }
    }
?>