<?php
    /**
     * HaploConfig - this class reads config files (in ini format) and provides helper 
     * methods for accessing properties set in those files
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploConfig
     **/
    class HaploConfig extends HaploSingleton {
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
            $this->config['files'] = array();
            
            if (is_dir(HAPLO_CONFIG_PATH)) {
                $files = $this->get_files(HAPLO_CONFIG_PATH);
                $this->parse_files(HAPLO_CONFIG_PATH, $files);
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
                    throw new HaploException("Couldn't parse configuration file ($path/$file)");
                }
            }
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
            $class = __CLASS__;
            
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
                throw new HaploException('Configuration key not found ('.$section.', '.$key.')');
            }
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
                throw new HaploException('Configuration section not found ('.$section.')');
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