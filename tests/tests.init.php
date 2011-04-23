<?php
require_once dirname(__FILE__) . '/../classes/class.Loader.php';
Loader::register();
$config = Config::getInstance();
require_once $config->getValue('extlib_dir') . '/simpletest/autorun.php';
