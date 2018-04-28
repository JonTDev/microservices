<?php

return
    /**
     * Pretty simple, just takes the path and passes it to node
     * and index.js as well as any arguments that may have been
     * passed.  Once the response comes in we pass that to the
     * callback.
     * @param string $path
     * @param callable $callback
     * @param null|array $argv
     */
    function($path, $callback, $argv = null) {

        // Getting the path to this directory.
        $node = __DIR__ . AP_DS;

        // Creating the command to use.
        $cmd = "node {$node}index.js {$path}";

        // Checking if arguments are empty.
        if($argv !== null)

            // If not empty then we will turn the array
            // into a string and ensure that all of the
            // arguments are wrapped in single quotes.
            // Normally you would want to check each variable
            // and pass them accordingly.

            $cmd .= ' \'' . implode('\' \'', $argv) . '\'';

        // converting the response into a stdClass for readability by php
        $results = json_decode(exec($cmd));

        // Returning the response to the callback.
        $callback($results);
};