<?php
require_once dirname(__FILE__) . '/classes/class.Loader.php';
Loader::register();
$config = Config::getInstance();

if (!WebPage::checkWorks()) {
    echo "You need either cURL installed or allor_url_fopen set to true in your php.ini to run this script.\n";
    exit;
}

if (!is_writeable($config->getValue('data_dir'))) {
    echo 'The data directory "'.$config->getValue('data_dir').'" needs to be writable by the server to run this script.' . "\n";
    exit;
}

if (!is_readable($config->getValue('templates_dir'))) {
    echo 'The templates directory "'.$config->getValue('data_dir').'" needs to be readable by the server to run this script.' . "\n";
    exit;
}

if ($config->getValue('debug')) {
    error_reporting(E_ALL);
}