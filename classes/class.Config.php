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
    private $config;

    /**
     * Singletone instance of the Config class.
     * @var Config
     */
    private static $config_instance;

    public function __construct($vals = null) {
        if (is_null($vals)) {
            $current_dir = dirname(__FILE__);
            $config_file = $current_dir.'/../config.inc.php';
            //Fetch the config file
            if (file_exists($config_file)) {
                require_once $config_file;
                //Set the $config class variable to the array in the config file
                $this->config = $PULL_REQUEST_BOT;

                $this->config['debug_dir'] = $current_dir . '/../debug';
                $this->config['tests_dir'] = $current_dir . '/../tests';
                $this->config['templates_dir'] = $current_dir . '/../templates';
                $this->config['data_dir'] = $current_dir . '/../data';
                $this->config['classes_dir'] = $current_dir . '/../classes';
                $this->config['extlib_dir'] = $current_dir . '/../extlib';
                $this->config['testdata_dir'] = $this->config['tests_dir'] . '/testdata';
            } else {
                $message = 'Could not find the config file at "' . $current_dir . '/../config.inc.php". ';
                $message .= 'Please ensure that it exists before trying again.' . "\n";
                echo $message;
                exit;
            }
        } else {
            $this->config = $vals;
        }
    }

    /**
     * Fetches the configuratino array in config.inc.php. Caches it for faster
     * fetching if it is needed more than once in the same script.
     *
     * If the $new parameter is set to true, any old config version in the
     * cache will be deleted and a new one will be generated.
     *
     * @return Array $config
     */
    public static function getInstance($new = false, $vals = null) {
        //If the $config class variable is not set
        if (!isset(self::$config_instance) || $new == true) {
            self::$config_instance = new Config($vals);
        }
        //Return the $config class variable
        return self::$config_instance;
    }

    /**
     * Get a value from the application configuration.
     *
     * @param str $key
     * @return mixed $value
     */
    public function getValue($key) {
        return $this->config[$key];
    }

    /**
     * Set a value in the application config. Stateless.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValue($key, $value) {
        $this->config[$key] = $value;
    }
}
?>
