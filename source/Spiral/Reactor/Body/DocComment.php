<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\Body;

use Spiral\Reactor\ReplaceableInterface;

/**
 * Wraps docBlock comment (by representing it as string lines).
 */
class DocComment extends Source implements ReplaceableInterface
{
    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function replace($search, $replace): DocComment
    {
        $lines = $this->getLines();

        array_walk($lines, function (&$line) use ($search, $replace) {
            $line = str_replace($search, $replace, $line);
        });

        return $this->setLines($lines);
    }

    /**
     * {@inheritdoc}
     */
    public function render(int $indentLevel = 0): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        $result = $this->addIndent("/**\n", $indentLevel);
        foreach ($this->getLines() as $line) {
            $result .= $this->addIndent(" * {$line}\n", $indentLevel);
        }

        $result .= $this->addIndent(" */", $indentLevel);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareLine(string $line): string
    {
        $line = trim($line);

        if ($line === '/*' || $line === '/**' || $line === '*/') {
            return '';
        }

        return parent::prepareLine(preg_replace('/^(\s)*(\*)+/si', ' ', $line));
    }
}