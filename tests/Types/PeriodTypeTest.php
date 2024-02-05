<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Doctrine\Types\PeriodType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\Period;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class PeriodTypeTest extends TestCase
{
    private function getPeriodType(): PeriodType
    {
        return Type::getType('Period');
    }

    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(?Period $value, ?string $expectedValue): void
    {
        $type = $this->getPeriodType();
        $actualValue = $type->convertToDatabaseValue($value, new SQLitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [Period::of(3, 2, 1), 'P3Y2M1D'],
        ];
    }

    #[DataProvider('providerConvertToDatabaseValueWithInvalidValue')]
    public function testConvertToDatabaseValueWithInvalidValue(mixed $value): void
    {
        $type = $this->getPeriodType();

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
    public function testConvertToPHPValue(mixed $value, ?string $expectedPeriodString): void
    {
        $type = $this->getPeriodType();
        $actualValue = $type->convertToPHPValue($value, new SQLitePlatform());

        if ($expectedPeriodString === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(Period::class, $actualValue);
            self::assertSame($expectedPeriodString, (string) $actualValue);
        }
    }

    public static function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            ['P3Y2M1D', 'P3Y2M1D'],
        ];
    }

    #[DataProvider('providerConvertToPHPValueWithInvalidValue')]
    public function testConvertToPHPValueWithInvalidValue(mixed $value, string $expectedExceptionClass): void
    {
        $type = $this->getPeriodType();

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
