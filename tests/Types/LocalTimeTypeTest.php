<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\Doctrine\Types\LocalTimeType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalTime;
use Brick\DateTime\LocalDateTime;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class LocalTimeTypeTest extends TestCase
{
    private function getLocalTimeType(): LocalTimeType
    {
        return Type::getType('LocalTime');
    }

    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(?LocalTime $value, ?string $expectedValue): void
    {
        $type = $this->getLocalTimeType();
        $actualValue = $type->convertToDatabaseValue($value, new SQLitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [LocalTime::of(9, 2), '09:02:00'],
            [LocalTime::of(10, 31, 1), '10:31:01'],
            [LocalTime::of(10, 31, 0, 7_000_000), '10:31:00.007'],
            [LocalTime::of(10, 31, 1, 7_000_000), '10:31:01.007'],
        ];
    }

    #[DataProvider('providerConvertToDatabaseValueWithInvalidValue')]
    public function testConvertToDatabaseValueWithInvalidValue(mixed $value): void
    {
        $type = $this->getLocalTimeType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SQLitePlatform());
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

    #[DataProvider('providerConvertToPHPValue')]
    public function testConvertToPHPValue(mixed $value, ?string $expectedLocalTimeString): void
    {
        $type = $this->getLocalTimeType();
        $actualValue = $type->convertToPHPValue($value, new SQLitePlatform());

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

    #[DataProvider('providerConvertToPHPValueWithInvalidValue')]
    public function testConvertToPHPValueWithInvalidValue(mixed $value, string $expectedExceptionMessage): void
    {
        $type = $this->getLocalTimeType();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $type->convertToPHPValue($value, new SQLitePlatform());
    }

    public static function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, 'Failed to parse "0".'],
            ['01:02:60', 'Invalid second-of-minute: 60 is not in the range 0 to 59.'],
            ['2021-04-17', 'Failed to parse "2021-04-17".'],
        ];
    }
}
