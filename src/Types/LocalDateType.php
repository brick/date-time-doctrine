<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\LocalDate;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

/**
 * Doctrine type for LocalDate.
 *
 * Maps to a database DATE type if supported.
 */
final class LocalDateType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDate) {
            return (string) $value;
        }

        throw InvalidType::new(
            $value,
            static::class,
            [LocalDate::class, 'null'],
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?LocalDate
    {
        if ($value === null) {
            return null;
        }

        try {
            return LocalDate::parse((string) $value);
        } catch (DateTimeException $e) {
            throw ValueNotConvertible::new(
                $value,
                LocalDate::class,
                $e->getMessage(),
                $e,
            );
        }
    }
}
