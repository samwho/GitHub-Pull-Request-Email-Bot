<?php
class Loader {
    /**
     * Registers our lazy loading function.
     *
     * @return bool True on success, false on failure.
     */
    public static function register() {
        return spl_autoload_register(array(__CLASS__, 'load' ));
    }

    /**
     * Unregisters our lazy loading function.
     *
     * @return bool True on success, false on failure.
     */
    public static function unregister() {
        return spl_autoload_unregister( array(__CLASS__, 'load') );
    }

    /**
     * Our lazy loading function. Searches the classes/ directory for the class being loaded.
     *
     * @param str $class The class that is being loaded.
     */
    public static function load($class) {
        // if class already exists, return
        if ( class_exists($class, false) ) {
            return;
        }

        $path = dirname(__FILE__) . '/';
        $file_name = $path . 'class.' . $class . '.php';
        if ( file_exists( $file_name )) {
            require_once $file_name;
            return;
        }

        $path = dirname(__FILE__) . '/../tests/classes/';
        $file_name = $path . 'mock.' . $class . '.php';
        if ( file_exists( $file_name )) {
            require_once $file_name;
            return;
        }

        $path = dirname(__FILE__) . '/../tests/';
        $file_name = $path . $class . '.php';
        if ( file_exists( $file_name )) {
            require_once $file_name;
            return;
        }
    }
}
