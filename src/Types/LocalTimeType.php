<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for LocalTime.
 *
 * Maps to a database TIME type if supported.
 */
final class LocalTimeType extends Type
{
    public function getName(): string
    {
        return 'LocalTime';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getTimeTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
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

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            [LocalTime::class, 'null']
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?LocalTime
    {
        if ($value === null) {
            return null;
        }

        return LocalTime::parse((string) $value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
