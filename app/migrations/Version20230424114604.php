<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230424114604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create dish storing table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE dish (
                id INT AUTO_INCREMENT NOT NULL,
                restaurant_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                price INT NOT NULL,
                INDEX IDX_957D8CB8B1E7706E (restaurant_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE dish 
                ADD CONSTRAINT FK_957D8CB8B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB8B1E7706E');
        $this->addSql('DROP TABLE dish');
    }
}
