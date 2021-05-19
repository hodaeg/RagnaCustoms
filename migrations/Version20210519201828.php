<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519201828 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_feedback ADD feedback_parent_id INT DEFAULT NULL, ADD is_moderated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE song_feedback ADD CONSTRAINT FK_79F51210CC3E94CB FOREIGN KEY (feedback_parent_id) REFERENCES song_feedback (id)');
        $this->addSql('CREATE INDEX IDX_79F51210CC3E94CB ON song_feedback (feedback_parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_feedback DROP FOREIGN KEY FK_79F51210CC3E94CB');
        $this->addSql('DROP INDEX IDX_79F51210CC3E94CB ON song_feedback');
        $this->addSql('ALTER TABLE song_feedback DROP feedback_parent_id, DROP is_moderated');
    }
}
