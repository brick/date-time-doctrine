<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\LocalDateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use Override;

use function str_replace;

/**
 * Doctrine type for LocalDateTime.
 *
 * Maps to a database DATETIME type if supported.
 */
final class LocalDateTimeType extends Type
{
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
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

        throw InvalidType::new(
            $value,
            self::class,
            [LocalDateTime::class, 'null'],
        );
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?LocalDateTime
    {
        if ($value === null) {
            return null;
        }

        $value = str_replace(' ', 'T', (string) $value);

        try {
            return LocalDateTime::parse($value);
        } catch (DateTimeException $e) {
            throw ValueNotConvertible::new(
                $value,
                LocalDateTime::class,
                $e->getMessage(),
                $e,
            );
        }
    }
}
