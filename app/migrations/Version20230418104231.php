<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230418104231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow sign in by linkedIn account';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user 
                ADD linked_in_sub_id VARCHAR(255) DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user 
                DROP linked_in_sub_id
        ');
    }
}
