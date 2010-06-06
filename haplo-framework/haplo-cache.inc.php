<?php
    /**
     * HaploCache - a factory class for instantiating different types of caching libraries
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
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
                throw new HaploException("Cache library ($libraryClassName) not found.");
            }
        }
    }
?>