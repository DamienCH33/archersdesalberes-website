<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260909082251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule ADD winter_location VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule ADD summer_location VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule DROP location');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule ADD location VARCHAR(60) DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule DROP winter_location');
        $this->addSql('ALTER TABLE schedule DROP summer_location');
    }
}
