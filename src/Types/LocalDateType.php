<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\LocalDate;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for LocalDate.
 *
 * Maps to a database DATE type if supported.
 */
final class LocalDateType extends Type
{
    public function getName(): string
    {
        return 'LocalDate';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDate) {
            return (string) $value;
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            [LocalDate::class, 'null']
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?LocalDate
    {
        if ($value === null) {
            return null;
        }

        return LocalDate::parse((string) $value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
