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
         * This method overrides the default get_instance() method from it's parent. It's only 
         * needed due to the lack of support for late static binding in PHP < 5.3
         *
         * @param array $options A key/value pair array of object references to pass to the class.
         * @param string $class Pass in the name of the class
         * @return PageNotFound
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
            $page = new HaploTemplate('page-not-found.php');
            $page->set('translations', $this->translations);
            $page->display();
        }
    }
?>