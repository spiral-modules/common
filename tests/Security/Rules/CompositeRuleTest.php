<?php
/**
 * Spiral, Core Components
 *
 * @author    Dmitry Mironov <dmitry.mironov@spiralscout.com>
 */

namespace Spiral\Tests\Security\Rules;

use Spiral\Security\ActorInterface;
use Spiral\Security\RuleInterface;
use Spiral\Security\RulesInterface;
use Spiral\Tests\Security\Rules\Fixtures\AllCompositeRule;
use Spiral\Tests\Security\Rules\Fixtures\OneCompositeRule;


/**
 * Class CompositeRuleTest
 *
 * @package Spiral\Tests\Security\Rules
 */
class CompositeRuleTest extends \PHPUnit_Framework_TestCase
{
    const OPERATION = 'test';
    const CONTEXT   = [];

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ActorInterface $callable
     */
    private $actor;

    public function setUp()
    {
        $this->actor = $this->createMock(ActorInterface::class);
    }

    /**
     * @param $expected
     * @param $compositeRuleClass
     * @param $rules
     *
     * @dataProvider allowsProvider
     */
    public function testAllow($expected, $compositeRuleClass, $rules)
    {
        $repository = $this->createRepository($rules);

        /** @var RuleInterface $rule */
        $rule = new $compositeRuleClass($repository);
        $this->assertEquals($expected,
            $rule->allows($this->actor, static::OPERATION, static::CONTEXT));
    }

    public function allowsProvider()
    {
        $allowRule = $this->allowRule();
        $forbidRule = $this->forbidRule();

        return [
            [true, AllCompositeRule::class, [$allowRule, $allowRule, $allowRule]],
            [false, AllCompositeRule::class, [$allowRule, $allowRule, $forbidRule]],
            [true, OneCompositeRule::class, [$allowRule, $forbidRule, $forbidRule]],
            [true, OneCompositeRule::class, [$allowRule, $allowRule, $allowRule]],
            [false, OneCompositeRule::class, [$forbidRule, $forbidRule, $forbidRule]],
        ];
    }

    /**
     * @param array $rules
     *
     * @return RulesInterface
     */
    private function createRepository(array $rules): RulesInterface
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|RulesInterface $repository */
        $repository = $this->createMock(RulesInterface::class);

        $repository->method('get')
            ->will(new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($rules));

        return $repository;
    }

    /**
     * @return RuleInterface
     */
    private function allowRule(): RuleInterface
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|RuleInterface $rule */
        $rule = $this->createMock(RuleInterface::class);
        $rule->method('allows')->willReturn(true);

        return $rule;
    }

    /**
     * @return RuleInterface
     */
    private function forbidRule(): RuleInterface
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|RuleInterface $rule */
        $rule = $this->createMock(RuleInterface::class);
        $rule->method('allows')->willReturn(false);

        return $rule;
    }
}