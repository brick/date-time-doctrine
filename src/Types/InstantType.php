<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\Instant;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use Override;

/**
 * Doctrine type for Instant.
 *
 * Maps to a database integer column, storing the epoch second, and silently discarding the nanos.
 */
final class InstantType extends Type
{
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Instant) {
            return $value->getEpochSecond();
        }

        throw InvalidType::new(
            $value,
            self::class,
            [Instant::class, 'null'],
        );
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Instant
    {
        if ($value === null) {
            return null;
        }

        return Instant::of((int) $value);
    }

    #[Override]
    public function getBindingType(): ParameterType
    {
        return ParameterType::INTEGER;
    }
}
