<?php
    class HaploNonce {
        protected $secret;
        protected $name;
        
        public function __construct($secret, $name = 'nonce') {
            $this->secret = $secret;
            $this->name = $name;
            
            $this->create();
        }
        
        public function check() {
            $result = (
                !empty($_SESSION[$this->name]) && 
                !empty($_REQUEST[$this->name]) && 
                $_SESSION[$this->name] == $_REQUEST[$this->name]
            );
            $this->create(true);
            
            return $result;
        }
        
        public function get() {
            return $_SESSION[$this->name];
        }
        
        protected function create($force = false) {
            if (empty($_SESSION[$this->name]) || $force) {
                $_SESSION[$this->name] = sha1($this->secret.uniqid());
            
                return $_SESSION[$this->name];
            }
            
            return false;
        }
    }
?>