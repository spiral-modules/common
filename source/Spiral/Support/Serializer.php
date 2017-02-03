<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Support;

use Spiral\Reactor\DeclarationInterface;
use Spiral\Support\Exceptions\SerializeException;

/**
 * Provides very simple api for serializing values. Attention, this is helper class, it's not
 * intended for processing user input.
 */
class Serializer
{
    /**
     * Fixed 4 spaces indent.
     */
    const INDENT = DeclarationInterface::INDENT;

    /**
     * Serialize array.
     *
     * @param mixed $array
     *
     * @return string
     */
    public function serialize($array): string
    {
        if (is_array($array)) {
            return $this->packArray($array);
        }

        return $this->packValue($array);
    }

    /**
     * @param array $array
     * @param int   $level
     *
     * @return string
     */
    protected function packArray(array $array, int $level = 0): string
    {
        if ($array === []) {
            return '[]';
        }

        //Delimiters between rows and sub-arrays.
        $subIndent = "\n" . str_repeat(self::INDENT, $level + 2);
        $keyIndent = "\n" . str_repeat(self::INDENT, $level + 1);

        //No keys for associated array
        $associated = array_diff_key($array, array_keys(array_keys($array)));

        $result = [];
        $innerIndent = 0;
        if (!empty($associated)) {
            foreach ($array as $key => $value) {
                //Based on biggest key length
                $innerIndent = max(strlen(var_export($key, true)), $innerIndent);
            }
        }

        foreach ($array as $key => $value) {
            $prefix = '';
            if (!empty($associated)) {
                //Key prefix
                $prefix = str_pad(
                        var_export($key, true),
                        $innerIndent, ' ',
                        STR_PAD_RIGHT
                    ) . " => ";
            }

            if (!is_array($value)) {
                $result[] = $prefix . $this->packValue($value);
                continue;
            }

            if ($value === []) {
                $result[] = $prefix . "[]";
                continue;
            }

            $subArray = $this->packArray($value, $level + 1);
            $result[] = $prefix . "[{$subIndent}" . $subArray . "{$keyIndent}]";
        }

        if ($level !== 0) {
            return $result ? join(",{$keyIndent}", $result) : "";
        }

        return "[{$keyIndent}" . join(",{$keyIndent}", $result) . "\n]";
    }

    /**
     * Pack array key value into string.
     *
     * @param mixed $value
     *
     * @return string
     * @throws SerializeException
     */
    protected function packValue($value): string
    {
        if ($value instanceof DeclarationInterface) {
            //No indentation here
            return $value->render();
        }

        if (is_null($value)) {
            return "null";
        }

        if (is_bool($value)) {
            return ($value ? "true" : "false");
        }

        if (is_object($value) && method_exists($value, '__set_state')) {
            return var_export($value, true);
        }

        if (!is_string($value) && !is_numeric($value)) {
            print_r($value);
            throw new SerializeException("Unable to pack non scalar value");
        }

        if (is_string($value) && class_exists($value)) {
            $reflection = new \ReflectionClass($value);

            return '\\' . $reflection->getName() . '::class';
        }

        return var_export($value, true);
    }
}