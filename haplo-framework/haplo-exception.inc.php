<?php
    /**
     * HaploException - a wrapper to PHP's default exception class
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploException
     **/
     
    define('HAPLO_UNDEFINED_EXCEPTION', 1);
    define('HAPLO_METHOD_NOT_FOUND_EXCEPTION', 2);
    define('HAPLO_ACTION_NOT_FOUND_EXCEPTION', 3);
    define('HAPLO_LIBRARY_NOT_FOUND_EXCEPTION', 4);
    define('HAPLO_CONFIG_PARSE_FILE_EXCEPTION', 5);
    define('HAPLO_CONFIG_KEY_NOT_FOUND_EXCEPTION', 6);
    define('HAPLO_CONFIG_SECTION_NOT_FOUND_EXCEPTION', 7);
    define('HAPLO_ROUTER_NO_ACTION_DEFINED_EXCEPTION', 8);
    define('HAPLO_ROUTER_NO_REDIRECT_URL_DEFINED_EXCEPTION', 9);
    define('HAPLO_ROUTER_ACTION_TYPE_NOT_SUPPORTED_EXCEPTION', 10);
    define('HAPLO_NO_DEFAULT_404_DEFINED_EXCEPTION', 11);
    define('HAPLO_PHP_CONFIG_EXCEPTION', 12);
    define('HAPLO_DIR_NOT_FOUND_EXCEPTION', 13);
    define('HAPLO_DIR_NOT_WRITABLE_EXCEPTION', 14);
    define('HAPLO_CLONING_NOT_ALLOWED_EXCEPTION', 15);
    define('HAPLO_INVALID_TEMPLATE_EXCEPTION', 16);
    define('HAPLO_CUSTOM_TEMPLATE_FUNCTION_NOT_FOUND_EXCEPTION', 17);
    define('HAPLO_POST_FILTER_FUNCTION_NOT_EXCEPTION', 18);
    define('HAPLO_TEMPLATE_NOT_FOUND_EXCEPTION', 19);
    define('HAPLO_LANG_FILE_NOT_FOUND_EXCEPTION', 20);
    define('HAPLO_TRANSLATION_KEY_NOT_FOUND_EXCEPTION', 21);
    define('HAPLO_METHOD_NOT_IMPLEMENTED_EXCEPTION', 22);
    define('HAPLO_NONCE_MISMATCH', 23);
    
    class HaploException extends Exception {
        /**
         * Constructor for class
         *
         * @param string $message The exception message to display
         * @param integer Custom exception code
         * @return void
         * @author Ed Eliot
         **/
        public function __construct($message, $code = 0) {
            // make sure everything is assigned properly
            parent::__construct($message, $code);
        }
        
        /**
         * Overridden magic method to output a string representation of the class
         *
         * @return string
         * @author Ed Eliot
         **/
        public function __toString() {
            return sprintf("
                <p>%s: [%d] %s</p>
                <ul>
                    <li>File: %s</li>
                    <li>Line: %d</li>
                </ul>
                <p><strong>Stack Trace</strong></p>
                <pre>%s</pre>
            ", __CLASS__, $this->code, $this->message, $this->file, $this->line, $this->getTraceAsString());
        }
        
        /**
         * Return CSS to format error message
         *
         * @return string
         * @author Ed Eliot
         **/
        public static function get_error_css() {
            return '
                <style type="text/css">
                    .haplo-error { border: 2px solid #ff9900; background: #eee; padding: 10px; margin: 10px 0; }
                    .haplo-error * { padding: 0; margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 14px; }
                    .haplo-error p { margin: 5px; }
                    .haplo-error strong { font-size: 16px; font-weight: bold; text-decoration: underline; }
                    .haplo-error ul { margin: 20px 0; }
                    .haplo-error li { list-style: none; margin-left: 20px; }
                    .haplo-error pre { margin: 10px 0 0 20px; }
                </style>
            ';
        }
        
        /**
         * The handler function that gets called when an exception occurs
         *
         * @param Exception $exception Details of the exception that has been thrown
         * @return void
         * @author Ed Eliot
         **/
        public static function handler($exception) {
            global $config;
            
            if ($config->get_key('exceptions', 'show')) {
                echo self::get_error_css();
                printf('
                    <div class="haplo-error">
                        <p><strong>Haplo Framework Exception</strong></p>
                        %s
                    </div>
                ', $exception);
            }
        }
    }
    
    // set up custom exception handler
    set_exception_handler(array('HaploException', 'handler'));
?>