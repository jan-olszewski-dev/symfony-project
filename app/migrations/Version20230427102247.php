<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230427102247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create restaurant employee roles relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE restaurant_role (
                id INT AUTO_INCREMENT NOT NULL,
                role VARCHAR(30) NOT NULL,
                UNIQUE INDEX UNIQ_957F1A0E57698A6A (role),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('INSERT INTO restaurant_role (role) VALUES ("ROLE_ADMIN"), ("ROLE_EMPLOYEE")');
        $this->addSql('
            CREATE TABLE restaurant_employee (
                id INT AUTO_INCREMENT NOT NULL, 
                restaurant_id INT NOT NULL, 
                employee_id INT NOT NULL, 
                INDEX IDX_7D3ABB4EB1E7706E (restaurant_id), 
                INDEX IDX_7D3ABB4E8C03F15C (employee_id), 
                UNIQUE INDEX UNIQ_7D3ABB4E8C03F15CB1E7706E (employee_id, restaurant_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE restaurant_employee_role_map (
                restaurant_employee_id INT NOT NULL,
                role_id INT NOT NULL,
                INDEX IDX_2F6C9DCEDD78CB46 (restaurant_employee_id),
                INDEX IDX_2F6C9DCED60322AC (role_id),
                PRIMARY KEY(restaurant_employee_id, role_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE restaurant_employee
                ADD CONSTRAINT FK_7D3ABB4EB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)
        ');
        $this->addSql('
            ALTER TABLE restaurant_employee
                ADD CONSTRAINT FK_7D3ABB4E8C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)
        ');
        $this->addSql('
            ALTER TABLE restaurant_employee_role_map
                ADD CONSTRAINT FK_2F6C9DCEDD78CB46 FOREIGN KEY (restaurant_employee_id) REFERENCES restaurant_employee (id)
        ');
        $this->addSql('
            ALTER TABLE restaurant_employee_role_map
                ADD CONSTRAINT FK_2F6C9DCED60322AC FOREIGN KEY (role_id) REFERENCES restaurant_role (id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE restaurant_employee DROP FOREIGN KEY FK_7D3ABB4EB1E7706E');
        $this->addSql('ALTER TABLE restaurant_employee DROP FOREIGN KEY FK_7D3ABB4E8C03F15C');
        $this->addSql('ALTER TABLE restaurant_employee_role_map DROP FOREIGN KEY FK_2F6C9DCEDD78CB46');
        $this->addSql('ALTER TABLE restaurant_employee_role_map DROP FOREIGN KEY FK_2F6C9DCED60322AC');
        $this->addSql('DROP TABLE restaurant_employee');
        $this->addSql('DROP TABLE restaurant_employee_role_map');
        $this->addSql('DROP TABLE restaurant_role');
    }
}
