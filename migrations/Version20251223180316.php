<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223180316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP CONSTRAINT fk_3bae0aa770574616');
        $this->addSql('DROP INDEX idx_3bae0aa770574616');
        $this->addSql('ALTER TABLE event DROP reviewer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD reviewer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT fk_3bae0aa770574616 FOREIGN KEY (reviewer_id) REFERENCES reviewer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3bae0aa770574616 ON event (reviewer_id)');
    }
}
