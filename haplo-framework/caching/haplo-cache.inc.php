<?php
    /**
     * HaploCache - a factory class for instantiating different types of caching libraries
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework 
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploCache
     **/
    
    class HaploCache {
        /**
         * Factory method for getting a cache object of different types (file, memcached)
         *
         * @param string $key Key to store data against
         * @param integer $cacheTime Length of time in seconds to cache data for
         * @param string $type String containing library type to use (file, memcached)
         * @return object
         * @author Ed Eliot
         **/
        public static function get_object($key, $cacheTime = null) {
            global $config;
            
            if (is_null($cacheTime)) {
                $cacheTime = $config->get_key('cache', 'length');
            }
            $type = $config->get_key('cache', 'type');
            $libraryPath = $config->get_key('paths', 'framework')."/haplo-$type-cache.inc.php";
            $libraryClassName = 'Haplo'.ucfirst($type).'Cache';
            
            if (file_exists($libraryPath)) {
                require_once($libraryPath);
                return new $libraryClassName($key, $cacheTime);
            } else {
                throw new HaploLibraryNotFoundException("Cache library ($libraryClassName) not found.");
            }
        }
    }
?>