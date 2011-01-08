<?php
require_once '../classes/class.PullRequestFetcher.php';

$fetcher = new PullRequestFetcher();
echo $fetcher->requestUrl();
$fetcher->showRequests();
?>
