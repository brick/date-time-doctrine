<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\Instant;
use Brick\DateTime\Doctrine\UnexpectedValueException;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for Instant.
 *
 * Maps to a database integer column, storing the epoch second, and silently discarding the nanos.
 */
final class InstantType extends Type
{
    public function getName()
    {
        return 'Instant';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Instant) {
            return $value->getEpochSecond();
        }

        throw new UnexpectedValueException(Instant::class, $value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return Instant::of((int) $value);
    }

    public function getBindingType()
    {
        return \PDO::PARAM_INT;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
