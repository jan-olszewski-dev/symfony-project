<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230424113431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Create address storing table";
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE address (
                id INT AUTO_INCREMENT NOT NULL,
                city_id INT NOT NULL,
                street VARCHAR(80) NOT NULL,
                street_number VARCHAR(10) NOT NULL,
                flat_number VARCHAR(10) DEFAULT NULL,
                postal_code VARCHAR(5) NOT NULL,
                INDEX IDX_D4E6F818BAC62AF (city_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE address 
                ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('DROP TABLE address');
    }
}
