<?php
    /**
     * example function
     * this retrieves a config item
     * calling in a template:
     * <?php echo $this->get_key('paths', 'templates'); ?>
     **/
    function get_key($section, $key) {
        global $config;
        
        return $config->get_key($section, $key);
    }
?>