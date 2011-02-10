<?php
/**
 * Static class to return the config array.
 *
 * Draws its information from config.inc.php
 *
 * @author Sam Rose
 */
class Config {
    /**
     * A cache variable for the config array in config.inc.php
     * @var Array
     */
    private static $config;

    /**
     * Fetches the configuratino array in config.inc.php. Caches it for faster
     * fetching if it is needed more than once in the same script.
     *
     * If the $new parameter is set to true, any old config version in the
     * cache will be deleted and a new one will be generated.
     *
     * @return Array $config
     */
    public static function getInstance($new = false) {
        //If the $config class variable is not set
        if (!isset(self::$config) || $new == true) {
            //Fetch the config file
            include_once dirname(__FILE__).'/../config.inc.php';
            //Set the $config class variable to the array in the config file
            self::$config = $PULL_REQUEST_BOT;
        }
        //Return the $config class variable
        return self::$config;
    }
}
?>
