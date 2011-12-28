<?php
    /**
     * StaticPage - this action class finds and loads templates based
     * on the URL entered. This would allow you to create a simple "static" site.
     * For example: /news/sport/ would result in ../templates/news/sport.php being loaded
     * Prefix include templates with an underscore (_) to prevent them being surfaced as 
     * actual pages
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package StaticPage
     **/
    
    class StaticPage extends HaploAction {
        protected $section;
        
        /**
         * This method is called when all types of request are received
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function do_all() {
            if ($template = $this->router->get_request_var('template', 'home')) {
                $template = trim($template, '/');
                $this->section = $template;
                
                try {
                    $page = new HaploTemplate($this->config->get_key_or_default($this->section, 'layout', '_layout.php'));
                    $page->set('section', $this->section);
                    $page->set('title', $this->config->get_key_or_default($this->section, 'metaTitle'));
                    $page->set('metaDesc', $this->config->get_key_or_default($this->section, 'metaDesc'));
                    $page->set('metaKeywords', $this->config->get_key_or_default($this->section, 'metaKeywords'));
                    $page->set('translations', $this->translations);
                    $page->set('content', new HaploTemplate($template.'.php'));
                    $page->display();
                } catch (Exception $e) {
                    $this->do_404();
                }
            } else {
                throw new HaploTemplateNotFoundException('Static page template not specified in '.$this->router->get_action().'.');
            }
        }
    }
?>