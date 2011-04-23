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

    /**
     * Runs the crawl process. This is essentially the "main" method of this program. It is the entry point.
     */
    public function run() {
        $this->checkForNewPulls();
        
        if ($this->config->getValue('alert_on_close') == true) {
            $this->checkPullsClosed();
        }
    }

    /**
     * Checks for new pull requests and sends emails if it finds any.
     */
    private function checkForNewPulls() {
        if (count($this->new_requests) > 0) {

            // save new requests
            foreach ($this->new_requests as $request) {
                file_put_contents($this->config->getValue('data_dir') . '/' . $request->number . '.json', json_encode($request));
            }

            //Send the requests all in one email
            if ($this->config->getValue('group_requests')) {

                $formatted_pull_requests = '';
                foreach ($this->new_requests as $request) {
                    $formatted_pull_requests .= TemplateParser::parse('_request.tpl', $request);
                }

                $group_email_placeholders =
                    array('pull_requests' => $formatted_pull_requests);

                $content = TemplateParser::parse('group_request.tpl', $request,
                        $group_email_placeholders);

                $subject = TemplateParser::parse('group_request_subject.tpl', $request);

                Email::send($content, $subject);

            } else {
                //Send requests in multiple emails
                foreach ($this->new_requests as $request) {
                    $content = TemplateParser::parse('single_request.tpl', $request);
                    $subject = TemplateParser::parse('single_request_subject.tpl', $request);
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
        if ($this->config->getValue('group_requests')) {
            $formatted_closed_requests = '';
            foreach ($this->old_requests as $number=>$request) {
                $request = $this->fetcher->updatePullRequestInfo($number);
                if ($request->state == 'closed') {
                    $formatted_closed_requests .= TemplateParser::parse('_closed.tpl', $request);

                    unlink($this->config->getValue('data_dir') . '/' .$request->number. '.json');
                }
            }

            // if no closed requests, return from the method
            if ($formatted_closed_requests == '') {
                return;
            }

            $group_email_placeholders = array (
                'closed_requests' => $formatted_closed_requests
            );

            $content = TemplateParser::parse('group_closed.tpl', $request,
                        $group_email_placeholders);

            $subject = TemplateParser::parse('group_closed_subject.tpl', $request);
            Email::send($content, $subject);
        } else {
            foreach ($this->old_requests as $number=>$request) {
                $request = $this->fetcher->updatePullRequestInfo($number);
                
                if ($request->state == 'closed') {
                    $content = TemplateParser::parse('single_closed.tpl', $request);
                    $subject = TemplateParser::parse('single_closed_subject.tpl', $request);
                    Email::send($content, $subject);

                    unlink($this->config->getValue('data_dir') . '/' .$request->number. '.json');
                }
            }
        }
    }
}
