<?php
    /**
     * StaticPage - this is an example action class which finds and loads templates based
     * on the URL entered. This would allow you to create a simple "static" site.
     * For example: /news/sport/ would result in ../templates/news/sport.php being loaded
     * Prefix include templates with an underscore (_) to prevent them being surfaced as 
     * actual pages
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package StaticPage
     **/
    class Contact extends HaploAction {
        protected function do_init() {
            $this->nonce->create();
        }
        
        protected function do_post() {
            if (!$this->nonce->check()) {
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
            $page->set('metaTitle', $this->config->get_key_or_default('contact', 'metaTitle'));
            $page->set('metaDesc', $this->config->get_key_or_default('contact', 'metaDesc'));
            $page->set('metaKeywords', $this->config->get_key_or_default('contact', 'metaKeywords'));
            $page->set('translations', $this->translations);
            $page->set('content', new HaploTemplate('contact.php'));
            // add a post filter to output nonce into all forms in page
            $page->add_post_filter('nonce');
            $page->display();
        }
    }
?>