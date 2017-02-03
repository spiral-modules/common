<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Support;

/**
 * Provides ability to process permissions as star based patterns. This is helper class which is
 * used in Tokenizer and Strempler components.
 *
 * Example:
 * post.*
 * post.(save|delete)
 */
class Patternizer
{
    /**
     * @param string $string
     *
     * @return bool
     */
    public function isPattern(string $string): bool
    {
        return strpos($string, '*') !== false || strpos($string, '|') !== false;
    }

    /**
     * Checks if string matches given pattent.
     *
     * @param string $string
     * @param string $pattern
     *
     * @return bool
     */
    public function matches(string $string, string $pattern): bool
    {
        if ($string === $pattern) {
            return true;
        }

        if (!$this->isPattern($pattern)) {
            return false;
        }

        return (bool)preg_match($this->getRegex($pattern), $string);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    private function getRegex(string $pattern): string
    {
        $regex = str_replace('*', '[a-z0-9_\-]+', addcslashes($pattern, '.-'));

        return "#^{$regex}$#i";
    }
}