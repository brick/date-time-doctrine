<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\DayOfWeek;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for DayOfWeek.
 *
 * Maps to a database small integer column.
 */
final class DayOfWeekType extends Type
{
    public function getName(): string
    {
        return 'DayOfWeek';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getSmallIntTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DayOfWeek) {
            return $value->value;
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            [DayOfWeek::class, 'null']
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DayOfWeek
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return DayOfWeek::from($value);
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['int', 'null']
        );
    }

    public function getBindingType(): int
    {
        return ParameterType::INTEGER;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
