<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Security\Rules;

use Spiral\Security\ActorInterface;
use Spiral\Security\RuleInterface;
use Spiral\Security\RulesInterface;

/**
 * Provides ability to evaluate multiple sub rules using boolean joiner.
 *
 * Example:
 *
 * class AuthorOrModeratorRule extends BooleanRule
 * {
 *      const BEHAVIOUR = self::AT_LEAST_ONE;
 *      const RULES     = [AuthorRule::class, ModeratorRule::class];
 * }
 */
abstract class CompositeRule implements RuleInterface
{
    const ALL          = 'ALL';
    const AT_LEAST_ONE = 'ONE';

    /**
     * How to process results on sub rules.
     */
    const BEHAVIOUR = self::ALL;

    /**
     * List of rules to be composited.
     */
    const RULES = [];

    /**
     * Rules repository.
     *
     * @var RulesInterface
     */
    private $repository = null;

    /**
     * @param RulesInterface $repository
     */
    public function __construct(RulesInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function allows(ActorInterface $actor, string $permission, array $context): bool
    {
        $allowed = 0;
        foreach (static::RULES as $rule) {
            $rule = $this->repository->get($rule);

            if ($rule->allows($actor, $permission, $context)) {
                if (static::BEHAVIOUR == self::AT_LEAST_ONE) {
                    return true;
                }

                $allowed++;
            } elseif (static::BEHAVIOUR == self::ALL) {
                return false;
            }
        }

        return $allowed === count(static::RULES);
    }
}