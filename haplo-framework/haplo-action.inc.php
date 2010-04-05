<?php
    /**
    * HaploAction - abstract class that provides the base functionality 
    * for descendent action classes
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploAction
     **/ 
    abstract class HaploAction extends HaploSingleton {
        /**
         * Class constructor - not called directly as the class is instantiated as a Singleton
         *
         * @params array $options Key/value pair array containing references to router, translations and other objects
         * @return void
         * @author Ed Eliot
         **/
        protected function __construct($options) {
            foreach ($options as $key => $value) {
                $this->$key = $value;
            }
            
            $requestMethod = $this->get_request_method();
            
            if ($requestMethod == 'get') {
                $this->do_get();
            } else {
                $this->do_get_other();
            }
            
            if ($requestMethod == 'post') {
                $this->do_post();
                
                if ($this->validate()) {
                    $this->validate_success();
                } else {
                    $this->validate_failure();
                }
            } else {
                $this->do_post_other();
            }
            
            if ($requestMethod == 'head') {
                $this->do_head();
            } else {
                $this->do_head_other();
            }
            
            if ($requestMethod == 'put') {
                $this->do_put();
            } else {
                $this->do_put_other();
            }
            
            if ($requestMethod == 'delete') {
                $this->do_delete();
            } else {
                $this->do_delete_other();
            }
            
            $this->do_all();
        }
        
        /**
         * Static helper method for getting an instance of the class
         *
         * @static
         * @param array $options Key/value pair array containing references to router, translations and other objects
         * @param string $class This is never set, it simply ensures the right class name is used in the descendent class
         * @return HaploAction (descendent class)
         * @author Ed Eliot
         **/
        public static function get_instance($options = array(), $class = __CLASS__) {
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class($options);
            }
            return self::$instances[$class];
        }
        
        /**
         * Check for valid method calls
         *
         * @param string $name Name of method being run
         * @param array $arguments Arguments to pass to the method
         * @return void
         * @author Ed Eliot
         **/
        public function __call($name, $arguments) {
            if (!in_array($name, array(
                'do_get', 'do_post', 'do_head', 'do_put', 'do_delete', 'do_get_other', 
                'do_post_other', 'do_head_other', 'do_put_other', 'do_delete_other', 'do_all'
            ))) {
                throw HaploException("Method $name not defined.");
            }
        }
        
        /**
         * Helper method to get the HTTP request method used
         *
         * @return string
         * @author Ed Eliot
         **/
        protected function get_request_method() {
            if (!empty($_SERVER['REQUEST_METHOD'])) {
                return strtolower($_SERVER['REQUEST_METHOD']);
            }
            return false;
        }
        
        /**
         * Return a 404 header and page
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function do_404() {
            header('HTTP/1.1 404 Not Found');
            
            $action = HAPLO_ACTIONS_PATH.'/404.php';
            
            if (file_exists($action)) {
                require($action);
                exit;
            } else {
                throw new HaploException('No default 404 action found. Add a file named 404.php to '.HAPLO_ACTIONS_PATH.' to suppress this message.');
            }
        }
    }
?>