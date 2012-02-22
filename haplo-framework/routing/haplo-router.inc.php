<?php
    /**
     * HaploRouter - this class routes requests by selecting an action from
     * a mapping array containing regular expressions for URL patterns and
     * their associated action
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploRouter
     **/
    
    class HaploRouter extends HaploSingleton {
        /**
         * Stores URL mappings passed to the class on instantiation
         *
         * @access protected
         * @var string
         **/
        protected $urls = array();
        
        /**
         * Contains values of matched variables in the selected URL pattern
         *
         * @access protected
         * @var string
         **/
        protected $requestVars = array();
        
        /**
         * Location of folder containing action files
         *
         * @access protected
         * @var string
         **/
        protected $actionsPath;
        
        /**
         * Raw action name (as specified in URL mapping)
         *
         * @access protected
         * @var string
         **/
        protected $action;
        
        /**
         * Path to selected action
         *
         * @access protected
         * @var string
         **/
        protected $actionPath;
        
        /**
         * Constructor for class
         *
         * @param array $urls URL pattern mappings to process - the first match found will be selected
         * @param string $actionsPath Path to folder which contains actions (by default ../actions relative to index.php)
         * @return void
         * @author Ed Eliot
         **/
        protected function __construct($urls, $actionsPath) {
            $this->urls = $urls;
            $this->actionsPath = $actionsPath;
        }
        
        /**
         * Static helper method used to ensure only one instance of the class is instantiated
         * This overrides the base version in the abstract HaploSingleton class because we 
         * need to support parameters
         *
         * @param array $urls URL pattern mappings t0 process - the first match found will be selected
         * @param string $actionsPath Path to folder which contains actions (by default ../actions relative to index.php)
         * @return HaploRouter
         * @author Ed Eliot
         **/
        public static function get_instance($urls) {
            global $config;
            
            $class = get_called_class();
            
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class($urls, $config->get_key('paths', 'actions'));
            }
            return self::$instances[$class];
        }
        
        /**
         * Appends a URL mapping pattern to those specified on class instantiation
         *
         * @param array $pattern Key/value pair containing URL pattern and action to map to
         * @return void
         * @author Ed Eliot
         **/
        public function append_url_pattern($pattern) {
            $this->urls[] = $pattern;
        }
        
        /**
         * Returns the selected action based on URL pattern matched against URL
         *
         * @return string The name of the selected action
         * @author Ed Eliot
         **/
        public function get_action() {
            $this->process($this->urls);
            
            if (!empty($this->actionPath)) {
                return $this->actionPath;
            }
            return false;
        }
        
        /**
         * Returns the class name corresponding to the selected action
         *
         * @return string the class name corresponding to the selected action
         * @author Ed Eliot
         **/
        public function get_action_class() {
            if (!empty($this->action)) {
                $parts = explode('/', $this->action);
                
                if (count($parts) > 1) {
                    $className = $parts[count($parts) - 1];
                } else {
                    $className = $this->action;
                }
                
                $className = str_replace('-', ' ', $className);
                $className = ucwords($className);
                $className = str_replace(' ', '', $className);
                
                if (class_exists($className)) {
                    return $className;
                }
            }
            return false;
        }
        
        /**
         * Provides a way to accessing matched URL sub-patterns, optionally set a default 
         * value if the specified sub-pattern wasn't found
         *
         * @param string $name Name of parameter to get value for
         * @param variant $default Default value to use if the parameter hasn't been set
         * @return void
         * @author Ed Eliot
         **/
        public function get_request_var($name, $default = null) {
            return (!empty($this->requestVars[$name]) ? $this->requestVars[$name] : $default);
        }
        
        /**
         * Get user's default locale from their browser
         *
         * @param string $default default locale to use
         * @return string locale - if not set use a default
         * @author Ed Eliot
         **/
        public function get_browser_locale($default = 'en-us') {
            if (
                !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) && 
                preg_match('/^[a-z-]+$/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'])
            ) {
                return strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            }
            return $default;
        }
        
        /**
         * Get request URI
         *
         * @return string
         * @author Ed Eliot
         **/
        public function get_request_uri() {
            return (!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
        }
        
        /**
         * Get IP address of client, takes into account proxies
         *
         * @return string
         * @author Ed Eliot
         **/
        public function get_remote_addr() {
            return (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        }
        
        /**
         * Get referring page
         *
         * @return string
         * @author Ed Eliot
         **/
        public function get_referer() {
            return (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        }
        
        /**
         * Helper method to get the HTTP request method used
         *
         * @return string
         * @author Ed Eliot
         **/
        public function get_request_method() {
            return (!empty($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : '');
        }
        
        /**
         * Check if the current request is made via AJAX
         *
         * @return boolean
         * @author Ed Eliot
         **/
        public function is_ajax() {
            return (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
            );
        }
        
        /**
         * Logic to work out which view should be loaded and process parameters
         *
         * @param array $urls URL patterns to process current URL against
         * @return void
         * @author Ed Eliot
         **/
        protected function process($urls) {
            // by default assume no match
            $match = false;
            
            // loop through list of URL regex patterns to find a match - 
            // processing stops as soon as a match is found so URL patterns 
            // should be ordered with the most important first
            foreach ($urls as $regEx => $dest) {
                $requestVars = array();
                
                // does the current URL match - if so capture matched sub-groups
                if (preg_match("#^$regEx(\?.*)?$#", $this->get_request_uri(), $this->requestVars)) {
                    // the first match is the full URL - we not really 
                    // interested in that so drop
                    array_shift($requestVars);
                    
                    // rule could be simple mapping to action or more complex 
                    // details containing mapping type etc
                    if (is_array($dest)) {
                        // if complex form a mapping type must be present (action, redirect, sub-patterns etc)
                        if (!empty($dest['type'])) {
                            switch ($dest['type']) {
                                // sets name of action in class property
                                case 'action':
                                    if (!empty($dest['action'])) {
                                        $this->set_action($dest['action'], $this->requestVars);
                                    } else {
                                        throw new HaploNoActionDefinedException("No action defined for $regEx.");
                                    }
                                    break;
                                // performs http redirect (301, 302, 404 etc)
                                case 'redirect':
                                    if (!empty($dest['url'])) {
                                        if (!empty($dest['code'])) {
                                            $this->redirect($dest['url'], $dest['code']);
                                        } else {
                                            $this->redirect($dest['url']);
                                        }
                                    } else {
                                        throw new HaploNoRedirectUrlDefinedException("No redirect URL defined for $regEx.");
                                    }
                                    break;
                                case 'sub-patterns':
                                    // process sub URL patterns - this allows one to create sub pattern matches under 
                                    // a base pattern for efficency reasons
                                    $subPatterns = array();
                                    
                                    foreach ($dest['sub-patterns'] as $subRegEx => $subDest) {
                                        $subPatterns[$regEx.$subRegEx] = $subDest;
                                    }
                                    $this->process($subPatterns);
                                    break;
                                default:
                                    throw new HaploActionTypeNotSupportedException("Action type not supported for $regEx. Should be one of 'action', 'redirect' or 'sub-patterns'.");
                            }
                        } else {
                            throw new HaploNoActionDefinedException("No action type defined for $regEx. Should be one of 'action', 'redirect' or 'sub-patterns'.");
                        }
                    } else {
                        $this->set_action($dest);
                    }
                    
                    $match = true;
                    // we don't want to continue processing patterns if a matching one has been found
                    break;
                }
            }
            
            // if none of the URL patterns matches look for a default 404 action
            if (!$match) {
                // send http 404 error header
                header('HTTP/1.1 404 Not Found');
                
                // check for existence of a 404 action
                if (file_exists("$this->actionsPath/page-not-found.php")) {
                    $this->set_action('page-not-found');
                } else {
                    throw new HaploNoDefault404DefinedException("No default 404 action found. Add a file named page-not-found.php to $this->actionsPath/ to suppress this message.");
                }
            }
            
            return $match;
        }
        
        /**
         *  Simply sets the name of the action to include - it isn't actually 
         *  included within this class so that the PHP it contains remains 
         *  outside the scope of the class
         *
         * @param string $action Name of action to set as selected
         * @param array $requestVars Details of request variables to try and map to dynamically generated action name
         * @return void
         * @author Ed Eliot
         **/
        protected function set_action($action, $requestVars = array()) {
            // map request variables to action name if applicable
            foreach ($this->requestVars as $key => $value) {
                $action = str_replace("<$key>", $value, $action);
            }
            
            $actionPath = "$this->actionsPath/$action.php";
            
            if (file_exists($actionPath)) {
                $this->action = $action;
                $this->actionPath = $actionPath;
            } else {
                $this->action = '';
                $this->actionPath = '';
                throw new HaploActionNotFoundException("Specified action ($action) hasn't been created.");
            }
        }
        
        /**
         * Do http redirect (301, 302, 404 etc)
         *
         * @param string $url URL to redirect to
         * @param integer $code HTTP code to send - probably 301 (permanently modified) or 302 (temporarily moved)
         * @return void
         * @author Ed Eliot
         **/
        protected function redirect($url, $code = 302) {
            // map request variables to URL if applicable
            foreach ($this->requestVars as $key => $value) {
                $url = str_replace("<$key>", $value, $url);
            }
            
            header("Location: $url", true, $code);
            exit;
        }
    }
?>
