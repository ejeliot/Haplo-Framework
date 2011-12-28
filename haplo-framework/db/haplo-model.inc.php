<?php
    /****************************************************************************************/
    /* HaploModel                                                                           */
    /*                                                                                      */
    /* Software License Agreement (BSD License)                                             */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
    
    abstract class HaploModel {
        protected $db;
        
        public function __contruct($dbConfig = 'db') {
            global $config;
            
            $this->db = HaploDb::get_instance(array(
                'user' => $config->get_key($dbConfig, 'user'),
                'pass' => $config->get_key($dbConfig, 'pass'),
                'database' => $config->get_key($dbConfig, 'database'),
                'host' => $config->get_key($dbConfig, 'host')
            ));
        }
    }
?>