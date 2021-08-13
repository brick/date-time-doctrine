<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Types;

use Brick\DateTime\Duration;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Doctrine type for Duration.
 *
 * Maps its string representation to a VARCHAR column.
 */
final class DurationType extends Type
{
    public function getName(): string
    {
        return 'Duration';
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

        if ($value instanceof Duration) {
            return (string) $value;
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            [Duration::class, 'null']
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Duration
    {
        if ($value === null) {
            return null;
        }

        return Duration::parse((string) $value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
