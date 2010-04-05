<?php
    /**
     * HaploTemplate - templating class which uses PHP
     * for its templating language rather than reinventing the wheel 
     * with custom syntax
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     * @package HaploTemplate
     **/
    class HaploTemplate {
        /**
         * Stores filename of template to render
         *
         * @var string
         **/
        protected $filename;
        /**
         * Stores file paths to look for template in
         *
         * @var array
         **/
        protected $filePaths;
        /**
         * Stores variables to pass to template
         *
         * @var string
         **/
        protected $vars = array();
        /**
         * Stores reference to post filter functions to run against template
         *
         * @var array
         **/
        protected $postFilters = array();

        /**
         * Constructor for class
         *
         * @param string $filename Filename of template to render
         * @param string $filePaths Comma separated list of paths to look for template in
         * @return void
         * @author Ed Eliot
         **/
        public function __construct($filename, $filePaths = HAPLO_TEMPLATE_PATHS) {
            if (!preg_match('/[a-z0-9\/_-]+\.php/i', $filename)) {
                throw new Exception("Invalid template filename specified ($filename). Characters allowed in the filename are a-z, 0-9, _ and -. The filename must also end in .php");
            }
            
            $this->filename = $filename;
            $this->filePaths = explode(',', $filePaths);
        }
        
        /**
         * Magic method to support calling custom functions within templates
         *
         * @param string $name Name of the function to call
         * @param array $args Arguments to pass to the function when called
         * @return string
         * @author Ed Eliot
         **/
        public function __call($name, $args) {
            $file = HAPLO_TEMPLATE_CUSTOM_FUNCTION_PATH.'/'.str_replace('-', '_', strtolower($name)).'.php';
            
            if (file_exists($file)) {
                require_once($file);
                
                return call_user_func_array($name, $args);
            } else {
                throw new HaploException("Custom template function ($name) not found in ($file).");
            }
        }

        /**
         * Include another template inside the main template (called within the template file).
         * Included template inherits parent templates variables and can optionally set its own
         * which live within the scope of that included template only.
         *
         * @param string $filename Filename for template to include - uses the same file paths as the parent
         * @param array $vars Optionally pass additional variables to the template
         * @return void
         * @author Ed Eliot
         **/
        protected function inc_template($filename, $vars = array()) {
            $template = new HaploTemplate($filename, $this->filePaths);
            $template->vars = $this->vars;
            
            if (count($vars)) {
                foreach ($vars as $sKey => $value) {
                    $template->set($sKey, $value);
                }
            }

            echo $template->render();
        }

        /**
         * Set a variable (make it available within the scope of the template)
         *
         * @param string $name Name of variable to set
         * @param variant $value Value to give to variable
         * @param boolean $stripHtml Whether or not to strip HTML in variable when output in template
         * @param boolean $convertEntities Whether or not to convert entities in variable when output in template
         * @param string $charSet The character set to use when converting entities
         * @return void
         * @author Ed Eliot
         **/
        public function set(
            $name, 
            $value, 
            $stripHtml = HAPLO_TEMPLATE_STRIP_HTML, 
            $convertEntities = HAPLO_TEMPLATE_CONVERT_ENTITIES, 
            $charSet = HAPLO_TEMPLATE_ENCODING
        ) {
            $this->vars[$name] = $value;

            // variable value might be a reference to a sub-template
            if (!($value instanceof HaploTemplate) && is_scalar($value)) {
                if ($stripHtml) {
                    $this->vars[$name] = strip_tags($this->vars[$name]);
                }

                if ($convertEntities) {
                    $this->vars[$name] = htmlspecialchars($this->vars[$name], ENT_QUOTES, $charSet);
                }
            }
        }

        /**
         * Add a post filter - a function which is run against the generated template before outputting
         *
         * @param string $functionName Name of the function to run
         * @param string $filePath File path to look for post filter in
         * @return void
         * @author Ed Eliot
         **/
        public function add_post_filter($functionName, $filePath = HAPLO_TEMPLATE_POSTFILTERS_PATH) {
            $postfilter = $filePath.'/'.str_replace('_', '-', strtolower($functionName)).'.php';
            if (file_exists($postfilter)) {
                require_once($postfilter);
                
                $this->postFilters[] = $functionName;
            } else {
                throw new HaploException("Post filter ($functionName) could not be found in ($filePath)");
            }
        }

        /**
         * Render template
         *
         * @return string
         * @author Ed Eliot
         **/
        public function render() {
            $output = '';

            // looping rather than using extract because we need to determine the value type before assigning
            foreach ($this->vars as $sKey => &$value) {
                // is this variable a reference to a sub-template
                if ($value instanceof HaploTemplate) {
                    // pass variables from parent to sub-template but don't override variables in sub-template 
                    // if they already exist as they are more specific
                    foreach ($this->vars as $subKey => $subValue) {
                        if (!($subValue instanceof HaploTemplate) && !array_key_exists($subKey, $value->vars)) {
                            $value->vars[$subKey] = $subValue;
                        }
                    }
                    // display sub-template and assign output to parent variable
                    $$sKey = $value->render();
                } else {
                    $$sKey = $value;
                }
            }

            // use output buffers to capture data from require statement and store in variable
            ob_start();
            
            $templateFound = false;
            
            foreach ($this->filePaths as $filePath) {
                if (file_exists($filePath.'/'.$this->filename)) {
                    require($filePath.'/'.$this->filename);
                    $templateFound = true;
                    break;
                }
            }
            
            if (!$templateFound) {
                throw new HaploException("Template ($this->filename) doesn't exist on any of the specified search paths.\n\nSearch paths:\n\n".implode("\n", $this->filePaths));
            }
            
            $output .= ob_get_clean();

            // process content against defined post filters
            foreach ($this->postFilters as $postFilter) {
                $output = $postFilter($output);
            }
            return $output;
        }

        /**
         * Output rendered template
         *
         * @return void
         * @author Ed Eliot
         **/
        public function display() {
            echo $this->render();
        }
    }
?>
