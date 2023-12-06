<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests;

use Brick\DateTime\DayOfWeek;
use Brick\DateTime\Doctrine\Tests\Entity\KitchenSink;
use Brick\DateTime\Duration;
use Brick\DateTime\Instant;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Brick\DateTime\Period;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\SchemaTool;

class TypesFunctionalTestCase extends AbstractFunctionalTestCase
{
    public function testCreateSchema(): Connection
    {
        $connection = self::createConnection();
        $entityManager = self::createEntityManager($connection);
        $schemaTool = new SchemaTool($entityManager);

        $classMetadata = $entityManager->getClassMetadata(KitchenSink::class);

        $sql = $schemaTool->getUpdateSchemaSql([$classMetadata]);
        self::assertCount(1, $sql);
        $sql = $sql[0];

        self::assertStringContainsString('dayOfWeek SMALLINT DEFAULT NULL --(DC2Type:DayOfWeek)', $sql);
        self::assertStringContainsString('instant INTEGER DEFAULT NULL --(DC2Type:Instant)', $sql);
        self::assertStringContainsString('localDate DATE DEFAULT NULL --(DC2Type:LocalDate)', $sql);
        self::assertStringContainsString('localTime TIME DEFAULT NULL --(DC2Type:LocalTime)', $sql);
        self::assertStringContainsString('localDateTime DATETIME DEFAULT NULL --(DC2Type:LocalDateTime)', $sql);
        self::assertStringContainsString('duration VARCHAR(64) DEFAULT NULL --(DC2Type:Duration)', $sql);
        self::assertStringContainsString('period VARCHAR(64) DEFAULT NULL --(DC2Type:Period)', $sql);

        $connection->exec($sql);

        return $connection;
    }

    /**
     * @depends testCreateSchema
     */
    public function testSaveNull(Connection $connection): Connection
    {
        $em = self::createEntityManager($connection);
        self::truncateEntityTable($em);

        $entity = new KitchenSink();

        $em->persist($entity);
        $em->flush();

        // https://github.com/sebastianbergmann/phpunit/issues/3016
        self::assertTrue(true);

        return $connection;
    }

    /**
     * @depends testSaveNull
     */
    public function testLoadNull(Connection $connection): void
    {
        $em = self::createEntityManager($connection);

        $entity = self::getFirstEntity($em);

        self::assertNotNull($entity);

        self::assertNull($entity->dayOfWeek);
        self::assertNull($entity->instant);
        self::assertNull($entity->localDate);
        self::assertNull($entity->localTime);
        self::assertNull($entity->localDateTime);
        self::assertNull($entity->duration);
        self::assertNull($entity->period);
    }

    /**
     * @depends testCreateSchema
     */
    public function testSaveValues(Connection $connection): Connection
    {
        $em = self::createEntityManager($connection);
        self::truncateEntityTable($em);

        $entity = new KitchenSink();

        $entity->instant = Instant::of(1234567890);
        $entity->dayOfWeek = DayOfWeek::friday();
        $entity->localDate = LocalDate::parse('2021-04-17');
        $entity->localTime = LocalTime::parse('06:31:45');
        $entity->localDateTime = LocalDateTime::parse('2017-01-16T10:01:02');
        $entity->duration = Duration::ofSeconds(7230, 123456789);
        $entity->period = Period::of(1, 3, 15);

        $em->persist($entity);
        $em->flush();

        // https://github.com/sebastianbergmann/phpunit/issues/3016
        self::assertTrue(true);

        return $connection;
    }

    /**
     * @depends testSaveValues
     */
    public function testLoadValues(Connection $connection): void
    {
        $em = self::createEntityManager($connection);

        $entity = self::getFirstEntity($em);

        self::assertNotNull($entity);

        self::assertInstanceOf(DayOfWeek::class, $entity->dayOfWeek);
        self::assertSame(5, $entity->dayOfWeek->getValue());

        self::assertInstanceOf(Instant::class, $entity->instant);
        self::assertSame(1234567890, $entity->instant->getEpochSecond());
        self::assertSame(0, $entity->instant->getNano());

        self::assertInstanceOf(LocalDate::class, $entity->localDate);
        self::assertSame('2021-04-17', (string) $entity->localDate);

        self::assertInstanceOf(LocalTime::class, $entity->localTime);
        self::assertSame('06:31:45', (string) $entity->localTime);

        self::assertInstanceOf(LocalDateTime::class, $entity->localDateTime);
        self::assertSame('2017-01-16T10:01:02', (string) $entity->localDateTime);

        self::assertInstanceOf(Duration::class, $entity->duration);
        self::assertSame('PT2H30.123456789S', (string) $entity->duration);

        self::assertInstanceOf(Period::class, $entity->period);
        self::assertSame('P1Y3M15D', (string) $entity->period);
    }
}
