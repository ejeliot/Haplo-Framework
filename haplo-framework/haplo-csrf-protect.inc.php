<?php
    /**
     * HaploCsrfProtect - heavily based on a class written by Simon Willison
     * See here for the original - http://simonwillison.net/static/2008/csrf_protect.php.txt
     *
     * @package HaploCsrfProtect
     **/

    class HaploCsrfProtect {
        protected $csrfFormField = '_csrf_protect_token';
        
        public function __construct() {
            $this->enable();
        }
    
        protected function generate_token() {
            return sha1(session_id() . ':csrf');
        }
    
        protected function should_protect() {
            // Should CSRF protection apply to this request?
            # Only protect if the user has a session
            $sessionId = session_id();
            if (empty($sessionId)) {
                return false;
            }
            # If the request came in via Ajax, don't run CSRF protection
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && trim(
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])
                ) == 'xmlhttprequest') {
                return false;
            }
            return true;
        }
    
        protected function enable() {
            if (!$this->should_protect()) {
                return;
            }
            // CSRF token is derived from session ID:
            $csrfToken = $this->generate_token();
            // If it's a POST, check the token matches
            if (!empty($_POST)) {
                if (empty($_POST[$this->csrfFormField])) {
                    throw new HaploException("No $this->csrfFormField was found in POST request.");
                }
                $formToken = $_POST[$this->csrfFormField];
                if ($formToken != $csrfToken) {
                    throw new HaploException("The form token ($formToken) did not match the corresponding CSRF token ($csrfToken).");
                }
            }
            // On POST or GET, we still need to add the token to any forms
            ob_start(array($this, 'ob_callback'));
        }
    
        protected function should_rewrite_forms($html) {
            // We only rewrite pages served with an HTML content type
            $sentHeaders = headers_list();
            foreach ($sentHeaders as $header) {
                // Search for a content-type header that is NOT HTML
                // Note the Content-Type header will not be included in 
                // headers_list() unless it has been explicitly set from PHP.
                if (preg_match('/^Content-Type:/i', $header) && 
                    strpos($header, 'text/html') === false) {
                    return false;
                }
            }
            return true;
        }
    
        protected function is_xhtml($html) {
            // Check for XHTML doctype
            return strpos(
                $html, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML'
            ) !== false;
        }
    
        protected function ob_callback($html) {
            // Rewrites any forms on the page to include the CSRF token
            if (!$this->should_rewrite_forms($html)) {
                return $html;
            }
        
            $token = $this->generate_token();
        
            $hidden = '<div style="display: none;">';
            $hidden .= '<input type="hidden" name="';
            $hidden .= $this->csrfFormField . '" value="' . $token;
            $hidden .= '"' . ($this->is_xhtml($html) ? ' />' : '>');
            $hidden .= '</div>';
        
            // Find only POST forms
            return preg_replace(
                '/(<form\W[^>]*\bmethod=(\'|"|)POST(\'|"|)\b[^>]*>)/i',
                '\\1'.$hidden,
                $html
            );
        }
    }
?>