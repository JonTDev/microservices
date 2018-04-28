<?php
return
    /**
     * This function only accepts one parameter.
     * It is looking for the ID to the employees table.
     * Creating an stdClass to store any data to return.
     *
     * Performs a delete query to MySQL using the DBO class.
     *
     * (id)
     *
     * @return stdClass
     */
    function()
{
    $results = new stdClass();

    if(func_num_args() < 1)
    {
        $results->message = 'Bad Request';
        $results->status = 400;

        return $results;
    }

    $dbo = AP::getDbo();

    $id = func_get_arg(0);
    $dbo->setBind($id);

    $query = 'DELETE FROM employees WHERE id = ' . $id;

    $results = $dbo->runQuery($query);

    return $results;
};