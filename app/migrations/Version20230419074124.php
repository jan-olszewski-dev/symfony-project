<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class Version20230419074124 extends AbstractMigration
{
    private ?PasswordHasherInterface $passwordHasher = null;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);
    }

    public function setPasswordHasher(PasswordHasherInterface $passwordHasher): void
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function getDescription(): string
    {
        return 'Create test user';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf($_ENV['APP_ENV'] !== 'test');
        $password = $this->passwordHasher->hash('zaq1@WSX');
        $googleSubId = uniqid('google_');
        $linkedInSubId = uniqid('linkedin_');
        $facebookSubId = uniqid('facebook_');

        $this->addSql("
            INSERT INTO user (email, password, first_name, last_name, google_sub_id, linked_in_sub_id, facebook_sub_id)  
                VALUE ('test.test@test.com', '$password', 'test', 'test', '$googleSubId', '$linkedInSubId', '$facebookSubId')
        ");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf($_ENV['APP_ENV'] !== 'test');
        $this->addSql("DELETE FROM user WHERE email = 'test.test@test.com'");
    }
}
