<?php
require_once 'class.PullRequestDatabase.php';
require_once 'class.Config.php';
require_once 'class.WebPage.php';

/**
 * This class installs the necessary database tables for this code.
 *
 * @author Sam Rose
 * @deprecated deprecated since before release.
 */
class PullRequestBotInstall {
    /**
     * A PDO connection to the database accessed with the credentials in
     * config.inc.php
     *
     * @var PDO $db
     */
    private $db;

    /**
     * Constructor for the Install class. Sets the config array if needed for
     * testing purposes.
     *
     * @param Array $config
     */
    public function __construct($config = null) {
        //Try to connect to the database
        try {
            $this->db = new PullRequestDatabase($config);
        }
        //Catch PDOException: represents the inability to make db connection
        catch (PDOException $e) {
            //print error message
            echo $e->getMessage();
            //stop script
            exit;
        }
    }

    /**
     * This function checks if the tables to install to exists or not. Returns
     * true if the tables exists, false if it does not.
     *
     * T
     *
     * @return bool $installed
     */
    public function checkInstalled() {
        $query = $this->db->query(sprintf('SHOW TABLES LIKE "%s"',
                Config::getTableName('requests')));
        $request_table_result = $query->rowCount();

        $query = $this->db->query(sprintf('SHOW TABLES LIKE "%s"',
                Config::getTableName('users')));
        $user_table_result = $query->rowCount();

        if ($request_table_result > 0 || $user_table_result > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns an associative array of PDOStatement objects. The decision to
     * make them an array came about because PDO can't execute more than one
     * query in a statement (it seems).
     *
     * All of the queries in the array that gets returned will be executed by
     * the installer. Their key value in the associative array will be printed
     * out along with their success or failure.
     *
     * @return PDOStatementArray $queries
     */
    private function installQueries() {
        $statement = array(
        'create_pull_table' =>
            sprintf(
            'CREATE TABLE %s (
                `pull_id` INT NOT NULL,
                `user` TEXT NOT NULL,
                `title` TEXT NOT NULL,
                `body` TEXT NOT NULL,
                `url` TEXT NOT NULL,
                `comments` INT DEFAULT 0,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`pull_id`)
            ) ENGINE=MyISAM;',
            Config::getTableName('requests')),

        'create_user_table' =>
            sprintf('
            CREATE TABLE %s (
                `user_login` TEXT NOT NULL,
                `user_real_name` TEXT,
                `gravatar_id` TEXT,
                `blog` TEXT,
                PRIMARY KEY (`user_login`(64))
            ) ENGINE=MyISAM;',
            Config::getTableName('users'))
        );

        $prepared_statements = array();
        foreach ($statement as $key=>$value) {
            $prepared_statements[$key] = $this->db->prepare($value);
        }

        return $prepared_statements;
    }

    /**
     * This is the function that does the entire install process.
     *
     * First it checks to see that the tables involved in the install process
     * are already in use and aborts if they are.
     *
     * Then the tables that need to be created get created. The process is
     * implemented transactionally even though MySQL issues an implicit COMMIT
     * after CREATE TABLE queries. It doesn't harm to have it in there just in
     * case.
     */
    public function install() {
        //Check if the bot is already installed
        if ($this->checkInstalled()) {
            echo 'It seems like the request bot is already installed. If this
                is not the case then you have tables in the database you
                specified that have the same name as the tables that the
                Pull Request Bot is going to use in its installation. Please
                either delete them or choose different table names in the
                config.inc.php file. Thank you.';
        } else {
            $install_queries = $this->installQueries();
            
            //bool variable to check whether or not to commit the transaction.
            $commit = true;

            try {
                //start transactional query
                $this->db->beginTransaction();

                //execute queries one by one
                foreach ($install_queries as $key=>$install_query) {
                    if ($install_query->execute()) {
                        echo $key.': executed successfully.<br />';
                    } else {
                        $commit = false;
                        echo $key.': failed.<br />';
                        echo '<pre>';
                        print_r($install_query->errorInfo());
                        echo '</pre>';
                        break;
                    }
                }

                //Check to see that we can connect to external URLs
                if (WebPage::checkWorks()) {
                    echo 'Check to connect to external websites passed.<br />';
                } else {
                    //Don't commit.
                    $commit = false;
                    echo 'Check to connect to external websites failed.<br />';
                }

                if (function_exists('json_decode')) {
                    echo 'JSON support enabled.<br />';
                } else {
                    $commit = false;
                    echo 'No JSON support.<br />';
                }

                //commit the transaction and display success message.
                if($commit == true) {
                    $this->db->commit();
                    echo 'Pull Request Bot has been successfully installed.
                        Please use the run.php on a Cron job to use it.';
                } else {
                    $this->db->rollBack();
                    echo 'Install failed. Please see above messages for details.';
                }
            }
            catch (PDOException $e) {
                $this->db->rollBack();
                echo $e->getMessage();
            }
        }
    }

}
?>
