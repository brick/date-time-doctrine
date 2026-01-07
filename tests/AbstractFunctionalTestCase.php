<?php

declare(strict_types=1);

namespace Brick\DateTime\Doctrine\Tests;

use Brick\DateTime\Doctrine\Tests\Entity\KitchenSink;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\TestCase;

use const PHP_VERSION_ID;

abstract class AbstractFunctionalTestCase extends TestCase
{
    final protected static function createConnection(): Connection
    {
        $dsnParser = new DsnParser(['sqlite' => 'pdo_sqlite']);

        return DriverManager::getConnection(
            $dsnParser->parse('sqlite:///:memory:'),
        );
    }

    final protected static function createEntityManager(Connection $connection): EntityManager
    {
        return new EntityManager($connection, self::createConfiguration());
    }

    final protected static function truncateEntityTable(EntityManager $em): void
    {
        $em->createQueryBuilder()
            ->delete(KitchenSink::class, 's')
            ->getQuery()
            ->execute();
    }

    final protected function getFirstEntity(EntityManager $em): ?KitchenSink
    {
        return $em->createQueryBuilder()
            ->select('s')
            ->from(KitchenSink::class, 's')
            ->getQuery()
            ->getOneOrNullResult();
    }

    private static function createConfiguration(): Configuration
    {
        $configuration = ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/tests/Entity']);

        if (PHP_VERSION_ID >= 80400) {
            $configuration->enableNativeLazyObjects(true);
        }

        return $configuration;
    }
}
