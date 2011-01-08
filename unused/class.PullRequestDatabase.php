<?php
require_once 'class.Config.php';

/**
 * An extension of the PDO class to make connecting to the database for this
 * code easier.
 *
 * @author Sam Rose
 * @deprecated deprecated since before release.
 *
 */
class PullRequestDatabase extends PDO {
    /**
     * Stores the configuration array from config.inc.php
     *
     * @var Array $config
     */
    private static $config;

    public function __construct($config = null) {
        if ($config == null) {
            self::$config = Config::getInstance();
        } else {
            self::$config = $config;
        }

        parent::__construct($this->dsn(),
                        self::$config['db_user'],
                        self::$config['db_pass']);
    }

    /**
     * Returns the $dsn parameter for the PDO constructor.
     *
     * @return String $dsn
     */
    private function dsn() {
        return 'mysql:dbname=' . self::$config['db_name'] .
            ';host=' . self::$config['db_host'];
    }

    /**
     * Returns the table name for a table of your choice concatenated with the
     * prefix.
     *
     * Options for the $table argument:
     *
     * 'requests' - the pull requests table
     * 'users' - the users table
     *
     * @return String $table
     */
    public static function getTableName($table) {
        Config::getInstance();
        if ($table == 'requests') {
            return self::$config['db_prefix'].self::$config['db_requests_table'];
        } else if ($table == 'users') {
            return self::$config['db_prefix'].self::$config['db_users_table'];
        } else {
            return null;
        }
    }
    
    public function pullExists($pull) {
        $result = $this->query('SELECT `pull_id` FROM `'.
                self::getTableName('requests').'` WHERE `pull_id`='.
                intval($pull->number));
        
        if ($result->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function userExists($user) {
        // TODO Write this.
    }

    /**
     * Stores a pull request in the database.
     *
     * This class was deprecated before this was tested, it might not work.
     */
    public function storePullRequest($pull) {
        // TODO in the event that this class gets used in future, this needs testing.

        //Check if the pull request already exists in the database.
        if ($this->pullExists($pull)) {
            return true;
        }

        $statement = $this->prepare('INSERT INTO `'.self::getTableName('requests').'`
            (`pull_id`, `user`, `title`, `body`, `url`, `comments`, `created_at`)
            VALUES
            (:number, :user, :title, :body, :url, :comments, :created_at)');

        $statement->bindParam(':number', $pull->number, PDO::PARAM_INT);
        $statement->bindParam(':user', $pull->number, PDO::PARAM_STR);
        $statement->bindParam(':title', $pull->number, PDO::PARAM_STR);
        $statement->bindParam(':body', $pull->number, PDO::PARAM_STR);
        $statement->bindParam(':url', $pull->number, PDO::PARAM_STR);
        $statement->bindParam(':comments', $pull->number, PDO::PARAM_INT);
        $statement->bindParam(':created_at', $pull->number, PDO::PARAM_STR);

        return $statement->execute();
    }
}
?>
