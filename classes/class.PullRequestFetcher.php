<?php
/**
 * Class to fetch pull request data from a repository.
 *
 * @author Sam Rose
 */
class PullRequestFetcher {

    /**
     * The location of the file that contains the number of the last pull
     * request emailed about.
     *
     * @var String $last_pull_file_name
     */
    private $last_pull_file_name = 'last_pull.txt';

    /**
     * An associative array of pull requests.
     *
     * @var Array $pull_requests
     */
    private $requests;

    /**
     * The config array from config.inc.php
     *
     * @var Array $config
     */
    private $config;

    public function __construct($username = null, $repo = null) {
        $this->config = Config::getInstance();

        if (!is_null($username)) {
            $this->config->setValue('repo_user', $username);
        }
        if (!is_null($repo)) {
            $this->config->setValue('repo_name', $repo);
        }
        
        $this->requests = $this->getPullRequests();

        if (is_writeable($this->config->getValue('debug_dir')) && $this->config->getValue('debug') == true) {
            file_put_contents($this->config->getValue('debug_dir') . '/last_request.json', print_r($this->requests, true));
        }
    }

    /**
     * Gets a list of the configurated repo's pull requests.
     * 
     * @return array A json_decoded array of pull requests on the repo in the config. 
     */
    public function getPullRequests() {
        return json_decode(WebPage::get($this->listRequestsUrl()))->pulls;
    }

    /**
     * Shows all pull requests. For testing purposes mainly.
     */
    public function showRequests() {
        echo '<pre>';
        print_r($this->requests);
        echo '</pre>';
    }

    /**
     * Gets the API request URL for all pull requests.
     *
     * The $which parameter can be one of two values:
     * 
     * 'open' - returns only open pull requests. (default)
     * 'closed' - returns only closed pull requests.
     *
     * @return String $api_request_url
     */
    private function listRequestsUrl($which = 'open') {
        switch ($which) {
            case 'open':
                return 'http://github.com/api/v2/json/pulls/'.
                $this->config->getValue('repo_user').'/'.$this->config->getValue('repo_name').'/open';
                break;
            case 'closed':
                return 'http://github.com/api/v2/json/pulls/'.
                $this->config->getValue('repo_user').'/'.$this->config->getValue('repo_name').'/closed';
                break;
            default:
                return 'http://github.com/api/v2/json/pulls/'.
                $this->config->getValue('repo_user').'/'.$this->config->getValue('repo_name');
        }
    }

    /**
     * Returns information on a specific pull request. Also updates that pull request's information in the data
     * directory.
     *
     * @param str $pull_request
     * @return array json_decoded information on the pull request.
     */
    public function updatePullRequestInfo($pull_request) {
        // strip the file type off if it exists
        $pull_request = str_replace('.json', '', $pull_request);

        $url = 'http://github.com/api/v2/json/pulls/'.
                $this->config->getValue('repo_user').'/'.$this->config->getValue('repo_name').'/'.intval($pull_request);

        $url_contents = json_decode(WebPage::get($url));
        
        if (!empty ($url_contents)) {
            file_put_contents($this->config->getValue('data_dir') . '/' . $pull_request . '.json',
                    json_encode($url_contents->pull));
            return $url_contents->pull;
        } else {
            return null;
        }
    }

    /**
     * Gets the comments on a particular pull request.
     *
     * @param int $pull_request
     * @return array Comments.
     */
    public function getPullRequestComments($pull_request) {
        // strip the file type off if it exists
        $pull_request = intval(str_replace('.json', '', $pull_request));
        if (file_exists($this->config->getValue('data_dir') . '/' . $pull_request . '.json')) {
            $request = json_decode(file_get_contents($this->config->getValue('data_dir') . '/' .
                    $pull_request . '.json'));
            $discussion = array_filter($request->discussion, __CLASS__.'::filterPullRequestComments');
        } else {
            return null;
        }
    }

    /**
     * Saves the number of the latest pull request in a file for future
     * reference.
     */
    public function saveLatestPull() {
        return file_put_contents($this->config->getValue('data_dir') . '/' . $this->last_pull_file_name,
                $this->requests[0]->number);
    }

    /**
     * Gets the number of the latest pull request this bot emailed about.
     *
     * @return String $number
     */
    public function getLatestPull() {
        if (file_exists($this->config->getValue('data_dir') . '/' . $this->last_pull_file_name)) {
            return intval(file_get_contents($this->config->getValue('data_dir') . '/' . $this->last_pull_file_name));
        } else {
            return -1;
        }
    }

    /**
     * Deletes the file that saves the number of the last pull request.
     *
     * For testing puposes only.
     */
    public function deleteLatestPull() {
        unlink($this->config->getValue('data_dir') . '/' . $this->last_pull_file_name);
    }

    /**
     * Returns an array of the pull requests since the last crawl ran.
     *
     * @return Array $array
     */
    public function getRequestsSinceLastCrawl() {
        $latest_pull = $this->getLatestPull();

        $return_array = array();
        foreach ($this->requests as $request) {
            if ($request->number > $latest_pull) {
                $return_array[] = $request;
            }
        }

        return $return_array;
    }

    /**
     * Returns an associative array of pull requests that have already been fetched before.
     *
     * @return array Associative array of PullRequestNumber => JSON Contents.
     */
    public function getOldPullRequests() {
        $return = array();
        $dir = array_filter(scandir($this->config->getValue('data_dir')) , __CLASS__.'::filterPullRequestFiles');
        foreach ($dir as $file) {
            $return[str_replace('.json', '', $file)] = json_decode(file_get_contents($this->config->getValue('data_dir') . '/' .
                    $file));
        }
        return $return;
    }

    /**
     * Determines whether or not a filename is the name of a specific pull request file.
     * 
     * @param str $file Path to file.
     * @return bool True if the file is a pull request json file, false otherwise.
     */
    private static function filterPullRequestFiles($file) {
        return preg_match('/[0-9]+\.json/', $file);
    }

    /**
     * Determines whether or not a comment on a pull request is to be emailed.
     *
     * @param stdClass $comment
     * @return bool
     */
    private static function filterPullRequestComments($comment) {
        if ($comment->type == 'IssueComment') {
            return true;
        } else {
            return false;
        }
    }
}
