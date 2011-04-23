<?php
/*
 * This file is entirely for just small bits of code that I want to test on
 * the fly.
 */
require_once dirname(__FILE__) . '/../init.php';

$fetcher = new PullRequestFetcher();
print_r($fetcher->getOldPullRequests());
?>
