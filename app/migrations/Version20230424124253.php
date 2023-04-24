<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230424124253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Added relation between dish and premises";
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE premises_dish (
                premises_id INT NOT NULL,
                dish_id INT NOT NULL,
                INDEX IDX_F03626C818C35A0F (premises_id),
                INDEX IDX_F03626C8148EB0CB (dish_id),
                PRIMARY KEY(premises_id, dish_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE premises_dish 
                ADD CONSTRAINT FK_F03626C818C35A0F FOREIGN KEY (premises_id) REFERENCES premises (id) ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE premises_dish 
                ADD CONSTRAINT FK_F03626C8148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE premises_dish DROP FOREIGN KEY FK_F03626C818C35A0F');
        $this->addSql('ALTER TABLE premises_dish DROP FOREIGN KEY FK_F03626C8148EB0CB');
        $this->addSql('DROP TABLE premises_dish');
    }
}
