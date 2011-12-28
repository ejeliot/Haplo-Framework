<?php
    /**
     * HaploHttp - wrappers to curl
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploHttp
     **/
    
    class HaploHttp {
        static public function get($url, $connectTimeout = null, $requestTimeout = null) {
            global $config;
            
            if (is_null($connectTimeout)) {
                $connectTimeout = $config->get_key('http', 'connectTimeout');
            }
            
            if (is_null($requestTimeout)) {
                $requestTimeout = $this->get_key('http', 'requestTimeout');
            }
            
            // initiate session
            $curl = curl_init($url);
            // set options
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
            curl_setopt($curl, CURLOPT_TIMEOUT, $requestTimeout);
            curl_setopt($curl, CURLOPT_USERAGENT, $config->get_key('http', 'userAgent'));
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // request URL
            $result = curl_exec($curl);
            // close session
            curl_close($curl);
            
            return $result;
        }
        
        static public function post($url, $params, $connectTimeout = null, $requestTimeout = null) {
            // TODO: Implement http post functionality.
            throw new HaploMethodNotImplementedException('Method (post) not implemented yet.');
        }
        
        static public function head($url) {
            // TODO: Implement http head functionality.
            throw new HaploMethodNotImplementedException('Method (head) not implemented yet.');
        }
    }
?>