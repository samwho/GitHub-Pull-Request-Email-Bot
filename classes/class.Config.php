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
            $current_dir = dirname(__FILE__);
            $config_file = $current_dir.'/../config.inc.php';
            //Fetch the config file
            if (file_exists($config_file)) {
                require_once $config_file;
                //Set the $config class variable to the array in the config file
                self::$config = $PULL_REQUEST_BOT;

                self::$config['debug_dir'] = $current_dir . '/../debug';
                self::$config['tests_dir'] = $current_dir . '/../tests';
                self::$config['templates_dir'] = $current_dir . '/../templates';
                self::$config['data_dir'] = $current_dir . '/../data';
            } else {
                $message = 'Could not find the config file at "' . $current_dir . '/../config.inc.php". ';
                $message .= 'Please ensure that it exists before trying again.' . "\n";
                echo $message;
                exit;
            }
        }
        //Return the $config class variable
        return self::$config;
    }
}
?>
