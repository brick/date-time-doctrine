<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\Period;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Doctrine type for Period.
 *
 * Maps its string representation to a VARCHAR column.
 */
final class PeriodType extends Type
{
    public function getName(): string
    {
        return 'Period';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if (!isset($column['length'])) {
            $column['length'] = 64;
        }

        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Period) {
            return (string) $value;
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            [Period::class, 'null']
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Period
    {
        if ($value === null) {
            return null;
        }

        return Period::parse((string) $value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
