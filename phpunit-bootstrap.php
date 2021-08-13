<?php

declare(strict_types=1);

use Brick\DateTime\Doctrine\Types\DayOfWeekType;
use Brick\DateTime\Doctrine\Types\DurationType;
use Brick\DateTime\Doctrine\Types\InstantType;
use Brick\DateTime\Doctrine\Types\LocalDateTimeType;
use Brick\DateTime\Doctrine\Types\LocalDateType;
use Brick\DateTime\Doctrine\Types\LocalTimeType;
use Brick\DateTime\Doctrine\Types\PeriodType;
use Doctrine\DBAL\Types\Type;

require __DIR__ . '/vendor/autoload.php';

Type::addType('DayOfWeek', DayOfWeekType::class);
Type::addType('Instant', InstantType::class);
Type::addType('LocalDate', LocalDateType::class);
Type::addType('LocalTime', LocalTimeType::class);
Type::addType('LocalDateTime', LocalDateTimeType::class);
Type::addType('Duration', DurationType::class);
Type::addType('Period', PeriodType::class);
