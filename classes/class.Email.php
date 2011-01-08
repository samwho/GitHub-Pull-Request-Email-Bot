<?php
require_once 'class.Config.php';

/**
 * A simple class for sending emails.
 *
 * @author Sam Rose
 */
class Email {
    /**
     *
     *
     * @param String $content
     */
    public static function send($content) {
        $config = Config::getInstance();

        // properly handle an array of email addresses
        if (is_array($config['email_to'])) {
            foreach($config['email_to'] as $to) {
                self::send_mail($to, $content);
            }
        } else {
            self::send_mail($config['email_to'], $content);
        }
    }

    private static function send_mail($to, $content) {
        $config = Config::getInstance();

        $additional_headers = '';
        if ($config['reply_to'] != '') {
            $additional_headers .= 'Reply-To: '.$config['reply_to']."\r\n";
        }
        if ($config['email_from'] != '') {
            $additional_headers .= 'From: '.$config['email_from']."\r\n";
        }
        if ($config['email_use_html']) {
            $additional_headers .= 'Content-Type: text/html'."\r\n";
        }
        
        return mail($to, $config['email_subject'], $content, $additional_headers);
    }
}
?>
