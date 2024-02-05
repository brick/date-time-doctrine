<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Entity;

use Brick\DateTime\DayOfWeek;
use Brick\DateTime\Duration;
use Brick\DateTime\Instant;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Brick\DateTime\Period;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class KitchenSink
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(type: 'DayOfWeek', nullable: true)]
    public ?DayOfWeek $dayOfWeek = null;

    #[ORM\Column(type: 'Instant', nullable: true)]
    public ?Instant $instant = null;

    #[ORM\Column(type: 'LocalDate', nullable: true)]
    public ?LocalDate $localDate = null;

    #[ORM\Column(type: 'LocalTime', nullable: true)]
    public ?LocalTime $localTime = null;

    #[ORM\Column(type: 'LocalDateTime', nullable: true)]
    public ?LocalDateTime $localDateTime = null;

    #[ORM\Column(type: 'Duration', nullable: true)]
    public ?Duration $duration = null;

    #[ORM\Column(type: 'Period', nullable: true)]
    public ?Period $period = null;
}
