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
        $config = Config::getInstance();
        $url = str_replace('https://github.com/api/v2/json/pulls/', '', $url);
        $url = str_replace('/', '_', $url);
        $url = str_replace('?', '-', $url);
        $url = str_replace('&', '-', $url);

        //Check the cache first.
        if (isset(self::$cache[$url])) {
            return self::$cache[$url];
        }

        $content = file_get_contents($url);

        //Add to cache
        self::$cache[$url] = $content;

        return $content;
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

