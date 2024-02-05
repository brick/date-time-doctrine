<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Types;

use Brick\DateTime\Instant;
use Brick\DateTime\Doctrine\Types\InstantType;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class InstantTypeTest extends TestCase
{
    private function getInstantType(): InstantType
    {
        return Type::getType('Instant');
    }

    #[DataProvider('providerConvertToDatabaseValue')]
    public function testConvertToDatabaseValue(?Instant $value, ?int $expectedValue): void
    {
        $type = $this->getInstantType();
        $actualValue = $type->convertToDatabaseValue($value, new SQLitePlatform());

        self::assertSame($expectedValue, $actualValue);
    }

    public static function providerConvertToDatabaseValue(): array
    {
        return [
            [null, null],
            [Instant::of(2000000000), 2000000000],
            [Instant::of(2000000000, 123456789), 2000000000],
        ];
    }

    #[DataProvider('providerConvertToDatabaseValueWithInvalidValue')]
    public function testConvertToDatabaseValueWithInvalidValue($value): void
    {
        $type = $this->getInstantType();

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
            [LocalDate::parse('2017-01-16')],
            [LocalTime::parse('10:31:00')]
        ];
    }

    #[DataProvider('providerConvertToPHPValue')]
    public function testConvertToPHPValue($value, ?int $expectedEpochSecond): void
    {
        $type = $this->getInstantType();
        $actualValue = $type->convertToPHPValue($value, new SQLitePlatform());

        if ($expectedEpochSecond === null) {
            self::assertNull($actualValue);
        } else {
            self::assertInstanceOf(Instant::class, $actualValue);
            self::assertSame($expectedEpochSecond, $actualValue->getEpochSecond());
        }
    }

    public static function providerConvertToPHPValue(): array
    {
        return [
            [null, null],
            [1, 1],
            [2000000000, 2000000000],
            ['2000000001', 2000000001],
        ];
    }
}
