<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\Instant;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Doctrine type for Instant.
 *
 * Maps to a database integer column, storing the epoch second, and silently discarding the nanos.
 */
final class InstantType extends Type
{
    public function getName(): string
    {
        return 'Instant';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Instant) {
            return $value->getEpochSecond();
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            [Instant::class, 'null']
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Instant
    {
        if ($value === null) {
            return null;
        }

        return Instant::of((int) $value);
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
