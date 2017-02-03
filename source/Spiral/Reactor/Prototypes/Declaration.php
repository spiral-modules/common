<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\Prototypes;

use Spiral\Core\Component;
use Spiral\Reactor\DeclarationInterface;

/**
 * Generic element declaration.
 */
abstract class Declaration extends Component implements DeclarationInterface
{
    /**
     * Access level constants.
     */
    const ACCESS_PUBLIC    = 'public';
    const ACCESS_PROTECTED = 'protected';
    const ACCESS_PRIVATE   = 'private';

    /**
     * @param string $string
     * @param int    $indent
     *
     * @return string
     */
    protected function addIndent(string $string, int $indent = 0): string
    {
        return str_repeat(self::INDENT, max($indent, 0)) . $string;
    }

    /**
     * Normalize string endings to avoid EOL problem. Replace \n\r and multiply new lines with
     * single \n.
     *
     * @param string $string       String to be normalized.
     * @param bool   $joinMultiple Join multiple new lines into one.
     *
     * @return string
     */
    protected function normalizeEndings(string $string, bool $joinMultiple = true): string
    {
        if (!$joinMultiple) {
            return str_replace("\r\n", "\n", $string);
        }

        return preg_replace('/[\n\r]+/', "\n", $string);
    }
}