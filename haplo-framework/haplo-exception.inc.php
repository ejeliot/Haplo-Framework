<?php
    /**
     * HaploException - a wrapper to PHP's default exception class
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploException
     **/
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
            return sprintf("%s: %s\n", __CLASS__, $this->message);
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
                    .haplo-error strong { font-weight: bold; }
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
                        <p>%s</p>
                    </div>
                ', $exception);
            }
        }
    }
    
    // set up custom exception handler
    set_exception_handler(array('HaploException', 'handler'));
?>