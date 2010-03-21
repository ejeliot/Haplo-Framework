<?php
    /**
     * HaploError - a wrapper to the default error handling
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploError
     **/
    class HaploError {
        /**
         * The handler function that gets called when an error occurs
         *
         * @param integer $errorNo The error number/code
         * @param string $errorStr The error message
         * @param string $errorFile Filename that the error was raised in
         * @param integer $errorLine The line number that the error occurred on
         * @return boolean
         * @author Ed Eliot
         **/
        public static function handler($errorNo, $errorStr, $errorFile, $errorLine) {
            if (HAPLO_SHOW_ERRORS) {
                switch ($errorNo) {
                    case E_NOTICE:
                    case E_USER_NOTICE:
                        $errorType = 'Notice';
                        break;
                    case E_WARNING:
                    case E_USER_WARNING:
                        $errorType = 'Warning';
                        break;
                    case E_ERROR:
                    case E_USER_ERROR:
                        $errorType = 'Fatal Error';
                        break;
                    default:
                        $errorType = 'Unknown';
                        break;
                }
        
                echo HaploException::get_error_css();
                printf('
                    <div class="haplo-error">
                        <p><strong>Haplo Framework Error</strong></p>
                        <pre><strong>%s:</strong> %s <strong>in</strong> %s <strong>on</strong> %s</pre>
                    </div>
                ', $errorType, $errorStr, $errorFile, $errorLine);
            }
        
            return true;
        }
    }
    
    // set up custom error handler
    set_error_handler(array('HaploError', 'handler'));
?>