<?php

class AP
{
    /** @var stdClass  */
    protected static $map = null;

    /** @var string  */
    protected static $method = '';

    /** @var array */
    protected static $argv = null;

    /** @var callable */
    protected static $driver = null;

    /** @var stdClass */
    protected static $response = null;

    /** @var array  */
    protected static $path = array(
        'driver' => '',
        'service' => ''
    );

    protected static $dbo = null;

    /**
     * If the map has not been initialized then grab it.
     * if not then do nothing.
     */
    protected static function init()
    {
        if( !self::$map )
            self::$map = json_decode( file_get_contents( AP_MAP ) );
    }

    /**
     * Returns the map as an stdClass object.
     *
     * @return stdClass
     */
    public static function getMap()
    {
        // Checking if the class has initiated properly.
        self::init();

        return self::$map;
    }


    /**
     * Function Title says it all.  Checks the given strings for
     * Shortcodes and replaces them.  The shortcodes are found in
     * the map file.
     * @example
     * {{root}} => /var/www/html/  // Or where ever the root of the access point is.
     * @param $string
     * @return String
     */
    public static function shortCodes($string)
    {
        foreach(get_object_vars(self::getMap()->shortcodes) as $search => $replace)
        {
            $string = str_replace($search, constant($replace), $string);
        }

        return $string;
    }

    /**
     * Loads the needed drivers based on the map file.
     */
    public static function setDriver()
    {
        self::$driver = include(self::$path['driver']);
    }

    /**
     * Sets services needed based on the map file.
     * @param $argv
     * @return bool
     */
    public static function setService(&$argv)
    {
        // Pulls the first argument.
        $request = array_shift($argv);

        // Gets the map data.
        $map = self::getMap();

        // Filtering for shortcodes, building paths, settings paths.
        $path = self::shortCodes($map->services->path);
        $servicePath = self::shortCodes($map->services->{$request}->{self::$method});
        $driver = $map->services->{$request}->driver;
        self::$path['service'] = $path . $request . AP_DS . $servicePath;
        self::$path['driver'] = self::shortCodes($map->drivers->path) . $driver . AP_DS . 'index.php';

        // If no arguments left then function stops here.
        if(count($argv) === 0)
            return false;

        // If there are arguments left sets them globally.
        self::$argv = $argv;

        return false;
    }

    /**
     * Sets the request method.
     * @param $argv
     */
    public static function setMethod(&$argv)
    {
        self::$method = array_shift($argv);
    }

    /**
     * Preparing the driver to run then starts it.
     */
    public static function runDriver()
    {
        // Preparing any needed arguments to pass.
        $argv = array();

        // Checks if there are any global arguments.
        // If there are adds it to the front of the arguments to send.
        if( self::$argv !== null)
            array_unshift($argv, self::$argv);

        // Adding the callback method to be sent as an argument.
        array_unshift($argv, array('AP', 'setResponse'));

        // Adding the service path as an argument.
        array_unshift($argv, self::$path['service']);

        // passing the prepared array as a argument.
        call_user_func_array(self::$driver, $argv);
    }

    /**
     * Preparing to provide the final response.
     * It sets it globally.
     * @param $response
     * @return bool
     */
    public static function setResponse($response)
    {
        self::$response = $response;

        if(!isset($response->status))
            self::$response->status = 200;

        return true;
    }

    /**
     * The end of the line.  This is the last method to run before
     * returning the final response.
     * @return string
     */
    public static function finish()
    {
        return json_encode(self::$response);
    }

    /**
     * The starting point before it runs the other methods to get
     * a response.
     * @return string
     */
    public static function run()
    {
        $argv = func_get_args();
        self::setMethod($argv);
        self::setService($argv);
        self::setDriver();

        self::runDriver();
        return self::finish();
    }

    public static function getDbo()
    {
        if(!self::$dbo)
            self::$dbo = new DBO();

        return self::$dbo;
    }
}