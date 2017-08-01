<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Debug\Dumper\Style;

/**
 * One of the oldest spiral parts, used to dump variables content in user friendly way.
 */
class Dumper implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Options for dump() function to specify output.
     */
    const OUTPUT_ECHO   = 0;
    const OUTPUT_RETURN = 1;
    const OUTPUT_LOG    = 2;

    /**
     * Deepest level to be dumped.
     *
     * @var int
     */
    private $maxLevel = 10;

    /**
     * @invisible
     *
     * @var Style
     */
    private $style = null;

    /**
     * @param int             $maxLevel
     * @param Style           $style Light styler to be used by default.
     * @param LoggerInterface $logger
     */
    public function __construct(
        int $maxLevel = 10,
        Style $style = null,
        LoggerInterface $logger = null
    ) {
        $this->maxLevel = $maxLevel;
        $this->style = $style ?? new Style();

        if (!empty($logger)) {
            $this->setLogger($logger);
        }
    }

    /**
     * Set dump styler.
     *
     * @param Style $style
     *
     * @return self
     */
    public function setStyle(Style $style): Dumper
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Dump specified value. Dumper will automatically detect CLI mode in OUTPUT_ECHO mode.
     *
     * @param mixed $value
     * @param int   $output
     *
     * @return string
     */
    public function dump($value, int $output = self::OUTPUT_ECHO): string
    {
        switch ($output) {
            case self::OUTPUT_ECHO:
                if (php_sapi_name() == 'cli') {
                    print_r($value);
                }

                echo $this->style->wrapContainer($this->dumpValue($value, '', 0));
                break;

            case self::OUTPUT_LOG:
                if (!empty($this->logger)) {
                    $this->logger->debug($this->dump($value, self::OUTPUT_RETURN));
                }
                break;

            case self::OUTPUT_RETURN:
                return $this->style->wrapContainer($this->dumpValue($value, '', 0));
        }

        //Nothing to return
        return '';
    }

    /**
     * Variable dumper. This is the oldest spiral function originally written in 2007. :).
     *
     * @param mixed  $value
     * @param string $name       Variable name, internal.
     * @param int    $level      Dumping level, internal.
     * @param bool   $hideHeader Hide array/object header, internal.
     *
     * @return string
     */
    private function dumpValue(
        $value,
        string $name = '',
        int $level = 0,
        bool $hideHeader = false
    ): string {
        //Any dump starts with initial indent (level based)
        $indent = $this->style->indent($level);

        if (!$hideHeader && !empty($name)) {
            //Showing element name (if any provided)
            $header = $indent . $this->style->apply($name, 'name');

            //Showing equal sing
            $header .= $this->style->apply(' = ', 'syntax', '=');
        } else {
            $header = $indent;
        }

        if ($level > $this->maxLevel) {
            //Dumper is not reference based, we can't dump too deep values
            return $indent . $this->style->apply('-too deep-', 'maxLevel') . "\n";
        }

        $type = strtolower(gettype($value));

        if ($type == 'array') {
            return $header . $this->dumpArray($value, $level, $hideHeader);
        }

        if ($type == 'object') {
            return $header . $this->dumpObject($value, $level, $hideHeader);
        }

        if ($type == 'resource') {
            //No need to dump resource value
            $element = get_resource_type($value) . ' resource ';

            return $header . $this->style->apply($element, 'type', 'resource') . "\n";
        }

        //Value length
        $length = strlen($value);

        //Including type size
        $header .= $this->style->apply("{$type}({$length})", 'type', $type);

        $element = null;
        switch ($type) {
            case 'string':
                $element = htmlspecialchars($value);
                break;

            case 'boolean':
                $element = ($value ? 'true' : 'false');
                break;

            default:
                if ($value !== null) {
                    //Not showing null value, type is enough
                    $element = var_export($value, true);
                }
        }

        //Including value
        return $header . ' ' . $this->style->apply($element, 'value', $type) . "\n";
    }

    /**
     * @param array $array
     * @param int   $level
     * @param bool  $hideHeader
     *
     * @return string
     */
    private function dumpArray(array $array, int $level, bool $hideHeader = false): string
    {
        $indent = $this->style->indent($level);

        if (!$hideHeader) {
            $count = count($array);

            //Array size and scope
            $output = $this->style->apply("array({$count})", 'type', 'array') . "\n";
            $output .= $indent . $this->style->apply('[', 'syntax', '[') . "\n";
        } else {
            $output = '';
        }

        foreach ($array as $key => $value) {
            if (!is_numeric($key)) {
                if (is_string($key)) {
                    $key = htmlspecialchars($key);
                }

                $key = "'{$key}'";
            }

            $output .= $this->dumpValue($value, "[{$key}]", $level + 1);
        }

        if (!$hideHeader) {
            //Closing array scope
            $output .= $indent . $this->style->apply(']', 'syntax', ']') . "\n";
        }

        return $output;
    }

    /**
     * @param object $object
     * @param int    $level
     * @param bool   $hideHeader
     * @param string $class
     *
     * @return string
     */
    private function dumpObject(
        $object,
        int $level,
        bool $hideHeader = false,
        string $class = ''
    ): string {
        $indent = $this->style->indent($level);

        if (!$hideHeader) {
            $type = ($class ?: get_class($object)) . ' object ';

            $header = $this->style->apply($type, 'type', 'object') . "\n";
            $header .= $indent . $this->style->apply('(', 'syntax', '(') . "\n";
        } else {
            $header = '';
        }

        //Let's use method specifically created for dumping
        if (method_exists($object, '__debugInfo') || $object instanceof \Closure) {
            if ($object instanceof \Closure) {
                $debugInfo = $this->describeClosure($object);
            } else {
                $debugInfo = $object->__debugInfo();
            }

            if (is_array($debugInfo)) {
                //Pretty view
                $debugInfo = (object)$debugInfo;
            }

            if (is_object($debugInfo)) {
                //We are not including syntax elements here
                return $this->dumpObject($debugInfo, $level, false, get_class($object));
            }

            return $header
                . $this->dumpValue($debugInfo, '', $level + (is_scalar($object)), true)
                . $indent . $this->style->apply(')', 'syntax', ')') . "\n";
        }

        $refection = new \ReflectionObject($object);

        $output = '';
        foreach ($refection->getProperties() as $property) {
            $output .= $this->dumpProperty($object, $property, $level);
        }

        //Header, content, footer
        return $header . $output . $indent . $this->style->apply(')', 'syntax', ')') . "\n";
    }

    /**
     * @param object              $object
     * @param \ReflectionProperty $property
     * @param int                 $level
     *
     * @return string
     */
    private function dumpProperty($object, \ReflectionProperty $property, int $level): string
    {
        if ($property->isStatic()) {
            return '';
        }

        if (
            !($object instanceof \stdClass)
            && strpos($property->getDocComment(), '@invisible') !== false
        ) {
            //Memory loop while reading doc comment for stdClass variables?
            //Report a PHP bug about treating comment INSIDE property declaration as doc comment.
            return '';
        }

        //Property access level
        $access = $this->getAccess($property);

        //To read private and protected properties
        $property->setAccessible(true);

        if ($object instanceof \stdClass) {
            $name = $this->style->apply($property->getName(), 'dynamic');
        } else {
            //Property name includes access level
            $name = $property->getName() . $this->style->apply(':' . $access, 'access', $access);
        }

        return $this->dumpValue($property->getValue($object), $name, $level + 1);
    }

    /**
     * Fetch information about the closure.
     *
     * @param \Closure $closure
     *
     * @return array
     */
    private function describeClosure(\Closure $closure): array
    {
        $reflection = new \ReflectionFunction($closure);

        return [
            'name' => $reflection->getName() . " (lines {$reflection->getStartLine()}:{$reflection->getEndLine()})",
            'file' => $reflection->getFileName(),
            'this' => $reflection->getClosureThis()
        ];
    }

    /**
     * Property access level label.
     *
     * @param \ReflectionProperty $property
     *
     * @return string
     */
    private function getAccess(\ReflectionProperty $property): string
    {
        if ($property->isPrivate()) {
            return 'private';
        } elseif ($property->isProtected()) {
            return 'protected';
        }

        return 'public';
    }
}
