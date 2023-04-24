<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230424115135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Create premises storing table";
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE premises (
                id INT AUTO_INCREMENT NOT NULL,
                address_id INT NOT NULL,
                restaurant_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_4A01730AF5B7AF75 (address_id),
                INDEX IDX_4A01730AB1E7706E (restaurant_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE premises 
                ADD CONSTRAINT FK_4A01730AF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)
        ');
        $this->addSql('
            ALTER TABLE premises 
                ADD CONSTRAINT FK_4A01730AB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE premises DROP FOREIGN KEY FK_4A01730AF5B7AF75');
        $this->addSql('ALTER TABLE premises DROP FOREIGN KEY FK_4A01730AB1E7706E');
        $this->addSql('DROP TABLE premises');
    }
}
