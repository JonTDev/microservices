<?php
// The Database Object  It is called in AP.
include_once('dbo.php');


return

    /**
     * Calls the service first, The service returned as a function.
     * Check for arguments.
     * Then call service function with or without the arguments.
     *
     * The results are sent through a callback function to AP to be output.
     *
     * @param string $path
     * @param callable $callback
     * @param array|NULL $argv
     */
    function($path, $callback, $argv = null) {

    $service = include($path);

    if(!$argv)
        $results = $service();
    else
        $results = call_user_func_array($service, $argv);

    $callback($results);
};