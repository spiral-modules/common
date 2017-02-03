<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\Body;

use Spiral\Reactor\Exceptions\MultilineException;
use Spiral\Reactor\Prototypes\Declaration;

/**
 * Represents set of lines (function source, docComment).
 */
class Source extends Declaration
{
    /**
     * @var array
     */
    private $lines = [];

    /**
     * @param array $lines
     */
    public function __construct(array $lines = [])
    {
        $this->lines = $lines;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->lines);
    }

    /**
     * @param array $lines
     *
     * @return self|$this
     */
    public function setLines(array $lines): Source
    {
        $this->lines = $lines;

        return $this;
    }

    /**
     * @param array $lines
     *
     * @return self
     */
    public function addLines(array $lines): Source
    {
        $this->lines = array_merge($this->lines, $lines);

        return $this;
    }

    /**
     * @param string $line
     *
     * @return self
     * @throws MultilineException
     */
    public function addLine(string $line): Source
    {
        if (strpos($line, "\n") !== false) {
            throw new MultilineException(
                "New line character is forbidden in addLine method argument."
            );
        }

        $this->lines[] = $line;

        return $this;
    }

    /**
     * @param string $string
     * @param bool   $cutIndents Function Strings::normalizeIndents will be applied.
     *
     * @return self
     */
    public function setString(string $string, bool $cutIndents = false): Source
    {
        return $this->setLines($this->fetchLines($string, $cutIndents));
    }

    /**
     * @param string $string
     * @param bool   $cutIndents Function Strings::normalizeIndents will be applied.
     *
     * @return self
     */
    public function addString(string $string, bool $cutIndents = false): Source
    {
        return $this->addLines($this->fetchLines($string, $cutIndents));
    }

    /**
     * @return array
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * {@inheritdoc}
     */
    public function render(int $indentLevel = 0): string
    {
        $lines = $this->lines;
        array_walk($lines, function (&$line) use ($indentLevel) {
            $line = $this->addIndent($line, $indentLevel);
        });

        return join("\n", $lines);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render(0);
    }

    /**
     * Converts input string into set of lines.
     *
     * @param string $string
     * @param bool   $cutIndents
     *
     * @return array
     */
    public function fetchLines(string $string, bool $cutIndents): array
    {
        if ($cutIndents) {
            $string = $this->normalizeEndings($string, false);
        }

        $lines = explode("\n", $this->normalizeEndings($string, false));

        //Pre-processing
        return array_map([$this, 'prepareLine'], $lines);
    }

    /**
     * Create version of source cut from specific string location.
     *
     * @param string $string
     * @param bool   $cutIndents Function Strings::normalizeIndents will be applied.
     *
     * @return Source
     */
    public static function fromString(string $string, bool $cutIndents = false): Source
    {
        $source = new self();

        return $source->setString($string, $cutIndents);
    }

    /**
     * Applied to every string before adding it to lines.
     *
     * @param string $line
     *
     * @return string
     */
    protected function prepareLine(string $line): string
    {
        return $line;
    }
}