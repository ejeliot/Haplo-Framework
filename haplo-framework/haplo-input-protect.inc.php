<?php
    /**
     * HaploInputProtect- library for applying a basic layer of input filtering 
     * to PHP superglobals
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploInputProtect
     **/
    class HaploInputProtect {
        /**
         * Stores unaltered values
         *
         * @var string
         **/
        protected $requestVars = array();
        
        public function __construct() {
            $this->requestVars['GET'] = $_GET;
            $this->requestVars['POST'] = $_POST;
            $this->requestVars['COOKIE'] = $_COOKIE;
            $this->requestVars['SERVER'] = $_SERVER;
            
            $this->filter_get_post_cookie();
        }
        
        public function get_request_var($type, $name, $filter = FILTER_SANITIZE_STRING, $options = array()) {
            if (!isset($this->requestVars[$type][$name])) {
                return filter_var($this->requestVars[$type][$name], $filter, $options);
            }
            return false;
        }
        
        protected function filter_get_post_cookie() {
            $_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
            $_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
            $_COOKIE = filter_input(INPUT_COOKIE, FILTER_SANITIZE_STRING);
        }
        
        protected function filter_specific_server_values() {
            // this variable is often echoed straight out into form tags 
            // thereby creating a potential for XSS
            $_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);
        }
        
        protected function remove_unused_superglobals() {
            unset($_REQUEST);
            
            unset($HTTP_GET_VARS);
            unset($HTTP_POST_VARS);
            unset($HTTP_COOKIE_VARS);
            unset($HTTP_REQUEST_VARS);
            unset($HTTP_SERVER_VARS);
            unset($HTTP_ENV_VARS);
            unset($HTTP_SESSION_VARS);
        }
    }
?>