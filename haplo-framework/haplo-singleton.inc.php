<?php
    /**
     * HaploSingleton - abstract class to define a simple Singleton pattern
     * The constructor isn't included in this class due to PHP < 5.3 lack of 
     * support for late static binding and varying parameters in descendent classes
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploSingleton
     **/
    abstract class HaploSingleton {
        /**
         * Holds a reference to an instance of the instantiated class
         * Used to facilitate implementation of the class as a singleton
         *
         * @var object
         **/
        protected static $instances;
        
        public function __clone() {
            throw new HaploException('Cloning of '.self::$class.' not allowed.');
        }
        
        /**
         * define a get_instance() method in the inherited class
         * we can't define it here due to differing parameters and a lack of support 
         * for late static binding in PHP < 5.3
         **/
    }
?>