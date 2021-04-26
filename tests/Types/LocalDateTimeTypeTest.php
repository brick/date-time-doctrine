<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Doctrine\Types\LocalDateTimeType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use stdClass;

class LocalDateTimeTypeTest extends TestCase
{
    /**
     * @dataProvider providerConvertToDatabaseValue
     */
    public function testConvertToDatabaseValue(?LocalDateTime $value, ?string $expectedValue): void
    {
        $type = new LocalDateTimeType();
        $actualValue = $type->convertToDatabaseValue($value, new SqlitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [LocalDateTime::of(2021, 4, 17, 9, 2), '2021-04-17 09:02:00'],
            [LocalDateTime::of(2021, 4, 17, 9, 2, 7), '2021-04-17 09:02:07'],
            [LocalDateTime::of(2021, 4, 17, 9, 2, 0, 7000000), '2021-04-17 09:02:00.007'],
        ];
    }

    /**
     * @dataProvider providerConvertToDatabaseValueWithInvalidValue
     */
    public function testConvertToDatabaseValueWithInvalidValue($value): void
    {
        $type = new LocalDateTimeType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SqlitePlatform());
    }

    public function providerConvertToDatabaseValueWithInvalidValue(): array
    {
        return [
            [123],
            [false],
            [true],
            ['2017-01-16 01:02:03'],
            ['2017-01-16T01:02:03'],
            [new stdClass()],
            [LocalDate::parse('2017-01-16')],
            [LocalTime::parse('10:31:00')],
        ];
    }

    /**
     * @dataProvider providerConvertToPHPValue
     */
    public function testConvertToPHPValue($value, ?string $expectedLocalDateTimeString): void
    {
        $type = new LocalDateTimeType();
        $actualValue = $type->convertToPHPValue($value, new SqlitePlatform());

        if ($expectedLocalDateTimeString === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(LocalDateTime::class, $actualValue);
            self::assertSame($expectedLocalDateTimeString, (string) $actualValue);
        }
    }

    public function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            ['2021-04-17 01:02:03', '2021-04-17T01:02:03'],
            ['2021-04-17T01:02:03', '2021-04-17T01:02:03'],
            ['2021-04-17 01:02:03.456', '2021-04-17T01:02:03.456'],
            ['2021-04-17T01:02:03.456', '2021-04-17T01:02:03.456'],
        ];
    }

    /**
     * @dataProvider providerConvertToPHPValueWithInvalidValue
     */
    public function testConvertToPHPValueWithInvalidValue($value, string $expectedExceptionClass): void
    {
        $type = new LocalDateTimeType();

        $this->expectException($expectedExceptionClass);
        $type->convertToPHPValue($value, new SqlitePlatform());
    }

    public function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, DateTimeException::class],
            ['01:02:59', DateTimeException::class],
            ['2021-04-17', DateTimeException::class],
            ['2021-04-17Z01:02:03.456', DateTimeException::class],
        ];
    }
}
