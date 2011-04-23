<?php
require_once dirname(__FILE__) . '/init.php';
$crawler = new PullRequestCrawl();
$crawler->run();
