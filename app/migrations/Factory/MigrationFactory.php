<?php

namespace App\Migrations\Factory;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory as MigrationFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class MigrationFactory implements MigrationFactoryInterface
{
    public function __construct(
        private Connection                     $connection,
        private LoggerInterface                $logger,
        private PasswordHasherFactoryInterface $passwordHasher
    )
    {
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $migration = new $migrationClassName($this->connection, $this->logger);

        if (method_exists($migration, 'setPasswordHasher')) {
            $passwordHasher = $this->passwordHasher->getPasswordHasher(User::class);
            $migration->setPasswordHasher($passwordHasher);
        }

        return $migration;
    }
}
