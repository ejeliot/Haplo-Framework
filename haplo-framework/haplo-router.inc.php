<?php
    /**
     * HaploRouter - this class routes requests by selecting an action from 
     * a mapping array containing regular expressions for URL patterns and 
     * their associated action
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
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
         * Constructor for class
         *
         * @param array $urls URL pattern mappings to process - the first match found will be selected
         * @param string $actionsPath Path to folder which contains actions (by default ../actions relative to index.php)
         * @return void
         * @author Ed Eliot
         **/
        protected function __construct($urls, $actionsPath = HAPLO_ACTIONS_PATH) {
            $this->urls = $urls;
            $this->actionsPath = $actionsPath;
        }
        
        /**
         * Static helper method used to ensure only one instance of the class is instantiated
         * This overrides the base version in the abstract HaploSingleton class because we 
         * need to support parameters and because PHP < 5.3 doesn't support late static binding
         *
         * @param array $urls URL pattern mappings t0 process - the first match found will be selected
         * @param string $actionsPath Path to folder which contains actions (by default ../actions relative to index.php)
         * @return HaploRouter
         * @author Ed Eliot
         **/
        public static function get_instance($urls, $actionsPath = HAPLO_ACTIONS_PATH) {
            $class = __CLASS__;
            
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class($urls, $actionsPath);
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
            
            if (!empty($this->action)) {
                return $this->action;
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
            if (!empty($this->requestVars[$name])) {
                return $this->requestVars[$name];
            }
            return $default;
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
                preg_match('/[a-z-]+/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'])
            ) {
                return strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            }
            return $default;
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
                
                // URL patterns would be too ugly if one had to escape each /
                $regExRaw = str_replace('/', '\/', $regEx);
                
                // does the current URL match - if so capture matched sub-groups
                if (preg_match("/^$regExRaw$/i", $_SERVER['REQUEST_URI'], $this->requestVars)) {
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
                                        throw new HaploException("No action defined for $regEx.");
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
                                        throw new HaploException("No redirect URL defined for $regEx.");
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
                                    throw new HaploException("Action type not supported for $regEx. Should be one of 'action', 'redirect' or 'sub-patterns'.");
                            }
                        } else {
                            throw new HaploException("No action type defined for $regEx. Should be one of 'action', 'redirect' or 'sub-patterns'.");
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
                if (file_exists("$this->actionsPath/404.php")) {
                    $this->set_action('404');
                } else {
                    throw new HaploException("No default 404 action found. Add a file named 404.php to $this->actionsPath/ to suppress this message.");
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
            
            $action = "$this->actionsPath/$action.php";
            
            if (file_exists($action)) {
                $this->action = $action;
            } else {
                $this->action = '';
                throw new HaploException("Specified action ($action) hasn't been created.");
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
