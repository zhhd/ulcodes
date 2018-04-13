<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/10
 * Time: 18:06
 */

namespace ulcodes\Core\Drive\Doc;


class DocParse
{
    /**
     * The string that we want to parse
     */
    private $string;

    public function __construct($string)
    {
        $this->string = $string;

    }

    /**
     * @return array
     */
    public function parse()
    {
        if (!preg_match('#^/\*\*(.*)\*/#s', $this->string, $comment))
            return [];
        $comment = trim($comment[1]);
        if (!preg_match_all('#^\s*\*(.*)#m', $comment, $lines))
            return [];
        $lines = $lines[1];

        $parserValues = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $params = preg_split('/\s+/is', $line);
            if ($params[0] == '@param') {
                $parserValue = [];

                // length
                if (preg_match('/length\((\d)\,(\d)\)/i', $params[1], $length)) {
                    $params[1]          = str_replace($length[0], 'length', $params[1]);
                    $parserValue['min'] = $length[1];
                    $parserValue['max'] = $length[2];
                }
                $types                = explode('|', $params[1]);
                $parserValue['name']  = str_replace('$', '', $params[2]);
                $parserValue['types'] = $types;
                $parserValue['desc']  = isset($params[3]) ? $params[3] : '';
                $parserValues[]       = $parserValue;
            }
        }

        return $parserValues;
    }
}