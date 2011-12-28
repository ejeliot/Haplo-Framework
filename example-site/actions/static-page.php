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
    class StaticPage extends HaploAction {
        /**
         * This method is called when all types of request are received
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function do_all() {
            if ($template = $this->router->get_request_var('template', 'welcome')) {
                $template = trim($template, '/');
                try {
                    $page = new HaploTemplate('_layout.php');
                    
                    // get meta information from config
                    $page->set('metaTitle', $this->config->get_key_or_default($template, 'metaTitle'));
                    $page->set('metaDesc', $this->config->get_key_or_default($template, 'metaDesc'));
                    $page->set('metaKeywords', $this->config->get_key_or_default($template, 'metaKeywords'));
                    $page->set('translations', $this->translations);
                    $page->set('content', new HaploTemplate($template.'.php'));
                    $page->display();
                } catch (HaploException $e) {
                    $this->do_404();
                }
            } else {
                throw new HaploException('Static page template not specified in '.$this->router->get_action().'.');
            }
        }
    }
?>