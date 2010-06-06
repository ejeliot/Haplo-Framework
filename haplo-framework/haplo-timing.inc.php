<?php
    /**
     * HaploTiming - times script execution
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploTiming
     **/
    class HaploTiming {
        protected static $startTime;
        
        /**
         * Start timing script
         *
         * @return void
         * @author Ed Eliot
         **/
        public static function start_timer() {
            self::$startTime = microtime(true);
        }
        
        /**
         * Stop timing script and output time taken
         *
         * @return void
         * @author Ed Eliot
         **/
        public static function shutdown() {
            global $config;
            
            if ($config->get_key('timing', 'show')) {
                echo '
                    <style type="text/css">
                        .haplo-timer { border: 2px solid #ff9900; background: #eee; padding: 10px; margin: 10px 0; }
                        .haplo-timer * { padding: 0; margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 14px; }
                        .haplo-timer p, .haplo-timer pre { margin: 5px; }
                        .haplo-timer strong { font-weight: bold; }
                    </style>
                ';
                printf('
                    <div class="haplo-timer">
                        <p><strong>Haplo Framework Timer</strong></p>
                        <p>Script execution time: %01.5f</p>
                    </div>', (microtime(true) - self::$startTime)
                );
            }
        } 
    }
    
    // start timing
    HaploTiming::start_timer();
    
    // register shutdown function to output time taken
    register_shutdown_function(array('HaploTiming', 'shutdown'));
?>