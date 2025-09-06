<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\Doctrine\Types\LocalDateTimeType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class LocalDateTimeTypeTest extends TestCase
{
    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(?LocalDateTime $value, ?string $expectedValue): void
    {
        $type = $this->getLocalDateTimeType();
        $actualValue = $type->convertToDatabaseValue($value, new SQLitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [LocalDateTime::of(2021, 4, 17, 9, 2), '2021-04-17 09:02:00'],
            [LocalDateTime::of(2021, 4, 17, 9, 2, 7), '2021-04-17 09:02:07'],
            [LocalDateTime::of(2021, 4, 17, 9, 2, 0, 7_000_000), '2021-04-17 09:02:00.007'],
            [LocalDateTime::of(2021, 4, 17, 9, 2, 1, 7_000_000), '2021-04-17 09:02:01.007'],
        ];
    }

    #[DataProvider('providerConvertToDatabaseValueWithInvalidValue')]
    public function testConvertToDatabaseValueWithInvalidValue(mixed $value): void
    {
        $type = $this->getLocalDateTimeType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SQLitePlatform());
    }

    public static function providerConvertToDatabaseValueWithInvalidValue(): array
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

    #[DataProvider('providerConvertToPHPValue')]
    public function testConvertToPHPValue(mixed $value, ?string $expectedLocalDateTimeString): void
    {
        $type = $this->getLocalDateTimeType();
        $actualValue = $type->convertToPHPValue($value, new SQLitePlatform());

        if ($expectedLocalDateTimeString === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(LocalDateTime::class, $actualValue);
            self::assertSame($expectedLocalDateTimeString, (string) $actualValue);
        }
    }

    public static function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            ['2021-04-17 01:02:03', '2021-04-17T01:02:03'],
            ['2021-04-17T01:02:03', '2021-04-17T01:02:03'],
            ['2021-04-17 01:02:03.456', '2021-04-17T01:02:03.456'],
            ['2021-04-17T01:02:03.456', '2021-04-17T01:02:03.456'],
        ];
    }

    #[DataProvider('providerConvertToPHPValueWithInvalidValue')]
    public function testConvertToPHPValueWithInvalidValue(mixed $value, string $expectedExceptionMessage): void
    {
        $type = $this->getLocalDateTimeType();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $type->convertToPHPValue($value, new SQLitePlatform());
    }

    public static function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, 'Failed to parse "0"'],
            ['01:02:59', 'Failed to parse "01:02:59".'],
            ['2021-04-17', 'Failed to parse "2021-04-17".'],
            ['2021-04-17Z01:02:03.456', 'Failed to parse "2021-04-17Z01:02:03.456".'],
        ];
    }

    private function getLocalDateTimeType(): LocalDateTimeType
    {
        return Type::getType('LocalDateTime');
    }
}
