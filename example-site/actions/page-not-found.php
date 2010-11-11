<?php
    /**
     * PageNotFound - Default 404 action class
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package PageNotFound
     **/ 
    class PageNotFound extends HaploAction { 
        /**
         * This method is called when all types of request are received
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function do_all() {
            $page = new HaploTemplate('_layout.php');
            
            // get meta information from config
            $page->set('metaTitle', $this->config->get_key_or_default('page-not-found', 'metaTitle'));
            $page->set('metaDesc', $this->config->get_key_or_default('page-not-found', 'metaDesc'));
            $page->set('metaKeywords', $this->config->get_key_or_default('page-not-found', 'metaKeywords'));
            $page->set('translations', $this->translations);
            $page->set('content', new HaploTemplate('page-not-found.php'));
            $page->display();
        }
    }
?>