<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Doctrine\Types\PeriodType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\Period;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use stdClass;

class PeriodTypeTest extends TestCase
{
    private function getPeriodType(): PeriodType
    {
        return Type::getType('Period');
    }

    /**
     * @dataProvider providerConvertToDatabaseValue
     */
    public function testConvertToDatabaseValue(?Period $value, ?string $expectedValue): void
    {
        $type = $this->getPeriodType();
        $actualValue = $type->convertToDatabaseValue($value, new SqlitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [Period::of(3, 2, 1), 'P3Y2M1D'],
        ];
    }

    /**
     * @dataProvider providerConvertToDatabaseValueWithInvalidValue
     */
    public function testConvertToDatabaseValueWithInvalidValue($value): void
    {
        $type = $this->getPeriodType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SqlitePlatform());
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

    /**
     * @dataProvider providerConvertToPHPValue
     */
    public function testConvertToPHPValue($value, ?string $expectedPeriodString): void
    {
        $type = $this->getPeriodType();
        $actualValue = $type->convertToPHPValue($value, new SqlitePlatform());

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

    /**
     * @dataProvider providerConvertToPHPValueWithInvalidValue
     */
    public function testConvertToPHPValueWithInvalidValue($value, string $expectedExceptionClass): void
    {
        $type = $this->getPeriodType();

        $this->expectException($expectedExceptionClass);
        $type->convertToPHPValue($value, new SqlitePlatform());
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
