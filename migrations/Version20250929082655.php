<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929082655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_line ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_line ADD order_ref_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE14584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE1E238517C FOREIGN KEY (order_ref_id) REFERENCES customer_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9CE58EE14584665A ON order_line (product_id)');
        $this->addSql('CREATE INDEX IDX_9CE58EE1E238517C ON order_line (order_ref_id)');
        $this->addSql('ALTER TABLE product ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD unit_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADF8BD700D ON product (unit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_line DROP CONSTRAINT FK_9CE58EE14584665A');
        $this->addSql('ALTER TABLE order_line DROP CONSTRAINT FK_9CE58EE1E238517C');
        $this->addSql('DROP INDEX IDX_9CE58EE14584665A');
        $this->addSql('DROP INDEX IDX_9CE58EE1E238517C');
        $this->addSql('ALTER TABLE order_line DROP product_id');
        $this->addSql('ALTER TABLE order_line DROP order_ref_id');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADF8BD700D');
        $this->addSql('DROP INDEX IDX_D34A04AD12469DE2');
        $this->addSql('DROP INDEX IDX_D34A04ADF8BD700D');
        $this->addSql('ALTER TABLE product DROP category_id');
        $this->addSql('ALTER TABLE product DROP unit_id');
    }
}
