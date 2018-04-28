<?php
return
    /**
     * Smart Parameters.
     * Parameters must be an odd number and must be three or higher.
     * (ID, ColumnName, Value)  Column Name and Value
     * can be as many as needed as long as they are Column then Value.
     *
     * Query is created and ran in MySQL with DBO Class.
     *
     * @return stdClass
     */
    function()
    {
        // Creating the response.
        $results = new stdClass();

        // Getting the number of arguments.
        $argc = func_num_args();

        // Checking count.
        if($argc % 2 === 0 || $argc <= 2)
        {
            // If it fails preparing response and sending it.
            $results->message = 'Bad Request';
            $results->status = 400;

            return $results;
        }


        // Getting the Database Object (DBO Class)
        $dbo = AP::getDbo();

        // Starting the update Query.
        $query = 'UPDATE employees SET ';

        // Getting the arguments.
        $argv = func_get_args();

        // Getting the ID and adding it to the current query.
        $id = 'WHERE id = ' . array_shift($argv);

        // Preparing Variables.
        $columns = array();
        $values = array();
        $setArray = array();

        // Filtering columns
        foreach($argv as $key => $val)
        {
            // If it is an odd number "starts at 0"
            if($key % 2 !== 0)
            {
                // Preparing Statements to protect against SQL Injection.
                $dbo->setBind($val);
                $values[] = $val;

                // Stopping this round and going to the next.
                continue;
            }

            // Columns
            $columns[] = $val;
        }

        // Creating the SET part of the query.
        foreach($columns as $key => $column)
        {
            $setArray[] = "{$column} = {$values[$key]}";
        }

        // Finishing the query.
        $query .= implode(', ', $setArray) . " {$id}";

        // Running the query.
        $results = $dbo->runQuery($query);

        // Returning the results.
        return $results;
    };