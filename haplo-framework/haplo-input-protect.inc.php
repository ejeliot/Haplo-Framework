<?php
    /**
     * HaploInputProtect- library for applying a basic layer of input filtering 
     * to PHP superglobals
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploInputProtect
     **/
    class HaploInputProtect extends HaploSingleton {
        /**
         * Stores unaltered values
         *
         * @var string
         **/
        protected $requestVars = array();
        
        /**
         * Constructor - not called directly as we instantiate this class as a singleton
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function __construct() {
            $this->requestVars['GET'] = $_GET;
            $this->requestVars['POST'] = $_POST;
            $this->requestVars['COOKIE'] = $_COOKIE;
            $this->requestVars['SERVER'] = $_SERVER;
            
            $this->filter_get_post_cookie();
            $this->remove_unused_superglobals();
            $this->filter_specific_server_values();
        }
        
        /**
         * Static helper method used to ensure only one instance of the class is instantiated
         * This overrides the base version in the abstract HaploSingleton class 
         * because PHP < 5.3 doesn't support late static binding
         *
         * @return HaploInputProtect
         * @author Ed Eliot
         **/
        public static function get_instance() {
            $class = __CLASS__;
            
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class;
            }
            return self::$instances[$class];
        }
        
        /**
         * Method for getting get, post, cookie or server values with specified filter type
         * Use FILTER_UNSAFE_RAW to get unfiltered value
         *
         * @param int $type GET, POST, COOKIE or SERVER
         * @param string $name Key for value to retrieve
         * @param $filter Filter to use - see http://www.php.net/manual/en/filter.constants.php
         * @param $options As per PHP manual entry for filter_var
         * @return variant
         * @author Ed Eliot
         **/         
        public function get_request_var($type, $name, $filter = FILTER_SANITIZE_STRING, $options = array()) {
            if (isset($this->requestVars[$type][$name])) {
                return filter_var($this->requestVars[$type][$name], $filter, $options);
            }
            return false;
        }
        
        /**
         * Filters GET, POST and COOKIE values
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function filter_get_post_cookie() {
            $_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
            $_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
            $_COOKIE = filter_input(INPUT_COOKIE, FILTER_SANITIZE_STRING);
        }
        
        /**
         * Filters old and unused superglobal arrays
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function remove_unused_superglobals() {
            /**
             * this one might be used but probably best to explicitly
             * check get, post, cookie etc
             * disable this line if you really need to
             **/
            unset($_REQUEST);
            
            // these are depricated and shouldn't be used
            unset($HTTP_GET_VARS);
            unset($HTTP_POST_VARS);
            unset($HTTP_COOKIE_VARS);
            unset($HTTP_REQUEST_VARS);
            unset($HTTP_SERVER_VARS);
            unset($HTTP_ENV_VARS);
            unset($HTTP_SESSION_VARS);
        }
        
        /**
         * Filters commonly misused variables
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function filter_specific_server_values() {
            // this variable is often echoed straight out into form tags 
            // thereby creating a potential for XSS
            $_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);
        }
    }
?>