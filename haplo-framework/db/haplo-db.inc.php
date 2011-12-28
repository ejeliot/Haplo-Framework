<?php
    /****************************************************************************************/
    /* HaploDb - a simple wrapper to PDO                                                    */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
    
    class HaploDb extends HaploSingleton {
        protected $db;
        
        protected function __construct($params) {
            $this->connect($params);
        }
        
        protected function connect($params) {
            $dsn = sprintf(
                "mysql:dbname=%s;host=%s", 
                $params['database'], 
                $params['host']
            );
            
            try {
                $this->db = new PDO(
                    $dsn, 
                    $params['user'], 
                    $params['pass'],
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    )
                );
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $this->log_error($e);
                
                return false;
            }
            
            return true;
        }
        
        protected function log_error($e) {
            error_log(sprintf(
                'Error connecting to database (%s - %d) on line %d in %s', 
                $e->getMessage(), 
                $e->getCode()),
                $e->getLine(),
                $e->getFile()
            );
        }
        
        public static function get_instance($params = array()) {
            $defaultParams = array(
                'user' => 'root',
                'pass' => '',
                'database' => '',
                'host' => '127.0.0.1'
            );
            $params = array_merge($defaultParams, $params);
            $class = get_called_class();
            $instanceKey = sha1($class.serialize($params));
            
            if (!isset(self::$instances[$instanceKey])) {
                self::$instances[$instanceKey] = new $class($params);
            }
            return self::$instances[$instanceKey];
        }
        
        // make all PDO functions available directly to class
        public function __call($name, $params) {
            return call_user_func_array(array($this->db, $name), $params);
        }
        
        public function __clone() {
            throw Exception('Cloning is not allowed.');
        }
        
        public function get_array($stmt, $params = array(), $start = 0, $count = 0) {
            try {
                if ($count > 0) {
                    $stmt = preg_replace(
                        '/^select\s+/i', 
                        'select sql_calc_found_rows ', 
                        sprintf('%s limit %d, %d', trim($stmt), $start, $count)
                    );
                }
            
                if (empty($params) && $result = $this->db->query($stmt)) {
                    return $result->fetchAll(PDO::FETCH_ASSOC);
                } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                $this->log_error($e);
            }
            
            return false;
        }
        
        public function get_row($stmt, $params = array()) {
            try {
                if (empty($params) && $result = $this->db->query($stmt)) {
                    return $result->fetch(PDO::FETCH_ASSOC);
                } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                $this->log_error($e);
            }
            
            return false;
        }
        
        public function get_column($stmt, $params = array(), $column = 0) {
            try {
                if (empty($params) && $result = $this->db->query($stmt)) {
                    return $result->fetchColumn($column);
                } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                    return $stmt->fetchColumn($column);
                }
            } catch (PDOException $e) {
                $this->log_error($e);
            }
            
            return false;
        }
        
        public function get_recordset($stmt, $params = array(), $start = 0, $count = 0) {
            try {
                if ($count > 0) {
                    $stmt = sprintf('%s limit %d, %d', $stmt, $start, $count);
                }
            
                if (empty($params) && $result = $this->db->query($stmt)) {
                    return $result;
                } elseif (($stmt = $this->db->prepare($stmt)) && $stmt->execute($params)) {
                    return $stmt;
                }
            } catch (PDOException $e) {
                $this->log_error($e);
            }
            
            return false;
        }
        
        public function run($stmt, $params = array()) {
            try {
                if (empty($params)) {
                    return $this->db->query($stmt);
                } elseif ($stmt = $this->db->prepare($stmt)) {
                    return $stmt->execute($params);
                }
            } catch (PDOException $e) {
                $this->log_error($e);
            }
            
            return false;
        }
        
        public function get_total_rows() {
            try {
                if ($result = $this->db->query('select found_rows()')) {
                    return $result->fetchColumn(0);
                }
            } catch (PDOException $e) {
                $this->log_error($e);
            }
            
            return false;
        }
        
        public function get_offsets_from_page($page, $numPerPage = 50) {
            $start = ($page - 1) * $numPerPage;
            
            return array($start, $numPerPage);
        }
        
        public function get_paging($page, $numPerPage = 50) {
            $numRows = $this->get_total_rows();
            $numPages = ceil($numRows / $numPerPage);
            
            if ($page < 1 || $page > $numPages) {
                return false;
            }
            
            $pages = array();
            
            $pages['total'] = $numRows;
            
            if ($page > 1) {
                $pages['previous'] = true;
            }
            
            if (($page - 4) > 1) {
                $pages['previous-n'] = true;
            }
            
            if ($page <= 5) {
                $pages['start'] = 1;
            } elseif ($numPages - $page < 4) {
                $pages['start'] = $page - (8 - ($numPages - $page));
                
                if ($pages['start'] < 1) {
                    $pages['start'] = 1;
                }
            } else {
                $pages['start'] = $page - 4;
            }
            
            if ($page >= $numPages - 4) {
                $pages['end'] = $numPages;
            } elseif ($page <= 4) {
                $pages['end'] = $page + (9 - $page);
                
                if ($pages['end'] > $numPages) {
                    $pages['end'] = $numPages;
                }
            } else {
                $pages['end'] = $page + 4;
            }
            
            if (($page + 4) < $numPages) {
                $pages['next-n'] = true;
            }
            
            if ($page < $numPages) {
                $pages['next'] = true;
            }
            
            $pages['current'] = $page;
            $pages['num-pages'] = $numPages;
            
            $pages['first-result'] = ($pages['current'] - 1) * $numPerPage + 1;
            $pages['last-result'] = ($pages['current'] - 1) * $numPerPage + $numPerPage;
            
            if ($pages['last-result'] > $pages['total']) {
                $pages['last-result'] = $pages['total'];
            }
            
            return $pages;
        }
    }
?>