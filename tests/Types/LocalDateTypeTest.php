<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Doctrine\Types\LocalDateType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use stdClass;

class LocalDateTypeTest extends TestCase
{
    private function getLocalDateType(): LocalDateType
    {
        return Type::getType('LocalDate');
    }

    /**
     * @dataProvider providerConvertToDatabaseValue
     */
    public function testConvertToDatabaseValue(?LocalDate $value, ?string $expectedValue): void
    {
        $type = $this->getLocalDateType();
        $actualValue = $type->convertToDatabaseValue($value, new SqlitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [LocalDate::of(2017, 1, 16), '2017-01-16'],
        ];
    }

    /**
     * @dataProvider providerConvertToDatabaseValueWithInvalidValue
     */
    public function testConvertToDatabaseValueWithInvalidValue($value): void
    {
        $type = $this->getLocalDateType();

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
            [LocalDateTime::parse('2017-01-16T10:31:00')],
            [LocalTime::parse('10:31:00')]
        ];
    }

    /**
     * @dataProvider providerConvertToPHPValue
     */
    public function testConvertToPHPValue($value, ?string $expectedLocalDateString): void
    {
        $type = $this->getLocalDateType();
        $actualValue = $type->convertToPHPValue($value, new SqlitePlatform());

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

    /**
     * @dataProvider providerConvertToPHPValueWithInvalidValue
     */
    public function testConvertToPHPValueWithInvalidValue($value, string $expectedExceptionClass): void
    {
        $type = $this->getLocalDateType();

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
