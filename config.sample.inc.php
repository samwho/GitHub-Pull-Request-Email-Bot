<?php
/* 
 * This config file contains all of the information required for this code
 * to work. Please ensure that it is all correct.
 */

/*
 * Details for sending emails.
 */

// The email address to send to.
$PULL_REQUEST_BOT['email_to'] = 'you@example.com';

// The email address to display in the "from" field.
$PULL_REQUEST_BOT['email_from'] = 'you@example.com';

// The subject of the email.
$PULL_REQUEST_BOT['email_subject'] = 'New Pull Request';

// The email to send replies to.
$PULL_REQUEST_BOT['reply_to'] = 'you@example.com';

// Whether or not you are using HTML in the email.
$PULL_REQUEST_BOT['email_use_html'] = true;

// Set this to true if you want to group multiple pull requests into one email.
$PULL_REQUEST_BOT['group_requests'] = true;

/*
 * Details for the repository you want to fetch pull requests from.
 */

// The user that created the repo.
$PULL_REQUEST_BOT['repo_user'] = 'username';

// The name of the repo.
$PULL_REQUEST_BOT['repo_name'] = 'Project';

// The name of the calling server
$PULL_REQUEST_BOT['server_name'] = 'http://example.com';

?>