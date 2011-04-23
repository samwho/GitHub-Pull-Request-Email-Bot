<?php
/**
 * A class to fetch the contents of web pages.
 *
 * @author Sam Rose
 */
class WebPage {

    /**
     * Stores the last error from this class.
     *
     * @var String $last_error
     */
    private static $last_error;
    
    /**
     * Stores the results from web requests as an associative array.
     * 
     * @var Array $cache
     */
    private static $cache = array();
    
    /**
     * Get a web file from a URL.
     */
    public static function get($url) {
        //Check the cache first.
        if (isset(self::$cache[$url])) {
            return self::$cache[$url];
        }

        $config = Config::getInstance();

        if (self::get_curl()) {
            $options = array(
                CURLOPT_RETURNTRANSFER => true, // return web page
                CURLOPT_HEADER => false, // don't return headers
                CURLOPT_FOLLOWLOCATION => true, // follow redirects
                CURLOPT_ENCODING => "", // handle all encodings
                CURLOPT_USERAGENT => "GitHub Pull Request Bot", // who am i
                CURLOPT_AUTOREFERER => true, // set referer on redirect
                CURLOPT_REFERER => isset($_SERVER['HTTPS']) ? 'https://' . $config['server_name'] :
                    'http://' . $config['server_name'],
                CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
                CURLOPT_TIMEOUT => 120, // timeout on response
                CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            );

            $ch = curl_init($url);

            // fix for people who are still running old versions of PHP
            if (function_exists('curl_setopt_array')) {
                curl_setopt_array($ch, $options);
            } else {
                foreach ($options as $option => $value) {
                    curl_setopt($ch, $option, $value);
                }
            }

            $content = curl_exec($ch);
            self::$last_error = curl_error($ch);

            curl_close($ch);

            if (self::$last_error != 0) {
                return false;
            }

            //Add to cache
            self::$cache[$url] = $content;

            return $content;

        } else if (self::get_allow_url_fopen()) {
            //Check cache first
            if (self::$cache[$url] != null) {
                return self::$cache[$url];
            }

            //Get content and add it to the cache
            $content = file_get_contents($url);
            self::$cache[$url] = $content;

            //Return the content
            return $content;
        } else {
            self::$last_error = 'cURL not loaded and allow_url_fopen set to
                false. Cannot load web page.';
            return false;
        }
    }

    /**
     * Checks if cURL is installed and loaded on the server. Return true if it
     * is, false if it isn't.
     *
     * @return bool $curl_installed
     */
    private static function get_curl() {
        return in_array('curl', get_loaded_extensions());
    }

    /**
     * Checks if the php.ini is allowing file_get_contents() to access external
     * web pages. True if it is, false if it isn't.
     *
     * @return bool $allow_url_fopen
     */
    private static function get_allow_url_fopen() {
        if (ini_get("allow_url_fopen") == "1") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * If a string is passed in with the $url parameter, only that url is
     * removed from the cache. If called with no arguments, the entire cache
     * is emptied.
     *
     * @param String $url
     */
    public static function emptyCache($url = null) {
        if ($url == null) {
            self::$cache = array();
        } else {
            self::$cache[$url] = null;
        }
    }

    /**
     * Checks that either cURL is loaded or allow_url_fopen is set to 1.
     *
     * @return bool $works
     */
    public static function checkWorks() {
        return self::get_allow_url_fopen() && self::get_curl();
    }

}
