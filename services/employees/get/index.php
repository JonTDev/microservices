<?php
return

    /**
     * Using smart parameters here, however only accepts in pairs.
     * Anything that is a odd number is returned as a bad request.
     *
     * SELECT query created and sent to MySQL using the DBO class.
     *
     * Order of operation is Column, Value
     * (id, 1)
     * {
     *      "id":"1",
     *      "first_name":"Gerladina",
     *      "last_name":"Van Daalen",
     *      "email":"g.van daalen@best-company.ever",
     *      "position":"Senior Developer",
     *      "salary":"$210377",
     *      "active":"0",
     *      "start_date":"2014-08-07",
     *      "end_date":"2011-03-07",
     *      "status":200
     * }
     *
     * @return stdClass
     */
    function()
    {

        // Starting Select Query
        $query = "SELECT * FROM employees";

        // Checking for any arguments.
        if(func_num_args() === 0)
        {
            // If no arguments then runs the query.
            $results = AP::getDbo()->runQuery($query);

            // Returns the results.
            return $results;
        }

        // Making it this far means there are arguments.
        // Checking if they are even numbers.
        if(func_num_args() % 2 !== 0)
        {
            // If they are even then creating and returning the response.
            $results = new stdClass();
            $results->message = 'Bad Request';
            $results->status = 400;

            return $results;
        }

        // Getting the arguments.
        $argv = func_get_args();

        // Preparing variables.
        $whereArray = array();
        $columns = array();
        $values = array();

        // Getting the Database Object (DBO Class)
        $dbo = AP::getDbo();

        // Filtering the arguments into columns and values.
        foreach($argv as $key => $val)
        {
            // Every even number is a value and odd is a column.
            if($key % 2 !== 0)
            {
                // Prepared Statements to protect against sql injection.
                $dbo->setBind($val);

                // Setting the value.
                $values[] = $val;
            }
            else
                // Setting the column
                $columns[] = $val;

        }

        // Setting up the where statement.
        foreach($columns as $key => $val)
        {
            // Setting the where statements.
            $whereArray[] = "{$val} = {$values[$key]}";
        }

        // Turnign the where array into a string.
        $where = implode(' AND ', $whereArray);

        // Finishing the query.
        $query .= " WHERE {$where}";

        // Running the query.
        $results = $dbo->runQuery($query);

        // Checking for empty results.
        if(!$results)
        {
            // No results found creating and sending the response.
            $obj = new stdClass();

            $obj->message = 'No Results Found';

            return $obj;
        }

        // Returning the results.
        return $results;
    };