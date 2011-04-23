<?php
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

    /**
     * Requests since the last crawl ran.
     * 
     * @var Array $new_requests
     */
    private $new_requests;

    /**
     * Requests the crawler has checked before.
     * 
     * @var Array
     */
    private $old_requests;

    public function  __construct() {
        $this->fetcher = new PullRequestFetcher();
        $this->config = Config::getInstance();
        $this->old_requests = $this->fetcher->getOldPullRequests();
        $this->new_requests = $this->fetcher->getRequestsSinceLastCrawl();
    }

    public function run() {
        $this->checkForNewPulls();
        $this->checkPullsClosed();
    }

    private function checkForNewPulls() {
        if (count($this->new_requests) > 0) {

            // save new requests
            foreach ($this->new_requests as $request) {
                file_put_contents($this->config['data_dir'] . '/' . $request->number . '.json', json_encode($request));
            }

            //Send the requests all in one email
            if ($this->config['group_requests']) {

                $formatted_pull_requests = '';
                foreach ($this->new_requests as $request) {
                    $formatted_pull_requests .= TemplateParser::parse(
                            $this->config['templates_dir'] . '/pull_request_group.tpl', $request);
                }

                $group_email_placeholders =
                    array('pull_requests' => $formatted_pull_requests);

                $content = TemplateParser::parse(
                        $this->config['templates_dir'] . '/group_request_email.tpl', $request,
                        $group_email_placeholders);

                $subject = TemplateParser::parse(
                            $this->config['templates_dir'] . '/pull_request_group_subject_line.tpl', $request);

                Email::send($content, $subject);

            } else {
                //Send requests in multiple emails
                foreach ($this->new_requests as $request) {
                    $content = TemplateParser::parse(
                            $this->config['templates_dir'] . '/pull_request_single.tpl', $request);
                    $subject = TemplateParser::parse(
                            $this->config['templates_dir'] . '/pull_request_single_subject_line.tpl', $request);
                    Email::send($content, $subject);
                }
            }
            $this->fetcher->saveLatestPull();
        }
    }

    /**
     * Checks to see if any of the previously scanned pull requests have been closed.
     */
    private function checkPullsClosed() {
        if ($this->config['group_requests']) {
            $formatted_closed_requests = '';
            foreach ($this->old_requests as $number=>$request) {
                $request = $this->fetcher->updatePullRequestInfo($number);
                if ($request->state == 'closed') {
                    $formatted_closed_requests .= TemplateParser::parse(
                            $this->config['templates_dir'] . '/pull_request_closed_single.tpl', $request);

                    unlink($this->config['data_dir'] . '/' .$request->number. '.json');
                }
            }

            // if no closed requests, return from the method
            if ($formatted_closed_requests == '') {
                return;
            }

            $group_email_placeholders = array (
                'closed_requests' => $formatted_closed_requests
            );

            $content = TemplateParser::parse(
                        $this->config['templates_dir'] . '/group_closed_email.tpl', $request,
                        $group_email_placeholders);

            $subject = 'Re: ' . TemplateParser::parse(
                        $this->config['templates_dir'] . '/pull_request_group_subject_line.tpl', $request);
            Email::send($content, $subject);
        } else {
            foreach ($this->old_requests as $request) {
                $request = $this->fetcher->updatePullRequestInfo($number);
                if ($request->state == 'closed') {
                    $content = TemplateParser::parse(
                            $this->config['templates_dir'] . '/pull_request_closed.tpl', $request);
                    $subject = 'Re: ' . TemplateParser::parse(
                            $this->config['templates_dir'] . '/pull_request_single_subject_line.tpl', $request);
                    Email::send($content, $subject);

                    unlink($this->config['data_dir'] . '/' .$request->number. '.json');
                }
            }
        }
    }
}
