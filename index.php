<?php
// Calling the defines
include_once(__DIR__ . DIRECTORY_SEPARATOR . 'defines.php');

// Calling the class to make it all happen.
include_once(AP_HOME . 'ap.php');

// Accessing from web server
if(!$argv)
{
    /**
     * This basically does the same thing as the CLI
     * The difference here is it is in a function format
     * So that the arguments can be accessed similarly.
     */
    function runAccessPoint()
    {
        return call_user_func_array(array('AP', 'run'), func_get_args());
    }
}

// Accessing from CLI
else
{

    // Passes the arguments to the run function of the AP class.
    array_shift($argv);

    // starting access point.
    echo call_user_func_array(array('AP', 'run'), $argv);
}