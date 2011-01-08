<?php
require_once 'class.Config.php';

/**
 * A simple class for sending emails.
 *
 * @author Sam Rose
 */
class Email {
    public static function send($content) {
        $config = Config::getInstance();

        if (is_array($config['email_to'])) {
            foreach($config['email_to'] as $to) {
                self::send_mail($to, $content);
            }
        } else {
            if (self::send_mail($config['email_to'], $content)) {
                echo "woot";
            }
            else {
                echo "not woot";
            }
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
