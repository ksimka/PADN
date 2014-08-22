<?php

namespace Padn;

/**
 * PADN parser
 * 
 * @package Padn
 */
class PadnParser
{
    /**
     * Parses a PADN string to an array
     *
     * @param string $padnString
     * @return array
     *      [['k':str=key, 'kc':str=key_class, 'km':str=key_meaning, 'c':str=class, 'm':str=meaning, '...':bool], ...]
     */
    public function parseArray($padnString)
    {
        $padnString = trim($padnString);

        // Validate bounds
        if (!$this->isArrayElement($padnString)) {
            throw new \UnexpectedValueException("Notation must be enclosed with []", 1);
        }

        $padnString = $this->removeBrackets($padnString);

        if (!$padnString) {
            throw new \UnexpectedValueException("Empty array", 3);
        }

        // Result structure
        $struct = [];

        // Explode to elements
        $matches = [];
        // Find all the elements delimited by a comma, but not inside square brackets
        preg_match_all('~((?:[^\[,]+)|(?:[^\[,]*\[[^\]]*\][^\[,]*)+)(?:,|$)~', $padnString, $matches);
        $elements = array_map('trim', $matches[1]);
        foreach ($elements as $element) {
            if ($this->isArrayElement($element)) {
                $parsedElement = $this->parseArray($element);
            } else {
                $parsedElement = $this->parseElement($element);
            }

            // Repetition
            if ('...' === $parsedElement) {
                if (empty($struct)) {
                    throw new \UnexpectedValueException(
                        "Invalid repetition (...) operator usage: no elements before",
                        4
                    );
                }

                $struct[count($struct) - 1]['...'] = true;

                continue;
            }

            if ($this->isArrayElement($parsedElement['c'])) {
                $parsedElement['a'] = $this->parseArray($parsedElement['c']);
                $parsedElement['c'] = '[]';
            }

            $struct[] = $parsedElement;
        }

        return $struct;
    }

    /**
     * Checks if string is valid array element in PADN
     *
     * @param string $element
     * @return bool
     */
    private function isArrayElement($element)
    {
        return substr($element, 0, 1) === '[' && substr($element, -1) === ']';
    }

    /**
     * Removes the first and the last chars from the string
     *
     * @param string $element
     * @return string
     */
    private function removeBrackets($element)
    {
        return substr($element, 1, strlen($element) - 2);
    }

    /**
     * Parses element to array structure
     *
     * Pseudo scheme: ('key'|class=meaning):class=meaning|...
     *
     * @param string $element
     * @return array|string ['k':str=key, 'kc':str=key_class, 'km':str=key_meaning, 'c':str=class, 'm':str=meaning]
     */
    private function parseElement($element)
    {
        // Repetition
        if ($element === '...') {
            return $element;
        }

        // Base RE's
        $array = "\[[^\[]+\]";
        $simpleClass = "int|flt|bool|str|res|null|clb";
        $class = "\w+|{$array}";
        // We add dummy parenthesis here so matches will always contain the following:
        // 1 - key, 2 - key class, 3 - key meaning, 4 - value class, 5 - value meaning
        $meaningRe = "()()()()=(\w+)"; // =id
        // int, int=id, str:int, str=name:int, str=name:int=id
        $noKeyRe = "()(?:({$simpleClass})(?:=(\w+))?\:)?({$class})(?:=(\w+))?";
        $keyRe = "'([^']*)'()()(?:\:({$class})(?:=(\w+))?)?"; // 'id', 'name':str, 'name':str=country_name

        $res = [$meaningRe, $noKeyRe, $keyRe];
        $matches = [];
        foreach ($res as $re) {
            $matches = [];
            if (preg_match("~^{$re}$~ui", $element, $matches)) {
                break;
            }
        }

        if (!$matches) {
            throw new \UnexpectedValueException("Element is invalid: '{$element}'", 2);
        }

        // Guarantee 5 values
        $matches = array_pad(array_slice($matches, 1), 5, '');
        return array_combine(['k', 'kc', 'km', 'c', 'm'], $matches);
    }
}
