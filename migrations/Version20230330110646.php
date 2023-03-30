<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330110646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE custom_car_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE configuration (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(180) NOT NULL, type VARCHAR(180) NOT NULL, red INT NOT NULL, blue INT NOT NULL, green VARCHAR(10) NOT NULL, slug VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A5E2A5D7989D9B62 ON configuration (slug)');
        $this->addSql('CREATE INDEX IDX_A5E2A5D77E3C61F9 ON configuration (owner_id)');
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_A5E2A5D77E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE custom_car DROP CONSTRAINT fk_da841ddc7e3c61f9');
        $this->addSql('DROP TABLE custom_car');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE configuration_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE custom_car_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE custom_car (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(180) NOT NULL, type VARCHAR(180) NOT NULL, red INT NOT NULL, blue INT NOT NULL, green VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_da841ddc7e3c61f9 ON custom_car (owner_id)');
        $this->addSql('ALTER TABLE custom_car ADD CONSTRAINT fk_da841ddc7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE configuration DROP CONSTRAINT FK_A5E2A5D77E3C61F9');
        $this->addSql('DROP TABLE configuration');
    }
}
