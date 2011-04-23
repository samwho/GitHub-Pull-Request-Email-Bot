<?php
/**
 * A simple class for sending emails.
 *
 * @author Sam Rose
 */
class Email {
    /**
     * Sends an email.
     *
     * @param String $content
     */
    public static function send($content, $subject=null) {
        $config = Config::getInstance();

        // properly handle an array of email addresses
        if (is_array($config['email_to'])) {
            foreach($config['email_to'] as $to) {
                self::send_mail($to, $content, $subject);
            }
        } else {
            self::send_mail($config['email_to'], $content, $subject);
        }
    }

    private static function send_mail($to, $content, $subject=null) {
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
        
        if (!isset($subject)) {
            $subject = $config['email_subject'];
        }

        if (is_writeable($config['debug_dir']) && $config['debug'] == true) {
            $message = $additional_headers . "To: " . $to . "\r\nSubject: " . $subject . "\r\nContent: " . $content;
            file_put_contents($config['debug_dir'] . '/last_email_sent.txt', $message);
        }

        return mail($to, $subject, $content, $additional_headers);
    }
}
