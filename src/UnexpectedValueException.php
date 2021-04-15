<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine;

/**
 * Thrown when a value does not match the expected type.
 */
final class UnexpectedValueException extends \UnexpectedValueException
{
    /**
     * @param mixed $actualValue
     */
    public function __construct(string $expectedType, $actualValue)
    {
        $type = is_object($actualValue) ? get_class($actualValue) : gettype($actualValue);
        $message = sprintf('Expected %s, got %s.', $expectedType, $type);

        parent::__construct($message);
    }
}
