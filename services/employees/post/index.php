<?php
return

    /**
     * Adding new rows to the database.
     * Uses smart parameters.
     *
     * Expected Parameters in this order.
     * (first_name, last_name, email, position, salary)
     *
     * Created an INSERT query and used it in MySQL with DBO Class.
     *
     * @returns stdClass
     */
    function()
    {
        // Array of the columns from employee table.
        $columnsArray = array(
            'first_name',
            'last_name',
            'email',
            'position',
            'salary',
            'active',
            'start_date',
            'end_date'
        );

        // What the arguments received should equal.
        $argc = (count($columnsArray) - 3);

        // Columns array turned into a string to be used in the query.
        $columns = implode(', ', $columnsArray);

        // Starting the INSERT query.
        $query = "INSERT INTO employees ({$columns}) VALUES (";

        // Starting the response.
        $results = new stdClass();

        // Checking the received response.
        if(func_num_args() !== $argc)
        {
            // If it is less than expected returns a bad request.
            $results->message = 'Bad Request';
            $results->status = 400;

            return $results;
        }

        // Getting the Database Object (DBO Class)
        $dbo = AP::getDbo();

        // Preparing the values.
        $valueArray = array();

        // Active
        $valueArray[] = true;

        // Start Date
        $valueArray[] = (string) date('Y-m-d');

        // End Date
        $valueArray[] = (string) '0000-00-00';

        // Merging the arguments received in the front
        // and current values in the back.
        $valueArray = array_merge(func_get_args(), $valueArray);

        foreach($valueArray as $key => $val)
        {
            // Preparing Statements to protect against SQL Injection.
            $dbo->setBind($valueArray[$key]);
        }

        // Creating the values as a string.
        $value = implode(', ', $valueArray);

        // finishing the query.
        $query .= "{$value})";

        // Running the query.
        $results = $dbo->runQuery($query);

        // Returning results.
        return $results;
    };