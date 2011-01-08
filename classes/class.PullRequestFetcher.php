<?php
require_once 'class.WebPage.php';
require_once 'class.Config.php';
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
    private $last_pull_file_name = 'data/last_pull.txt';

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

    public function __construct() {
        $this->config = Config::getInstance();
        $this->requests = json_decode(WebPage::get($this->requestUrl()))->pulls;
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
    public function requestUrl($which = 'open') {
        switch ($which) {
            case 'open':
                return 'http://github.com/api/v2/json/pulls/'.
                $this->config['repo_user'].'/'.$this->config['repo_name'].'/open';
                break;
            case 'closed':
                return 'http://github.com/api/v2/json/pulls/'.
                $this->config['repo_user'].'/'.$this->config['repo_name'].'/closed';
                break;
            default:
                return 'http://github.com/api/v2/json/pulls/'.
                $this->config['repo_user'].'/'.$this->config['repo_name'];
        }
    }

    /**
     * Saves the number of the latest pull request in a file for future
     * reference.
     */
    public function saveLatestPull() {
        $file = fopen($this->last_pull_file_name, 'w');
        fwrite($file, $this->requests[0]->number);
    }

    /**
     * Gets the number of the latest pull request this bot emailed about.
     *
     * @return String $number
     */
    public function getLatestPull() {
        return intval(file_get_contents($this->last_pull_file_name));
    }

    /**
     * Deletes the file that saves the number of the last pull request.
     *
     * For testing puposes only.
     */
    public function deleteLatestPull() {
        unlink($this->last_pull_file_name);
    }

    /**
     * Returns an array of the pull requests since the last crawl ran.
     *
     * @return Array $array
     */
    public function getRequestsSinceLastCrawl() {
        $latest_pull = $this->getLatestPull() ? $this->getLatestPull() : -1;

        $return_array = array();
        foreach ($this->requests as $request) {
            if ($request->number > $latest_pull) {
                $return_array[] = $request;
            }
        }

        return $return_array;
    }
}
?>
