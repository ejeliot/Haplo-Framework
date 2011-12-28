<?php
    /****************************************************************************************/
    /* HaploMemcachedCache                                                                  */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
    
    class HaploMemcachedCache {
        protected $key;
        protected $cacheTime;
        protected static $cache = array();
        
        public function __construct($key, $cacheTime, $servers = array()) {
            $this->memcached = new Memcached();
            
            if (!empty($servers)) {
                foreach ($servers as $server) {
                    list($server, $port) = explode(':', $server);
                    $this->memcached->addServer($server, $port);
                }
            } else {
                $this->memcached->addServer('127.0.0.1', 11211);
            }
            
            $this->key = md5($key);
            $this->cacheTime = $cacheTime;
        }
        
        public function check() {
            if (array_key_exists($this->key, self::$cache)) {
                return true;
            }
            
            $value = $this->memcached->get($this->key);
            
            if ($this->memcached->getResultCode() != Memcached::RES_NOTFOUND) {
                self::$cache[$this->key] = $value;
                return true;
            }
            
            return false;
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