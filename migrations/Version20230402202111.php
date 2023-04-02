<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402202111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration DROP red');
        $this->addSql('ALTER TABLE configuration DROP blue');
        $this->addSql('ALTER TABLE configuration RENAME COLUMN green TO hex_color');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE configuration ADD red INT NOT NULL');
        $this->addSql('ALTER TABLE configuration ADD blue INT NOT NULL');
        $this->addSql('ALTER TABLE configuration RENAME COLUMN hex_color TO green');
    }
}
