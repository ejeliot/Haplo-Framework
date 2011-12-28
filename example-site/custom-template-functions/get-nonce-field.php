<?php
    function get_nonce_field($name = 'nonce', $trailingSlash = false) {
        global $nonce;
        
        return sprintf('<input type="hidden" name="nonce" value="%s"%s>', $nonce->get($name), ($trailingSlash ? ' /' : ''));
    }
?>