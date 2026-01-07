<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use Override;

/**
 * Doctrine type for LocalTime.
 *
 * Maps to a database TIME type if supported.
 */
final class LocalTimeType extends Type
{
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getTimeTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalTime) {
            $stringValue = (string) $value;

            if ($value->getSecond() === 0 && $value->getNano() === 0) {
                $stringValue .= ':00';
            }

            return $stringValue;
        }

        throw InvalidType::new(
            $value,
            self::class,
            [LocalTime::class, 'null'],
        );
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?LocalTime
    {
        if ($value === null) {
            return null;
        }

        try {
            return LocalTime::parse((string) $value);
        } catch (DateTimeException $e) {
            throw ValueNotConvertible::new(
                $value,
                LocalTime::class,
                $e->getMessage(),
                $e,
            );
        }
    }
}
