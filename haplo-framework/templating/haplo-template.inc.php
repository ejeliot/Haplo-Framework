<?php
    /****************************************************************************************/
    /* HaploTemplate - templating class which uses PHP for its templating language rather   */
    /* than reinventing the wheel with custom syntax                                        */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
    
    /**
     * @author Ed Eliot
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
        public function __construct($filename) {
            global $config;
            
            if (!preg_match('/^[a-z0-9\/_-]+\.php$/i', $filename)) {
                throw new HaploInvalidTemplateException("Invalid template filename specified ($filename). Characters allowed in the filename are a-z, 0-9, _ and -. The filename must also end in .php");
            }
            
            $this->filename = $filename;
            $this->filePaths = explode(',', $config->get_key('paths', 'templates'));
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
                foreach ($vars as $key => $value) {
                    $template->set($key, $value);
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
            $options = array()
        ) {
            global $config;
            
            $defaultOptions = array(
                'stripHtml' => null,
                'convertEntities' => null,
                'charSet' => null,
            );
            $options = array_merge($defaultOptions, $options);
            
            if (is_null($options['stripHtml'])) {
                $optiona['stripHtml'] = $config->get_key('templates', 'stripHtml');
            }
            
            if (is_null($options['convertEntities'])) {
                $options['convertEntities'] = $config->get_key('templates', 'convertEntities');
            }
            
            if (is_null($options['charSet'])) {
                $options['charSet'] = $config->get_key('templates', 'encoding');
            }

            // is variable a scalar
            if (is_scalar($value)) {
                if ($options['stripHtml']) {
                    $value = strip_tags($value);
                }

                if ($options['convertEntities']) {
                    $value = htmlspecialchars($value, ENT_QUOTES, $options['charSet']);
                }
            }
            
            // is variable an array
            if (is_array($value)) {
                array_walk_recursive($value, function(&$value, $key, $options) {
                    if (!($value instanceof HaploTemplate) && is_scalar($value)) {
                        if ($options['stripHtml']) {
                            $value = strip_tags($value);
                        }

                        if ($options['convertEntities']) {
                            $value = htmlspecialchars($value, ENT_QUOTES, $options['charSet']);
                        }
                    }
                }, $options);
            }
            
            $this->vars[$name] = $value;
        }

        /**
         * Add a post filter - a function which is run against the generated template before outputting
         *
         * @param string $functionName Name of the function to run
         * @param string $filePath File path to look for post filter in
         * @return void
         * @author Ed Eliot
         **/
        public function add_post_filter($functionName, $filePath = null) {
            global $config;
            
            if (is_null($filePath)) {
                $filePath = $config->get_key('paths', 'postFilters');
            }
            
            $postfilter = $filePath.'/'.str_replace('_', '-', strtolower($functionName)).'.php';
            if (file_exists($postfilter)) {
                require_once($postfilter);
                
                $this->postFilters[] = $functionName;
            } else {
                throw new HaploPostFilterFunctionNotFoundException("Post filter ($functionName) could not be found in ($filePath)");
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
            foreach ($this->vars as $key => &$value) {
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
                    $$key = $value->render();
                } else {
                    $$key = $value;
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
                throw new HaploTemplateNotFoundException("Template ($this->filename) doesn't exist on any of the specified search paths.\n\nSearch paths:\n\n".implode("\n", $this->filePaths));
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
