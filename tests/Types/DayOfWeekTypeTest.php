<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DayOfWeek;
use Brick\DateTime\Doctrine\Types\DayOfWeekType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class DayOfWeekTypeTest extends TestCase
{
    private function getDayOfWeekType(): DayOfWeekType
    {
        return Type::getType('DayOfWeek');
    }

    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(?DayOfWeek $value, ?int $expectedValue): void
    {
        $type = $this->getDayOfWeekType();
        $actualValue = $type->convertToDatabaseValue($value, new SQLitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [DayOfWeek::MONDAY, 1],
            [DayOfWeek::TUESDAY, 2],
            [DayOfWeek::WEDNESDAY, 3],
            [DayOfWeek::THURSDAY, 4],
            [DayOfWeek::FRIDAY, 5],
            [DayOfWeek::SATURDAY, 6],
            [DayOfWeek::SUNDAY, 7],
        ];
    }

    #[DataProvider('providerConvertToDatabaseValueWithInvalidValue')]
    public function testConvertToDatabaseValueWithInvalidValue(mixed $value): void
    {
        $type = $this->getDayOfWeekType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, new SQLitePlatform());
    }

    public static function providerConvertToDatabaseValueWithInvalidValue(): array
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

    #[DataProvider('providerConvertToPHPValue')]
    public function testConvertToPHPValue(mixed $value, ?int $expectedDayOfWeekValue): void
    {
        $type = $this->getDayOfWeekType();
        $actualValue = $type->convertToPHPValue($value, new SQLitePlatform());

        if ($expectedDayOfWeekValue === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(DayOfWeek::class, $actualValue);
            self::assertSame($expectedDayOfWeekValue, $actualValue->value);
        }
    }

    public static function providerConvertToPHPValue(): array
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
        ];
    }

    #[DataProvider('providerConvertToPHPValueWithInvalidValue')]
    public function testConvertToPHPValueWithInvalidValue(mixed $value, string $expectedExceptionMessage): void
    {
        $type = $this->getDayOfWeekType();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $type->convertToPHPValue($value, new SQLitePlatform());
    }

    public static function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, '0 is not a valid backing value for enum'],
            [8, '8 is not a valid backing value for enum'],
        ];
    }
}
