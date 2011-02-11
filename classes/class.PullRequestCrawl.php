<?php
require_once 'class.PullRequestFetcher.php';
require_once 'class.Config.php';
require_once 'class.TemplateParser.php';
require_once 'class.Email.php';

/**
 * This class fetches the pull requests and sends the emails. It's where the
 * magic happens.
 *
 * @author Sam Rose
 */
class PullRequestCrawl {
    /**
     * An instance of the PullRequestFetcher class for obtaining pull requests.
     *
     * @var PullRequestFetcher $fetcher
     */
    private $fetcher;

    /**
     * The config array from config.inc.php.
     *
     * @var Array $config
     */
    private $config;

    public function  __construct() {
        $this->fetcher = new PullRequestFetcher();
        $this->config = Config::getInstance();
    }

    public function run() {
        $requests = $this->fetcher->getRequestsSinceLastCrawl();

        if (count($requests) > 0) {
            //Send the requests all in one email
            if ($this->config['group_requests']) {

                $formatted_pull_requests = '';
                foreach ($requests as $request) {
                    $formatted_pull_requests .= TemplateParser::parse(
                            'templates/pull_request_group.tpl', $request);
                }

                $group_email_placeholders =
                    array('pull_requests' => $formatted_pull_requests);
                
                $content = TemplateParser::parse(
                        'templates/group_request_email.tpl', $request, $group_email_placeholders);

                Email::send($content);

            } else {
                //Send requests in multiple emails
                foreach ($requests as $request) {
                    $content = TemplateParser::parse(
                            'templates/pull_request_single.tpl', $request);
                    $subject = TemplateParser::parse(
                            'templates/pull_request_single_subject_line.tpl', $request);
                    Email::send($content, $subject);
                }
            }

            $this->fetcher->saveLatestPull();
        }
    }
}
?>
