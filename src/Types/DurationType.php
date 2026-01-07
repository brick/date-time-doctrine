<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Duration;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use Override;

/**
 * Doctrine type for Duration.
 *
 * Maps its string representation to a VARCHAR column.
 */
final class DurationType extends Type
{
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if (! isset($column['length'])) {
            $column['length'] = 64;
        }

        return $platform->getStringTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Duration) {
            return (string) $value;
        }

        throw InvalidType::new(
            $value,
            static::class,
            [Duration::class, 'null'],
        );
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Duration
    {
        if ($value === null) {
            return null;
        }

        try {
            return Duration::parse((string) $value);
        } catch (DateTimeException $e) {
            throw ValueNotConvertible::new(
                $value,
                Duration::class,
                $e->getMessage(),
                $e,
            );
        }
    }
}
