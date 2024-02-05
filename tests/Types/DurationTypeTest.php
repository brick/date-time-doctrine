<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Doctrine\Types\DurationType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\Duration;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class DurationTypeTest extends TestCase
{
    private function getDurationType(): DurationType
    {
        return Type::getType('Duration');
    }

    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(?Duration $value, ?string $expectedValue): void
    {
        $type = $this->getDurationType();
        $actualValue = $type->convertToDatabaseValue($value, new SQLitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [Duration::ofSeconds(10921, 987654321), 'PT3H2M1.987654321S'],
        ];
    }

    #[DataProvider('providerConvertToDatabaseValueWithInvalidValue')]
    public function testConvertToDatabaseValueWithInvalidValue(mixed $value): void
    {
        $type = $this->getDurationType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SQLitePlatform());
    }

    public static function providerConvertToDatabaseValueWithInvalidValue(): array
    {
        return [
            [123],
            [false],
            [true],
            ['2017-01-16'],
            [new stdClass()],
            [LocalDate::parse('2017-01-16')],
            [LocalTime::parse('10:31:00')]
        ];
    }

    #[DataProvider('providerConvertToPHPValue')]
    public function testConvertToPHPValue(mixed $value, ?string $expectedDurationString): void
    {
        $type = $this->getDurationType();
        $actualValue = $type->convertToPHPValue($value, new SQLitePlatform());

        if ($expectedDurationString === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(Duration::class, $actualValue);
            self::assertSame($expectedDurationString, (string) $actualValue);
        }
    }

    public static function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            ['PT3H2M1.987S', 'PT3H2M1.987S'],
        ];
    }

    #[DataProvider('providerConvertToPHPValueWithInvalidValue')]
    public function testConvertToPHPValueWithInvalidValue(mixed $value, string $expectedExceptionClass): void
    {
        $type = $this->getDurationType();

        $this->expectException($expectedExceptionClass);
        $type->convertToPHPValue($value, new SQLitePlatform());
    }

    public static function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, DateTimeException::class],
            ['10:31:00', DateTimeException::class],
            ['2021-04-00', DateTimeException::class],
        ];
    }
}
