<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor;

/**
 * To be rendered with some indent.
 */
interface DeclarationInterface
{
    /**
     * Indent is always 4 spaces.
     */
    const INDENT = "    ";

    /**
     * Must render it's own content into string using given indent level.
     *
     * @param int $indentLevel
     *
     * @return string
     */
    public function render(int $indentLevel = 0): string;
}