<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230426112040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create roles and add relation to user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE user_role (
                id INT AUTO_INCREMENT NOT NULL,
                role VARCHAR(30) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE user_role_map (
                user_id INT NOT NULL,
                role_id INT NOT NULL,
                INDEX IDX_C742CAC9A76ED395 (user_id),
                INDEX IDX_C742CAC9D60322AC (role_id),
                PRIMARY KEY(user_id, role_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE user_role_map 
                ADD CONSTRAINT FK_C742CAC9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        ');
        $this->addSql('
            ALTER TABLE user_role_map 
                ADD CONSTRAINT FK_C742CAC9D60322AC FOREIGN KEY (role_id) REFERENCES user_role (id)
        ');
        $this->addSql('INSERT INTO user_role (role) VALUES ("ROLE_ADMIN"), ("ROLE_USER")');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user_role_map 
                DROP FOREIGN KEY FK_C742CAC9A76ED395
        ');
        $this->addSql('
            ALTER TABLE user_role_map 
                DROP FOREIGN KEY FK_C742CAC9D60322AC
        ');
        $this->addSql('DROP TABLE user_role_map');
        $this->addSql('DROP TABLE user_role');
    }
}
