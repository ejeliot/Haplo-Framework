<?php
    function nonce($content) {
        global $nonce;
        
        return str_replace('</form>', '<input type="hidden" name="nonce" value="'.$nonce->get().'"></form>', $content);
    }
?>