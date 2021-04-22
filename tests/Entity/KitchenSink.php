<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests\Entity;

use Brick\DateTime\DayOfWeek;
use Brick\DateTime\Instant;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class KitchenSink
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(type="DayOfWeek", nullable=true)
     *
     * @var DayOfWeek|null
     */
    public $dayOfWeek = null;

    /**
     * @ORM\Column(type="Instant", nullable=true)
     *
     * @var Instant|null
     */
    public $instant = null;

    /**
     * @ORM\Column(type="LocalDate", nullable=true)
     *
     * @var LocalDate|null
     */
    public $localDate = null;

    /**
     * @ORM\Column(type="LocalTime", nullable=true)
     *
     * @var LocalTime|null
     */
    public $localTime = null;

    /**
     * @ORM\Column(type="LocalDateTime", nullable=true)
     *
     * @var LocalDateTime|null
     */
    public $localDateTime = null;
}
