<?php
// Defines are here because I am lazy.
define('DS', DIRECTORY_SEPARATOR);

// Assuming this is being ran in the root.
define('PATH_H', __DIR__);

/**
 * Uses smart parameters
 * Takes the arguments and uses them in the CLI for the access point.
 * @example
 * get_access('info', 'employees', 'id', 13);
 * return
 * {
 *   "id" : 13,
 *   "name" : "John Smith",
 *   "active" : true,
 *   "title" : "Universe Master",
 *   "reports_to" : 1
 * }
 * @return string
 */
function get_access()
{
    // Path to the access point.
    $path = PATH_H . DS . 'access' . DS . 'index.py';

    // Taking any argument for this function.
    $argv = implode(' ', func_get_args());

    // Execution and getting the output.
    exec("python {$path} {$argv}", $output);

    // Returning the output.
    return $output[0];
}

echo get_access('info', 'employees', 'id', 13);