<?php
    /**
     * HaploConfig - this class reads config files (in ini format) and provides helper
     * methods for accessing properties set in those files
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploConfig
     **/
    
    class HaploConfig extends HaploSingleton {
        protected $environment;

        /**
         * Stores merged config details from all config files
         *
         * @access protected
         * @var array
         **/
        protected $config = array();

        /**
         * Constructor for class
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function __construct() {
            $this->config['_files'] = array();

            if (is_dir(HAPLO_CONFIG_PATH)) {
                $this->get_environments();

                if (!empty($this->environment)) {
                    $files = $this->parse_files(HAPLO_CONFIG_PATH, $this->environment['files']);
                    $this->config['environment']['name'] = $this->environment['name'];
                } else {
                    $files = $this->get_files(HAPLO_CONFIG_PATH);
                    $this->parse_files(HAPLO_CONFIG_PATH, $files);
                }
                
                $this->config = $this->replace_vars($this->config);
            }
        }

        /**
         * Get details about available environments and return matching environment 
         * or default
         *
         * @return array
         * @author Ed Eliot
         **/
        protected function get_environments() {
            $environmentsFile = HAPLO_CONFIG_PATH.'/environments.ini';
            $server = gethostname();

            if (file_exists($environmentsFile)) {
                $environments = parse_ini_file($environmentsFile, true);

                if (!empty($environments)) {
                    if (!empty($environments[$server])) {
                        $this->environment = $environments[$server];
                    } else {
                        $this->environment = $environments['default'];
                    }
                } else {
                    throw new HaploConfigParseFileException("Couldn't parse environment file ($environmentsFile)");
                }
            }
        }

        /**
         * Read config directory and add filenames for all files with extension .ini to files array
         *
         * @param string $path Path to config directory
         * @return array
         * @author Ed Eliot
         **/
        protected function get_files($path) {
            $files = array();
            $dir = dir($path);

            while (false !== ($file = $dir->read())) {
                if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == 'ini') {
                    $files[] = $file;
                }
            }

            sort($files);

            return $files;
        }

        /**
         * Process .ini files and add to config array
         * Values in later files will override ones with the same name in earlier files
         *
         * @param string $path Path to config directory
         * @param array $files Files to process
         * @return void
         * @author Ed Eliot
         **/
        protected function parse_files($path, $files) {
            foreach ($files as $file) {
                $config = parse_ini_file("$path/$file", true);

                if (!empty($config)) {
                    $this->config = array_merge($this->config, $config);
                    $this->config['_files'][] = "$path/$file";
                } else {
                    throw new HaploConfigParseFileException("Couldn't parse configuration file ($path/$file)");
                }
            }
        }
        
        /**
         * Replace special variables within config values with real content 
         * <SITE_BASE> etc
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function replace_vars($config) {
            if (!empty($config)) {
                foreach ($config as &$value) {
                    if (is_array($value)) {
                        $value = $this->replace_vars($value);
                    } else {
                        $value = str_replace(array(
                            '<SITE_BASE>', 
                            '<HAPLO_FRAMEWORK_BASE>'
                        ), array(
                            SITE_BASE, 
                            HAPLO_FRAMEWORK_BASE
                        ), $value);
                    }
                }
            }
            
            return $config;
        }

        /**
         * Static helper method used to ensure only one instance of the class is instantiated
         * This overrides the base version in the abstract HaploSingleton class because
         * PHP < 5.3 doesn't support late static binding
         *
         * @return HaploConfig
         * @author Ed Eliot
         **/
        public static function get_instance() {
            $class = get_called_class();

            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class();
            }
            return self::$instances[$class];
        }

        /**
         * Get specified key from section and key
         *
         * @param string $section The section the key can be found in (corresponds to ini file section)
         * @param string $key The key to retrieve value for
         * @return string
         * @author Ed Eliot
         **/
        public function get_key($section, $key) {
            if (isset($this->config[$section][$key])) {
                return $this->config[$section][$key];
            } else {
                throw new HaploConfigKeyNotFoundException('Configuration key not found ('.$section.', '.$key.')');
            }
        }

        /**
         * Get value for a key or default if it doesn't exist, use this when you don't 
         * want to throw an exception if the key doesn't exist
         *
         * @return string
         * @author Ed Eliot
         **/
        public function get_key_or_default($section, $key, $default = '') {
        	$result = null;
            try {
                $result = $this->get_key($section, $key);
            } catch (HaploConfigKeyNotFoundException $e) {
                $result = $default;
            }
            return $result;
        }
        
        /**
         * Dynamically set/update a config key - this doesn't actually change the config files
         *
         * @return boolean
         * @author Ed Eliot
         **/
        public function set_key($section, $key, $value) {
            return ($this->config[$section][$key] = $value);
        }

        /**
         * Get specified section
         *
         * @param string $section The section to retrieve (corresponds to ini file section)
         * @return string
         * @author Ed Eliot
         **/
        public function get_section($section) {
            if (isset($this->config[$section])) {
                return $this->config[$section];
            } else {
                throw new HaploConfigSectionNotFoundException('Configuration section not found ('.$section.')');
            }
        }

        /**
         * Get all configuration options
         *
         * @return array
         * @author Ed Eliot
         **/
        public function get_all() {
            return $this->config;
        }
    }
?>