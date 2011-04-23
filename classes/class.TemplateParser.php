<?php
/**
 * This class handles .tpl file parsing. It's a simple templating structure
 * to make separating the display and logic easy.
 *
 * @author Sam Rose
 */
class TemplateParser {

    /**
     * The start of a tag in the template.
     *
     * @var String $open_delimiter
     */
    private static $open_delimiter = '[+';
    /**
     * The end of a tag in the template.
     *
     * @var String $close_delimiter
     */
    private static $close_delimiter = '+]';

    /**
     * Returns an associative array of search => replace values for parsing.
     *
     * e.g.
     *
     * $placeholders = array('name' => 'Sam');
     *
     * Would parse the template and replace the "name" placeholder (plus
     * whatever delimiters you specify) with the word "Sam".
     *
     * Regular expressions are not supported.
     *
     * Requires that a pull request be supplied to it.
     *
     * @param stdObject $request
     * @return Array $placeholders
     */
    private static function getPlaceholders($request) {
        if (is_object($request)) {
            return array(
                'title' => $request->title,
                'user_login' => $request->user->login,
                'gravatar_id' => $request->user->gravatar_id,
                'created_at' => $request->created_at,
                'body' => $request->body,
                'link' => $request->html_url,
                'user_real_name' => isset($request->user->name) ? $request->user->name : $request->user->login,
                'date_today' => strftime('%D'),
                'state' => $request->state,
                'number' => $request->number
            );
        } else {
            return array();
        }
    }

    /**
     * Parses a template file replacing placeholders with values as specified
     * in the getPlaceholders() method of this class.
     *
     * @param String $template
     * @param stdObject $request
     * @param Array $placeholders
     */
    public static function parse($template, $request = null, $placeholders = null) {
        $config = Config::getInstance();
        $placeholders = is_array($placeholders) ? array_merge($placeholders, self::getPlaceholders($request)) :
                self::getPlaceholders($request);

        $template = file_get_contents($config->getValue('templates_dir') . '/' . $template);
        foreach ($placeholders as $search=>$replace) {
            $template = str_replace(
                    self::$open_delimiter.$search.self::$close_delimiter,
                    $replace,
                    $template
            );
        }
        
        return $template;
    }
}
