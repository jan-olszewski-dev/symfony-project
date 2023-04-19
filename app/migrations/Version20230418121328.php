<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230418121328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow sign in by facebook account';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user 
                ADD facebook_sub_id VARCHAR(255) DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user 
                DROP facebook_sub_id
        ');
    }
}
