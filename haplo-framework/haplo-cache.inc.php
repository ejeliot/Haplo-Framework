<?php
    /**
     * HaploCache - a factory class for instantiating different types of caching libraries
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploCache
     **/
     
    class HaploCache {
        public static function get_object($key, $cacheTime = HAPLO_CACHE_LENGTH, $type = HAPLO_CACHE_LIBRARY) {
            $libraryPath = HAPLO_FRAMEWORK_PATH."/haplo-$type-cache.inc.php";
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