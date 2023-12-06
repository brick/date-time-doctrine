<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Doctrine\Types\LocalTimeType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalTime;
use Brick\DateTime\LocalDateTime;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use stdClass;

class LocalTimeTypeTest extends TestCase
{
    private function getLocalTimeType(): LocalTimeType
    {
        return Type::getType('LocalTime');
    }

    /**
     * @dataProvider providerConvertToDatabaseValue
     */
    public function testConvertToDatabaseValue(?LocalTime $value, ?string $expectedValue): void
    {
        $type = $this->getLocalTimeType();
        $actualValue = $type->convertToDatabaseValue($value, new SqlitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [LocalTime::of(9, 2), '09:02:00'],
            [LocalTime::of(10, 31, 1), '10:31:01'],
            [LocalTime::of(10, 31, 1, 7000000), '10:31:01.007'],
        ];
    }

    /**
     * @dataProvider providerConvertToDatabaseValueWithInvalidValue
     */
    public function testConvertToDatabaseValueWithInvalidValue($value): void
    {
        $type = $this->getLocalTimeType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SqlitePlatform());
    }

    public static function providerConvertToDatabaseValueWithInvalidValue(): array
    {
        return [
            [123],
            [false],
            [true],
            ['01:02:03'],
            [new stdClass()],
            [LocalDate::parse('2017-01-16')],
            [LocalDateTime::parse('2017-01-16T10:31:00')],
        ];
    }

    /**
     * @dataProvider providerConvertToPHPValue
     */
    public function testConvertToPHPValue($value, ?string $expectedLocalTimeString): void
    {
        $type = $this->getLocalTimeType();
        $actualValue = $type->convertToPHPValue($value, new SqlitePlatform());

        if ($expectedLocalTimeString === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(LocalTime::class, $actualValue);
            self::assertSame($expectedLocalTimeString, (string) $actualValue);
        }
    }

    public static function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            ['01:02:03', '01:02:03'],
            ['01:02:03.001', '01:02:03.001']
        ];
    }

    /**
     * @dataProvider providerConvertToPHPValueWithInvalidValue
     */
    public function testConvertToPHPValueWithInvalidValue($value, string $expectedExceptionClass): void
    {
        $type = $this->getLocalTimeType();

        $this->expectException($expectedExceptionClass);
        $type->convertToPHPValue($value, new SqlitePlatform());
    }

    public static function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, DateTimeException::class],
            ['01:02:60', DateTimeException::class],
            ['2021-04-17', DateTimeException::class],
        ];
    }
}
