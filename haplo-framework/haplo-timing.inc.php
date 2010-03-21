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
        
        public static function start_timer() {
            self::$startTime = microtime(true);
        }
        
        public static function shutdown() {
            if (HAPLO_SHOW_TIMING) {
                echo '
                    <style type="text/css">
                        .haplo-timer { border: 2px solid #ff9900; background: #eee; padding: 10px; margin: 10px 0; }
                        .haplo-timer * { padding: 0; margin: 0; font-family: Arial; font-size: 14px; }
                        .haplo-timer p, .haplo-timer pre { margin: 5px; }
                        .haplo-timer pre { font-family: Courier New; white-space: pre-wrap; }
                        .haplo-timer strong { font-weight: bold; }
                    </style>
                ';
                printf('
                    <div class="haplo-timer">
                        <p><strong>Haplo Framework Timer</strong></p>
                        <pre>Script execution time: %01.5f</pre>
                    </div>', (microtime(true) - self::$startTime)
                );
            }
        } 
    }
    
    HaploTiming::start_timer();
    
    register_shutdown_function(array('HaploTiming', 'shutdown'));
?>