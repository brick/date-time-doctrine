<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\Doctrine\Types\LocalDateType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class LocalDateTypeTest extends TestCase
{
    private function getLocalDateType(): LocalDateType
    {
        return Type::getType('LocalDate');
    }

    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(?LocalDate $value, ?string $expectedValue): void
    {
        $type = $this->getLocalDateType();
        $actualValue = $type->convertToDatabaseValue($value, new SQLitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [LocalDate::of(2017, 1, 16), '2017-01-16'],
        ];
    }

    #[DataProvider('providerConvertToDatabaseValueWithInvalidValue')]
    public function testConvertToDatabaseValueWithInvalidValue(mixed $value): void
    {
        $type = $this->getLocalDateType();

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
            [LocalDateTime::parse('2017-01-16T10:31:00')],
            [LocalTime::parse('10:31:00')]
        ];
    }

    #[DataProvider('providerConvertToPHPValue')]
    public function testConvertToPHPValue(mixed $value, ?string $expectedLocalDateString): void
    {
        $type = $this->getLocalDateType();
        $actualValue = $type->convertToPHPValue($value, new SQLitePlatform());

        if ($expectedLocalDateString === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(LocalDate::class, $actualValue);
            self::assertSame($expectedLocalDateString, (string) $actualValue);
        }
    }

    public static function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            ['2021-04-17', '2021-04-17'],
        ];
    }

    #[DataProvider('providerConvertToPHPValueWithInvalidValue')]
    public function testConvertToPHPValueWithInvalidValue(mixed $value, string $expectedExceptionMessage): void
    {
        $type = $this->getLocalDateType();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $type->convertToPHPValue($value, new SQLitePlatform());
    }

    public static function providerConvertToPHPValueWithInvalidValue(): array
    {
        return [
            [0, 'Failed to parse "0".'],
            ['10:31:00', 'Failed to parse "10:31:00".'],
            ['2021-04-00', 'Invalid day-of-month: 0 is not in the range 1 to 31.'],
        ];
    }
}
