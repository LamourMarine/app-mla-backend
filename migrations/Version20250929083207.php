<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929083207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_order ADD customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A39395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3B1CE6A39395C3F3 ON customer_order (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer_order DROP CONSTRAINT FK_3B1CE6A39395C3F3');
        $this->addSql('DROP INDEX IDX_3B1CE6A39395C3F3');
        $this->addSql('ALTER TABLE customer_order DROP customer_id');
    }
}
