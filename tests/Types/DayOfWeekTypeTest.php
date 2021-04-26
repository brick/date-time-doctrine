<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\DayOfWeek;
use Brick\DateTime\Doctrine\Types\DayOfWeekType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use stdClass;

class DayOfWeekTypeTest extends TestCase
{
    /**
     * @dataProvider providerConvertToDatabaseValue
     */
    public function testConvertToDatabaseValue(?DayOfWeek $value, ?int $expectedValue): void
    {
        $type = new DayOfWeekType();
        $actualValue = $type->convertToDatabaseValue($value, new SqlitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [DayOfWeek::monday(), 1],
            [DayOfWeek::tuesday(), 2],
            [DayOfWeek::wednesday(), 3],
            [DayOfWeek::thursday(), 4],
            [DayOfWeek::friday(), 5],
            [DayOfWeek::saturday(), 6],
            [DayOfWeek::sunday(), 7],
        ];
    }

    /**
     * @dataProvider providerConvertToDatabaseValueWithInvalidValue
     */
    public function testConvertToDatabaseValueWithInvalidValue($value): void
    {
        $type = new DayOfWeekType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SqlitePlatform());
    }

    public function providerConvertToDatabaseValueWithInvalidValue(): array
    {
        return [
            [123],
            [false],
            [true],
            ['string'],
            [new stdClass()],
            [LocalDate::parse('2021-04-17')],
            [LocalTime::parse('06:31:00')]
        ];
    }

    /**
     * @dataProvider providerConvertToPHPValue
     */
    public function testConvertToPHPValue($value, ?int $expectedDayOfWeekValue): void
    {
        $type = new DayOfWeekType();
        $actualValue = $type->convertToPHPValue($value, new SqlitePlatform());

        if ($expectedDayOfWeekValue === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(DayOfWeek::class, $actualValue);
            self::assertSame($expectedDayOfWeekValue, $actualValue->getValue());
        }
    }

    public function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            [1, 1],
            [2, 2],
            [3, 3],
            [4, 4],
            [5, 5],
            [6, 6],
            [7, 7],
            ['1', 1],
            ['2', 2],
            ['3', 3],
            ['4', 4],
            ['5', 5],
            ['6', 6],
            ['7', 7],
        ];
    }

    /**
     * @dataProvider providerConvertToPHPValueWithInvalidValue
     */
    public function testConvertToPHPValueWithInvalidValue($value, string $expectedExceptionClass): void
    {
        $type = new DayOfWeekType();

        $this->expectException($expectedExceptionClass);
        $type->convertToPHPValue($value, new SqlitePlatform());
    }

    public function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, DateTimeException::class],
            [8, DateTimeException::class],
            ['0', DateTimeException::class],
            ['8', DateTimeException::class],
        ];
    }
}
