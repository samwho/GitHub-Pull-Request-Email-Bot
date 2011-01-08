<?php
/*
 * This file is entirely for just small bits of code that I want to test on
 * the fly.
 */

require_once '../classes/class.PullRequestFetcher.php';

$fetcher = new PullRequestFetcher();
echo $fetcher->requestUrl();
$fetcher->showRequests();
?>
