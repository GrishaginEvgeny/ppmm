<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223194005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP status');
        $this->addSql('ALTER TABLE event_student DROP CONSTRAINT fk_3274069ccb944f1a');
        $this->addSql('ALTER TABLE event_student DROP CONSTRAINT fk_3274069c71f7e88b');
        $this->addSql('ALTER TABLE event_student ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE event_student ADD CONSTRAINT FK_3274069CCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE event_student ADD CONSTRAINT FK_3274069C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE event_student DROP CONSTRAINT FK_3274069C71F7E88B');
        $this->addSql('ALTER TABLE event_student DROP CONSTRAINT FK_3274069CCB944F1A');
        $this->addSql('ALTER TABLE event_student DROP status');
        $this->addSql('ALTER TABLE event_student ADD CONSTRAINT fk_3274069c71f7e88b FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_student ADD CONSTRAINT fk_3274069ccb944f1a FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
