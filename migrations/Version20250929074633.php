<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929074633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer_order (id SERIAL NOT NULL, number VARCHAR(255) NOT NULL, total NUMERIC(10, 2) NOT NULL, order_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN customer_order.order_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE order_line (id SERIAL NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description_product VARCHAR(255) NOT NULL, image_product VARCHAR(255) DEFAULT NULL, is_bio BOOLEAN NOT NULL, price NUMERIC(10, 2) NOT NULL, availability BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE unit (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, phone_number VARCHAR(30) NOT NULL, structure_names VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('DROP TABLE order_line');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE "user"');
    }
}
