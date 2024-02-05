<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\Period;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

/**
 * Doctrine type for Period.
 *
 * Maps its string representation to a VARCHAR column.
 */
final class PeriodType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if (!isset($column['length'])) {
            $column['length'] = 64;
        }

        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Period) {
            return (string) $value;
        }

        throw InvalidType::new(
            $value,
            static::class,
            [Period::class, 'null'],
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Period
    {
        if ($value === null) {
            return null;
        }

        return Period::parse((string) $value);
    }
}
