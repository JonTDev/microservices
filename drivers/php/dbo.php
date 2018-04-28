<?php

/**
 * I personally like the idea of a factory calling anything that
 * communicates with a database.  This keeps the connections low.
 *
 * The extending is just preference.
 */
class DBO extends PDO
{
    // Allows for easy binding and forgetting.
    protected $binds = array();

    /**
     * Pulls the database information from the map file.
     * DBO constructor.
     */
    public function __construct()
    {
        $data = AP::getMap()->database;
        $dsn = "mysql:host={$data->host};dbname={$data->database}";
        $options = array( self::ATTR_ERRMODE => self::ERRMODE_EXCEPTION );
        parent::__construct($dsn, $data->user, $data->pass, $options);
    }

    /**
     * Allows for easy binding and forgetting.
     *
     * @example
     * $dbo = new DBO();
     * $id = 13;
     * $first = 'Jon';
     * $last = 'Taylor';
     * $dbo->setBind($id)
     *     ->setBind($first)
     *     ->setBind($last);
     * $query = "SELECT * FROM employees WHERE id={$od} AND ";
     * $query .= "first_name={$first} AND last_name={$last}";
     * $results = $dbo->runQuery($query);
     * var_dump($results);
     *
     * @param $value
     * @return $this for chaining.
     */
    public function setBind(&$value)
    {
        $bind = uniqid(':');
        $this->binds[$bind] = $value;
        $value = $bind;
        return $this;
    }

    /**
     * Runs the query provided using prepared statements.
     * If there are no binds done before it will not provide an empty
     * array.
     *
     * @example
     * $id = 13;
     * $results = $dbo
     *      ->setBind($id)
     *      ->runQuery("SELECT * FROM employees WHERE id={$id}";
     * var_dump($results);
     *
     * @param $query
     * @return array|null|stdClass
     */
    public function runQuery($query)
    {
        $stmt = $this->prepare($query);

        try
        {
            if(count($this->binds) > 0)
                $success = $stmt->execute($this->binds);
            else
                $success = $stmt->execute();

            if(strpos($query, 'SELECT') !== false)
            {

                $results = $stmt->fetchAll(self::FETCH_CLASS);
                if(is_array($results))
                {
                    if(count($results) === 1) return $results[0];
                    if(!$results[0]) return null;
                    return $results;
                }
            }

            $results = new stdClass();

            if($success)
                $results->message = 'Success';
            else
            {
                $results->message = 'Unsuccessful';
                $results->status = 500;
            }


            return $results;
        }

        catch(PDOException $err)
        {
            $results = new stdClass();
            foreach($this->binds as $search => $replace)
            {
                var_dump($replace);
                if(!is_string($replace))
                    $query = str_replace($search, $replace, $query);
                else
                    $query = str_replace($search, '\''. $replace . '\'', $query);
            }
            // TODO:  You would set a logging method here.
            echo PHP_EOL;
            echo PHP_EOL;
            var_dump($err);
            echo PHP_EOL;
            echo PHP_EOL;
            var_dump($this->binds);
            echo PHP_EOL;
            echo PHP_EOL;
            var_dump($query);
            die();
            $results->message = 'Unsuccessful Request';

            return $results;
        }

    }
}