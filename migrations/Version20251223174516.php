<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223174516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reviewer_student DROP CONSTRAINT fk_7635e72a70574616');
        $this->addSql('ALTER TABLE reviewer_student DROP CONSTRAINT fk_7635e72acb944f1a');
        $this->addSql('DROP TABLE reviewer_student');
        $this->addSql('ALTER TABLE direction ADD reviewer_id INT NOT NULL');
        $this->addSql('ALTER TABLE direction ADD CONSTRAINT FK_3E4AD1B370574616 FOREIGN KEY (reviewer_id) REFERENCES reviewer (id) NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3E4AD1B370574616 ON direction (reviewer_id)');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT fk_3bae0aa770574616');
        $this->addSql('DROP INDEX idx_3bae0aa770574616');
        $this->addSql('ALTER TABLE event DROP reviewer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reviewer_student (reviewer_id INT NOT NULL, student_id INT NOT NULL, PRIMARY KEY (reviewer_id, student_id))');
        $this->addSql('CREATE INDEX idx_7635e72acb944f1a ON reviewer_student (student_id)');
        $this->addSql('CREATE INDEX idx_7635e72a70574616 ON reviewer_student (reviewer_id)');
        $this->addSql('ALTER TABLE reviewer_student ADD CONSTRAINT fk_7635e72a70574616 FOREIGN KEY (reviewer_id) REFERENCES reviewer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reviewer_student ADD CONSTRAINT fk_7635e72acb944f1a FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE direction DROP CONSTRAINT FK_3E4AD1B370574616');
        $this->addSql('DROP INDEX UNIQ_3E4AD1B370574616');
        $this->addSql('ALTER TABLE direction DROP reviewer_id');
        $this->addSql('ALTER TABLE event ADD reviewer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT fk_3bae0aa770574616 FOREIGN KEY (reviewer_id) REFERENCES reviewer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3bae0aa770574616 ON event (reviewer_id)');
    }
}
