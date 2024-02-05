<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\DayOfWeek;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for DayOfWeek.
 *
 * Maps to a database small integer column.
 */
final class DayOfWeekType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getSmallIntTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DayOfWeek) {
            return $value->value;
        }

        throw InvalidType::new(
            $value,
            static::class,
            [DayOfWeek::class, 'null'],
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DayOfWeek
    {
        if ($value === null) {
            return null;
        }

        return DayOfWeek::from((int) $value);
    }

    public function getBindingType(): ParameterType
    {
        return ParameterType::INTEGER;
    }
}
