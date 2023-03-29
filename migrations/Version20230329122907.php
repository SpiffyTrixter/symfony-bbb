<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230329122907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE custom_car (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(180) NOT NULL, type VARCHAR(180) NOT NULL, red INT NOT NULL, blue INT NOT NULL, green VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DA841DDC7E3C61F9 ON custom_car (owner_id)');
        $this->addSql('ALTER TABLE custom_car ADD CONSTRAINT FK_DA841DDC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE custom_car DROP CONSTRAINT FK_DA841DDC7E3C61F9');
        $this->addSql('DROP TABLE custom_car');
    }
}
