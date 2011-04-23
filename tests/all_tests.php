<?php
require_once dirname(__FILE__) . '/tests.init.php';
$all_tests = & new TestSuite('All tests');

$all_tests->add(new TestOfTemplateParser());
