<?php
    /**
     * StaticPage - this is an example action class which finds and loads templates based
     * on the URL entered. This would allow you to create a simple "static" site.
     * For example: /news/sport/ would result in ../templates/news/sport.php being loaded
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package StaticPage
     **/
    class StaticPage extends HaploAction {
        /**
         * This method overrides the default get_instance() method from it's parent. It's only 
         * needed due to the lack of support for late static binding in PHP < 5.3
         *
         * @param array $options A key/value pair array of object references to pass to the class.
         * @param string $class Pass in the name of the class
         * @return StaticPage
         * @author Ed Eliot
         **/
        public static function get_instance($options = array(), $class = __CLASS__) {
            return parent::get_instance($options, $class);
        }
        
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
                    $page = new HaploTemplate($template.'.php');
                    $page->set('translations', $this->translations);
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