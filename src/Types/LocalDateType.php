<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\LocalDate;
use Brick\DateTime\Doctrine\UnexpectedValueException;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for LocalDate.
 *
 * Maps to a database DATE type if supported.
 */
final class LocalDateType extends Type
{
    public function getName()
    {
        return 'LocalDate';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDate) {
            return (string) $value;
        }

        throw new UnexpectedValueException(LocalDate::class, $value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return LocalDate::parse((string) $value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
