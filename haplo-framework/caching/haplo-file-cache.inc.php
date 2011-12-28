<?php
    /****************************************************************************************/
    /* HaploFileCache - file based caching library                                          */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
    
    /**
     * @author Ed Eliot
     * @package HaploFileCache
     **/
    class HaploFileCache {
        protected $file;
        protected $fileLock;
        protected $cacheTime;
        protected $shortKey;
        protected static $cache = array();

        /**
         * Constructor for class
         *
         * @param string $key Key for cached data to look up/set
         * @param integer $cacheTime Length of time to cache data for (in seconds)
         * @param string $cachePath File path for location of cache files
         * @return void
         * @author Ed Eliot
         **/
        public function __construct($key, $cacheTime) {
            global $config;
            
            $this->shortKey = 'HAPLO-CACHE-'.md5($key);
            $this->file = $config->get_key('paths', 'cache')."/$this->shortKey.txt";
            $this->fileLock = "$this->file.lock";
            $this->cacheTime = $cacheTime;
        }

        /**
         * Checks if cache item exists and content isn't stale
         *
         * @return boolean
         * @author Ed Eliot
         **/
        public function check() {
            if (array_key_exists($this->shortKey, self::$cache) || file_exists($this->fileLock)) {
                return true;
            }
            return (file_exists($this->file) && ($this->cacheTime == -1 || time() - filemtime($this->file) <= $this->cacheTime));
        }

        /**
         * Checks if cache item exists but isn't concerned if content is stale
         *
         * @return boolean
         * @author Ed Eliot
         **/
        public function exists() {
            return (array_key_exists($this->shortKey, self::$cache)) || (file_exists($this->file) || file_exists($this->fileLock));
        }

        /**
         * Cache some content
         *
         * @param variant $contents Data to cache
         * @return boolean
         * @author Ed Eliot
         **/
        public function set($contents) {
            if (!file_exists($this->fileLock)) {
                if (file_exists($this->file)) {
                    copy($this->file, $this->fileLock);
                }
                $file = fopen($this->file, 'w');
                fwrite($file, serialize($contents));
                fclose($file);
                if (file_exists($this->fileLock)) {
                    unlink($this->fileLock);
                }
                self::$cache[$this->shortKey] = $contents;
                return true;
            }     
            return false;
        }

        /**
         * Get the contents of the cache
         *
         * @return variant
         * @author Ed Eliot
         **/
        public function get() {
            if ($this->exists()) {
                if (array_key_exists($this->shortKey, self::$cache)) {
                    return self::$cache[$this->shortKey];
                } else if (file_exists($this->fileLock)) {
                    self::$cache[$this->shortKey] = unserialize(file_get_contents($this->fileLock));
                    return self::$cache[$this->shortKey];
                } else {
                    self::$cache[$this->shortKey] = unserialize(file_get_contents($this->file));
                    return self::$cache[$this->shortKey];
                }
            }
            return false;

        }

        /**
         * Revalidates stale content in cache
         *
         * @return void
         * @author Ed Eliot
         **/
        public function re_validate() {
            touch($this->file);
        }
    }
?>
