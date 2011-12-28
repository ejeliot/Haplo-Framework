<?php
    /****************************************************************************************/
    /* HaploNonce - generate and check tokens which can be used to prevent CSRF attacks     */
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
     * @package HaploNonce
     **/ 
    class HaploNonce {
        /**
         * Stores secret used to generate token
         *
         * @var string
         **/
        protected $secret;
        
        /**
         * Stores name of token
         *
         * @var string
         **/
        protected $name;
        
        /**
         * Class constructor - sets up variables and creates token
         *
         * @param string $secret Secret used to generate token
         * @param string $name Name of token
         * @return void
         * @author Ed Eliot
         **/
        public function __construct($secret, $name = 'nonce') {
            $this->secret = $secret;
            $this->name = $name;
            
            $this->create();
        }
        
        /**
         * Check token passed in request with last generated token and 
         * then create a new token for subsequent requests
         *
         * @return boolean
         * @author Ed Eliot
         **/
        public function check() {
            $result = (
                !empty($_SESSION[$this->name]) && 
                !empty($_REQUEST[$this->name]) && 
                $_SESSION[$this->name] == $_REQUEST[$this->name]
            );
            $this->create(true);
            
            return $result;
        }
        
        /**
         * Get the token
         *
         * @return string
         * @author Ed Eliot
         **/
        public function get() {
            return $_SESSION[$this->name];
        }
        
        /**
         * Create a new token
         *
         * @param boolean $force Force creation of a new token even if one already exists
         * @return boolean
         * @author Ed Eliot
         **/
        protected function create($force = false) {
            if (empty($_SESSION[$this->name]) || $force) {
                $_SESSION[$this->name] = sha1($this->secret.uniqid());
            
                return $_SESSION[$this->name];
            }
            
            return false;
        }
    }
?>