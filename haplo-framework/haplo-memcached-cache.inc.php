<?php
    class HaploMemcachedCache {
        protected $key;
        protected $cacheTime;
        protected static $cache = array();
        
        public function __construct($key, $cacheTime) {
            $this->memcached = new Memcached();
            $this->memcached->addServer('127.0.0.1', 11211);
            
            $this->key = md5($key);
            $this->cacheTime = $cacheTime;
        }
        
        public function check() {
            return array_key_exists($this->key, self::$cache);
        }
        
        public function exists() {
            // unable to check expired cache item
            return false;
        }
        
        public function set($contents) {
            self::$cache[$this->key] = $contents;
            return $this->memcached->set($this->key, $contents, time() + $this->cacheTime);
        }
        
        public function get() {
            if (isset(self::$cache[$this->key])) {
                return self::$cache[$this->key];
            }
            
            return $this->memcached->get($this->key);
        }
        
        public function re_validate() {
            // unable to re-validate expired cache item
            return false;
        }
    }
?>