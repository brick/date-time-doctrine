<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\DayOfWeek;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use Override;
use ValueError;

/**
 * Doctrine type for DayOfWeek.
 *
 * Maps to a database small integer column.
 */
final class DayOfWeekType extends Type
{
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getSmallIntTypeDeclarationSQL($column);
    }

    #[Override]
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
            self::class,
            [DayOfWeek::class, 'null'],
        );
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DayOfWeek
    {
        if ($value === null) {
            return null;
        }

        try {
            return DayOfWeek::from((int) $value);
        } catch (ValueError $e) {
            throw ValueNotConvertible::new(
                $value,
                DayOfWeek::class,
                $e->getMessage(),
                $e,
            );
        }
    }

    #[Override]
    public function getBindingType(): ParameterType
    {
        return ParameterType::INTEGER;
    }
}
