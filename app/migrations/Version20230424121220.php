<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230424121220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Create disposition and disposition_dish storing table";
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE disposition (
                id INT AUTO_INCREMENT NOT NULL,
                status VARCHAR(20) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql(
        'CREATE TABLE disposition_dish (
                disposition_id INT NOT NULL,
                dish_id INT NOT NULL,
                INDEX IDX_2A18AA23287B65ED (disposition_id),
                INDEX IDX_2A18AA23148EB0CB (dish_id),
                PRIMARY KEY(disposition_id, dish_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql(
        'ALTER TABLE disposition_dish 
                ADD CONSTRAINT FK_2A18AA23287B65ED FOREIGN KEY (disposition_id) REFERENCES disposition (id) ON DELETE CASCADE
        ');
        $this->addSql(
        'ALTER TABLE disposition_dish 
                ADD CONSTRAINT FK_2A18AA23148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE disposition_dish DROP FOREIGN KEY FK_2A18AA23287B65ED');
        $this->addSql('ALTER TABLE disposition_dish DROP FOREIGN KEY FK_2A18AA23148EB0CB');
        $this->addSql('DROP TABLE disposition');
        $this->addSql('DROP TABLE disposition_dish');
    }
}
