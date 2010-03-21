<?php
    /**
     * An example controller file - you can add as many of these as you
     * need. It instantiates the routing code with a list of URL pattern
     * matches.                                                         
     *                                                                  
     * URLs can take one of two formats:                                
     * /index.php/news/                                                 
     * or                                                               
     * /news/                                                           
     * if you want to use the second form you'll need                   
     * mod_rewrite enabled and support for .htaccess files.             
     * To improve performance you can alternatively add the rules       
     * found in www/.htaccess to your Apache virtual host configuration.
     *
     * @author Ed Eliot
     * @copyright Brightfish Software Limited, 2008-2010. See license.txt for more details.
     **/
    
    /**
     * include framework libraries and initial config
     * if you want to modify some of the settings specified in this file 
     * or include only parts of the Haplo Framework then you're probably 
     * best to create your own copy and link to that instead
     **/
    require('../includes/haplo-init.inc.php');
    // require('../includes/haplo-init-custom.inc.php');
    
    // set up URL mappings
    $urls = array(
        '/.*' => 'static-page',
    );
    
    HaploSetup::validate();
    
    // create an instance of the router
    $router = new HaploRouter($urls);
    
    /**
     * filter input variables - GET, POST 
     * and REQUEST and apply other security settings
     * this line is optional but I strongly recommend adding it 
     * as it offers some basic protection against use of unfiltered 
     * content
     **/
    $securityFilter = new HaploInputProtect();
    
    // Add protection against cross site request forgeries (CSRF) in forms
    $csrfProtect = new HaploCsrfProtect();
    
    /**
     * set up support for translations
     * the language you pass should probably be a two letter ISO code
     **/
    $translations = new HaploTranslations('en');
    
    // load selected action
    if ($action = $router->get_action()) {
        require($action);
    }
?>