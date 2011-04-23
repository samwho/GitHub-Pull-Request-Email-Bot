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
        if (is_array($config->getValue('email_to'))) {
            foreach($config->getValue('email_to') as $to) {
                self::send_mail($to, $content, $subject);
            }
        } else {
            self::send_mail($config->getValue('email_to'), $content, $subject);
        }
    }

    private static function send_mail($to, $content, $subject=null) {
        $config = Config::getInstance();

        $additional_headers = '';
        if ($config->getValue('reply_to') != '') {
            $additional_headers .= 'Reply-To: '.$config->getValue('reply_to')."\r\n";
        }
        if ($config->getValue('email_from') != '') {
            $additional_headers .= 'From: '.$config->getValue('email_from')."\r\n";
        }
        if ($config->getValue('email_use_html')) {
            $additional_headers .= 'Content-Type: text/html'."\r\n";
        }
        
        if (!isset($subject)) {
            $subject = $config->getValue('email_subject');
        }

        if (is_writeable($config->getValue('debug_dir')) && $config->getValue('debug') == true) {
            $message = $additional_headers . "To: " . $to . "\r\nSubject: " . $subject . "\r\nContent: " . $content;
            file_put_contents($config->getValue('debug_dir') . '/last_email_sent.txt', $message);
        }

        return mail($to, $subject, $content, $additional_headers);
    }
}
