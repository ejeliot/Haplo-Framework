<?php
    /****************************************************************************************/
    /* PageNotFound                                                                         */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
    
    /**
     * PageNotFound - Default 404 action class
     *
     * @package PageNotFound
     **/ 
    class PageNotFound extends HaploAction { 
        protected $section = 'page-not-found';
        
        /**
         * This method is called when all types of request are received
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function do_all() {
            $page = new HaploTemplate($this->config->get_key_or_default($this->section, 'layout', '_layout.php'));
            $page->set('section', $this->section);
            $page->set('title', $this->config->get_key_or_default($this->section, 'title'));
            $page->set('metaDesc', $this->config->get_key_or_default($this->section, 'metaDesc'));
            $page->set('metaKeywords', $this->config->get_key_or_default($this->section, 'metaKeywords'));
            $page->set('translations', $this->translations);
            $page->set('content', new HaploTemplate('page-not-found.php'));
            $page->display();
        }
    }
?>