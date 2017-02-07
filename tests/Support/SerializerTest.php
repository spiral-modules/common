<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Support;

use Spiral\Core\Component;
use Spiral\Support\Serializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    //To cover this weird trait as well
    use SerializerTrait;

    public function setUp()
    {
        $this->setSerializer(new Serializer());
    }

    public function testEmptyArray()
    {
        $this->assertSame('[]', $this->getSerializer()->serialize([]));
    }

    public function testArrayOfArray()
    {
        $this->assertEquals(preg_replace('/\s+/', '',
            '[
    \'hello\' => [
        \'name\' => 123
    ]
]'), preg_replace('/\s+/', '', $this->getSerializer()->serialize([
            'hello' => ['name' => 123]
        ])));
    }

    public function testArrayOfArray2()
    {
        $this->assertEquals(preg_replace('/\s+/', '',
            '[
    \'hello\' => [
        \'name\' => 123,
        \'sub\'  => magic
    ]
]'), preg_replace('/\s+/', '', $this->getSerializer()->serialize([
            'hello' => ['name' => 123, 'sub' => new Source(['magic'])]
        ])));
    }

    public function testClassNames()
    {
        $this->assertEquals(preg_replace('/\s+/', '',
            '[
    \'hello\' => [
        \'name\' => 123,
        \'sub\'  => \Spiral\Support\Serializer::class
    ]
]'), preg_replace('/\s+/', '', $this->getSerializer()->serialize([
            'hello' => ['name' => 123, 'sub' => Serializer::class]
        ])));
    }
}

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

trait SerializerTrait
{
    /**
     * @var Serializer|null
     */
    private $serializer = null;

    /**
     * Set custom serializer.
     *
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Associated serializer.
     *
     * @return Serializer
     */
    private function getSerializer(): Serializer
    {
        return $this->serializer ?? ($this->serializer = new Serializer());
    }
}