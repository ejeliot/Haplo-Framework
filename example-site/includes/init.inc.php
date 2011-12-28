<?php
    // path to haplo framework directory
    define('HAPLO_FRAMEWORK_PATH', '../../haplo-framework');
    
    // path to 500 error page
    define('HAPLO_SERVER_ERROR_PAGE', '../templates/server-error.php');
    
    // path to application config directory
    define('APP_CONFIG_PATH', '../config');
    
    // by default just include the default Haplo Framework init file
    require(HAPLO_FRAMEWORK_PATH.'/haplo-init.inc.php');
    
    // add any custom includes etc
?>