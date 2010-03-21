<?php
    if ($template = $router->get_request_var('template', 'welcome')) {
        $page = new HaploTemplate($template.'.php');
        $page->set('translations', $translations);
        $page->display();
    } else {
        throw new HaploException('Static page template not specified in '.$router->get_action().'.');
    }
?>