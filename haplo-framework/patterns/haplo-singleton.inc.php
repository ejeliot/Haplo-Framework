<?php
    /**
     * HaploSingleton - abstract class to define a simple Singleton pattern
     * The constructor isn't included in this class due to PHP < 5.3 lack of
     * support for late static binding and varying parameters in descendent classes
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
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
            throw new HaploCloningNotAllowedException('Cloning of '.get_called_class().' not allowed.');
        }
        
        /**
         * define a get_instance() method in the inherited class
         * we can't define it here due to differing parameters
         **/
    }
?>