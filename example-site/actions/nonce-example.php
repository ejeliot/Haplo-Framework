<?php
    /**
     * NonceExample - an example of using nonces to protect against CSRF whilst offering 
     * functionality to delete products from a list
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package NonceExample
     **/
    class NonceExample extends HaploAction {
        protected $message;
        protected $nonce;
        
        protected function do_init() {
            // simple library to help with implementing nonces to prevent CSRF attacks 
            $this->nonce = new HaploNonce('12345'); // change secret in real world code
        }
        
        protected function do_post() {
            if ($this->nonce->check()) {
                $this->message = sprintf('Product %d deleted successfully.', $_POST['id']);
            } else {
                throw new HaploException("Nonce values don't match", HAPLO_NONCE_MISMATCH);
            }
        }
        /**
         * This method is called when all types of request are received
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function do_all() {
            $page = new HaploTemplate('_layout.php');
            
            // get meta information from config
            $page->set('metaTitle', $this->config->get_key_or_default('nonce-example', 'metaTitle'));
            $page->set('metaDesc', $this->config->get_key_or_default('nonce-example', 'metaDesc'));
            $page->set('metaKeywords', $this->config->get_key_or_default('nonce-example', 'metaKeywords'));
            $page->set('translations', $this->translations);
            $page->set('message', $this->message);
            $page->set('nonce', $this->nonce->get());
            $page->set('content', new HaploTemplate('nonce-example.php'));
            // add a post filter to output nonce into all forms in page
            $page->display();
        }
    }
?>