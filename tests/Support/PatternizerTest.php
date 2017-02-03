<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Support;

use Spiral\Support\Patternizer;

class PatternizerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsPattern()
    {
        $patternizer = new Patternizer();

        $this->assertFalse($patternizer->isPattern('abc'));
        $this->assertTrue($patternizer->isPattern('ab*'));
        $this->assertTrue($patternizer->isPattern('ab(d|e)'));
    }

    /**
     * @dataProvider patternProvider
     *
     * @param array $string
     * @param array $pattern
     * @param bool  $result
     */
    public function testMatch($string, $pattern, $result)
    {
        $patternizer = new Patternizer();
        $this->assertEquals($result, $patternizer->matches($string, $pattern));
    }

    /**
     * @return array
     */
    public function patternProvider()
    {
        return [
            ['string', 'string', true],
            ['string', 'st*', true],
            ['abc', 'dce', false],
            ['abc', 'a(bc|de)', true],
            ['ade', 'a(bc|de)', true],
            ['string', '*ring', true],
            ['ring', '*ring', false],
            ['strings', '*ri(ng|ngs)', true],
        ];
    }
}