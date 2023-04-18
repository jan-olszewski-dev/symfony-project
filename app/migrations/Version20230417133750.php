<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230417133750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow sign in by google account';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user 
                ADD google_sub_id VARCHAR(255) DEFAULT NULL,
                CHANGE password password VARCHAR(255) DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user 
                DROP google_sub_id,
                CHANGE password password VARCHAR(255) NOT NULL
        ');
    }
}
