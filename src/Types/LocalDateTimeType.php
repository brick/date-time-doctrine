<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\LocalDateTime;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for LocalDateTime.
 *
 * Maps to a database DATETIME type if supported.
 */
final class LocalDateTimeType extends Type
{
    public function getName()
    {
        return 'LocalDateTime';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getDateTimeTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDateTime) {
            $stringValue = str_replace('T', ' ', (string) $value);

            if ($value->getSecond() === 0 && $value->getNano() === 0) {
                $stringValue .= ':00';
            }

            return $stringValue;
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            [LocalDateTime::class, 'null']
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $value = str_replace(' ', 'T', (string) $value);

        return LocalDateTime::parse($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
