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
         * Stores original options passed to class
         *
         * @var array
         **/
        protected $options;
        
        /**
         * Stores details of the order in which methods are run
         * Override to change order
         *
         * @var array
         **/
        protected $methodOrder = array(
            'do_init', 
            'do_get', 
            'do_post', 
            'do_head', 
            'do_put', 
            'do_delete', 
            'do_all'
        );
        
        /**
         * Class constructor - not called directly as the class is instantiated as a Singleton
         *
         * @params array $options Key/value pair array containing references to router, translations and other objects
         * @return void
         * @author Ed Eliot
         **/
        protected function __construct($options) {
            $this->options = $options;
            
            foreach ($options as $key => $value) {
                $this->$key = $value;
            }
            
            $requestMethod = $this->get_request_method();
            
            foreach ($this->methodOrder as $methodType) {
                $methodName = $methodType;
                
                if (in_array($methodType, array(
                    'do_get', 
                    'do_post', 
                    'do_head', 
                    'do_put', 
                    'do_delete'
                ))) {
                    if ("do_$requestMethod" == $methodType) {
                        $this->$methodName();
                        
                        if (in_array($requestMethod, array('get', 'post')) {
                            $methodName = 'do_'.$requestMethod.'_validate';
                            
                            if ($this->$validateMethodName()) {
                                $methodName = 'do_'.$requestMethod.'_success()';
                                $this->$methodName();
                            } else {
                                $methodNamr = 'do_'.$requestMethod.'_failure()';
                                $this->$methodName();
                            }
                        }
                    } else {
                        $methodName = str_replace('do_', 'do_all_except_', $methodName);
                        $this->$methodName();
                    }
                } else {
                    $this->$methodName();
                }
            }
        }
        
        /**
         * Static helper method for getting an instance of the class
         *
         * @static
         * @param array $options Key/value pair array containing references to router, translations and other objects
         * @return HaploAction (descendent class)
         * @author Ed Eliot
         **/
        public static function get_instance($options = array()) {
            $class = get_called_class();
            
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
                'do_init', 'do_get', 'do_post', 'do_head', 'do_put', 'do_delete', 
                'do_all_except_get', 'do_all_except_post', 'do_all_except_head', 
                'do_all_except_put', 'do_all_except_delete', 'do_all', 'do_get_validate', 
                'do_get_success', 'do_get_failure', 'do_post_validate', 
                'do_post_success', 'do_post_failure'
            ))) {
                throw new HaploException(
                    sprintf('Method %s not defined in %s.', $name, get_called_class()), 
                    HAPLO_METHOD_NOT_FOUND_EXCEPTION
                );
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
            
            $actionsPath = $this->config->get_key('paths', 'actions');
            $action = $actionsPath.'/page-not-found.php';
            
            if (file_exists($action)) {
                require($action);
                
                if (class_exists('PageNotFound')) {
                    PageNotFound::get_instance($this->options);
                }
                
                exit;
            } else {
                throw new HaploException(
                    'No default 404 action found. Add a file named page-not-found.php to '.$actionsPath.' to suppress this message.', 
                    HAPLO_ACTION_NOT_FOUND_EXCEPTION
                );
            }
        }
        
        /**
         * Redirect to another URL
         *
         * @param string $url URL to redirect to
         * @param integer $code HTTP code to send
         * @return void
         * @author Ed Eliot
         **/
        protected function do_redirect($url, $code = 302) {
            header("Location: $url", true, $code);
            exit;
        }
        
        /**
         * Permanently redirect to another URL
         *
         * @param string $url URL to redirect to
         * @return void
         * @author Ed Eliot
         **/
        protected function do_301($url) {
            $this->do_redirect($url, 301);
        }
        
        /**
         * Temporarily redirect to another URL
         *
         * @param string $url URL to redirect to
         * @return void
         * @author Ed Eliot
         **/
        protected function do_302($url) {
            $this->do_redirect($url, 302);
        }
    }
?>