<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests;

use Brick\DateTime\DayOfWeek;
use Brick\DateTime\Doctrine\Tests\Entity\KitchenSink;
use Brick\DateTime\Instant;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\SchemaTool;

class TypesTest extends AbstractTest
{
    public function testCreateSchema(): Connection
    {
        $connection = self::createConnection();
        $entityManager = self::createEntityManager($connection);
        $schemaTool = new SchemaTool($entityManager);

        $classMetadata = $entityManager->getClassMetadata(KitchenSink::class);

        $expectedSQL =
            "CREATE TABLE KitchenSink (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL" .
            ", dayOfWeek SMALLINT DEFAULT NULL --(DC2Type:DayOfWeek)\n" .
            ", instant INTEGER DEFAULT NULL --(DC2Type:Instant)\n" .
            ", localDate DATE DEFAULT NULL --(DC2Type:LocalDate)\n" .
            ", localTime TIME DEFAULT NULL --(DC2Type:LocalTime)\n" .
            ", localDateTime DATETIME DEFAULT NULL --(DC2Type:LocalDateTime)\n" .
            ")";

        self::assertSame([$expectedSQL], $schemaTool->getUpdateSchemaSql([$classMetadata]));

        $connection->exec($expectedSQL);

        return $connection;
    }

    /**
     * @depends testCreateSchema
     */
    public function testSaveNull(Connection $connection): Connection
    {
        $this->expectNotToPerformAssertions();

        $em = self::createEntityManager($connection);
        self::truncateEntityTable($em);

        $entity = new KitchenSink();

        $em->persist($entity);
        $em->flush();

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
    }

    /**
     * @depends testCreateSchema
     */
    public function testSaveValues(Connection $connection): Connection
    {
        $this->expectNotToPerformAssertions();

        $em = self::createEntityManager($connection);
        self::truncateEntityTable($em);

        $entity = new KitchenSink();

        $entity->instant = Instant::of(1234567890);
        $entity->dayOfWeek = DayOfWeek::friday();
        $entity->localDate = LocalDate::parse('2021-04-17');
        $entity->localTime = LocalTime::parse('06:31:45');
        $entity->localDateTime = LocalDateTime::parse('2017-01-16T10:01:02');

        $em->persist($entity);
        $em->flush();

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
    }
}
