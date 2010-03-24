<?php
    /**
     * HaploTranslations - Provides translation string support
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploTranslations
     **/
    class HaploTranslations {
        /**
         * Stores the selected language
         *
         * @var string
         **/
        protected $lang;
        /**
         * Stores the path to the translation files
         *
         * @var string
         **/
        protected $translationsDir;
        /**
         * Stores the path to the data representing processed translation files
         *
         * @var string
         **/
        protected $translationsCacheDir;
        /**
         * Stores whether or not a user can show translation keys by passing ?showKeys=true
         *
         * @var string
         **/
        protected $translationsAllowShowKeys;
        /**
         * Stores the path to the selected translation file
         *
         * @var string
         **/
        protected $file;
        /**
         * Stores the path to the cache file for the selected translation
         *
         * @var string
         **/
        protected $cacheFile;
        /**
         * A key/value pair containing translations found
         *
         * @var array
         **/
        protected $translations = array();
        
        /**
         * Constructor for class
         *
         * @param string $lang Language to use
         * @param string $translationsDir Path to translation files
         * @param string $translationsCacheDir Path to translation cache files
         * @param boolean $translationsAllowShowKeys Whether or not to show translation keys when ?showKeys=true present
         * @return void
         * @author Ed Eliot
         **/
        public function __construct(
            $lang = HAPLO_TRANSLATIONS_DEFAULT_LANG, 
            $translationsDir = HAPLO_TRANSLATIONS_PATH,
            $translationsCacheDir = HAPLO_CACHE_PATH,
            $translationsAllowShowKeys = HAPLO_TRANSLATIONS_ALLOW_SHOW_KEYS
        ) {
            // set up file paths
            $this->lang = strtolower($lang);
            $this->translationsDir = $translationsDir;
            $this->translationsCacheDir = $translationsCacheDir;
            $this->bTranslationAllowShowKeys = $translationsAllowShowKeys;
            $this->file = $this->translationsDir.'/'.$lang.'.txt';
            $this->cacheFile = $this->translationsCacheDir.'/HAPLO-TRANSLATIONS-'.md5($lang).'.cache';
            
            if (!file_exists($this->file)) {
                throw new HaploException("Specified language file ($this->lang) does not exist.");
            }
            
            // after first pass translations are stored in serialised PHP array for speed
            // does a cache exist for the selected language
            if (file_exists($this->cacheFile)) {
                // grab current cache
                $cache = unserialize(file_get_contents($this->cacheFile));
                
                // if the recorded timestamp varies from the selected language file then the translations have changed
                // update the cache
                if (
                    $cache['timestamp'] == filemtime($this->file) && 
                    (!isset($cache['parent-filename']) || 
                    $cache['parent-timestamp'] == filemtime($cache['parent-filename']))
                ) { // not changed
                    $this->translations = $cache['translations'];
                } else { // changed
                    $this->Process();
                }
            } else {
                $this->Process();
            }
        }
        
        /**
         * Processes selected translation file
         *
         * @return void
         * @author Ed Eliot
         **/
        protected function process() {
            // create array for serialising
            $cache = array();
            
            // read translation file into array
            $file = file($this->file);
            
            // does this translation file inherit from another
            $inheritsMatches = array();
            if (isset($file[0]) && preg_match("/^\s*{inherits\s+([^}]+)}.*$/", $file[0], $inheritsMatches)) {
                $parentFile = $this->translationsDir.trim($inheritsMatches[1]).'.txt';
                // read parent file into array
                $parentFile = file($parentFile);
                // merge lines from parent file into main file array, lines in the main file override lines in the parent
                $file = array_merge($parentFile, $file);
                // store filename of parent
                $cache['parent-filename'] = $parentFile;
                // store timestamp of parent
                $cache['parent-timestamp'] = filemtime($parentFile);
            }
            
            // read language array line by line
            foreach ($file as $line) {
                $translationMatches = array();
                
                // match valid translations, strip comments - both on their own lines and at the end of a translation
                // literal hashes (#) should be escaped with a backslash
                if (preg_match("/^\s*([0-9a-z\._-]+)\s*=\s*((\\\\#|[^#])*).*$/iu", $line, $translationMatches)) {
                    $this->translations[$translationMatches[1]] = trim(str_replace('\#', '#', $translationMatches[2]));
                }
            }
            // add current timestamp of translation file
            $cache['timestamp'] = filemtime($this->file);
            // add translations
            $cache['translations'] = $this->translations;
            // write cache
            file_put_contents($this->cacheFile, serialize($cache));
        }
        
        /**
         * Get translation for specified key
         *
         * @param string $key Key to look up translation for
         * @return string
         * @author Ed Eliot
         **/
        public function get($key) {
            $translation = '';
            
            if (array_key_exists($key, $this->translations)) { // key / value pair exists
                $translation = $this->translations[$key];
                
                // number of arguments can be variable as user can pass any number of substitution values
                $numArgs = func_num_args();
                if ($numArgs > 1) { // complex translation, substitution values to process
                    $firstArg = func_get_arg(1);
                    if (is_array($firstArg)) { // named substitution variables
                        foreach ($firstArg as $key => $value) {
                            $translation = str_replace('{'.$key.'}', $value, $translation);
                        }
                    } else { // numbered substitution variables
                        for ($i = 1; $i < $numArgs; $i++) {
                            $param = func_get_arg($i);
                            // replace current substitution marker with value
                            $translation = str_replace('{'.($i - 1).'}', $param, $translation);
                        } 
                    } 
                }
            
                // whilst translating the user has the option to switch out all values with the corresponding key
                // this helps to see what translated text will appear where
                // set ALLOW_SHOW_KEYS false to disable - might be preferable in production
                if ($this->translationsAllowShowKeys && (isset($_GET['showKeys']) || isset($_POST['showKeys']))) {
                    return $key;
                } else {
                    return $translation;
                }
            }
            // key / value doesn't exist, throw exception
            throw new HaploException("Translation key ($key) does not exist in selected language file($this->file).");
        }
        
        /**
         * Get plural versions of a translation depending on qty, for example: 5 apples
         * (not implemented yet)
         *
         * @param string $key Key to look up translation for
         * @param integer number of items
         * @return void
         * @author Ed Eliot
         **/
        public function get_plural($key, $qty) {
            // TODO: Implement get_plural functionality.
            throw new HaploException("Method (get_plural) not implemented yet.");
        }
    }
?>