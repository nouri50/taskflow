<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519211719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card_label (card_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_3693A12E4ACC9A20 (card_id), INDEX IDX_3693A12E33B92F39 (label_id), PRIMARY KEY (card_id, label_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE card_label ADD CONSTRAINT FK_3693A12E4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_label ADD CONSTRAINT FK_3693A12E33B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE comment_label');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B477E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX IDX_DCFABEDFDBBA9EF7 ON board_member');
        $this->addSql('ALTER TABLE board_member CHANGE boardboard_id board_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE board_member ADD CONSTRAINT FK_DCFABEDFE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('ALTER TABLE board_member ADD CONSTRAINT FK_DCFABEDFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DCFABEDFE7EC5785 ON board_member (board_id)');
        $this->addSql('ALTER TABLE card ADD priority VARCHAR(50) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE assigned_to_id assigned_to_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3CA372FE FOREIGN KEY (board_column_id) REFERENCES `column` (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE checklist_item ADD CONSTRAINT FK_99EB20F94ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE `column` ADD CONSTRAINT FK_7D53877EE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('ALTER TABLE comment ADD content LONGTEXT NOT NULL, ADD created_at DATETIME NOT NULL, ADD card_id INT DEFAULT NULL, ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9474526C4ACC9A20 ON comment (card_id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('ALTER TABLE label ADD CONSTRAINT FK_EA750E8E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment_label (comment_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_A73DC81933B92F39 (label_id), INDEX IDX_A73DC819F8697D13 (comment_id), PRIMARY KEY (comment_id, label_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE card_label DROP FOREIGN KEY FK_3693A12E4ACC9A20');
        $this->addSql('ALTER TABLE card_label DROP FOREIGN KEY FK_3693A12E33B92F39');
        $this->addSql('DROP TABLE card_label');
        $this->addSql('ALTER TABLE board DROP FOREIGN KEY FK_58562B477E3C61F9');
        $this->addSql('ALTER TABLE board_member DROP FOREIGN KEY FK_DCFABEDFE7EC5785');
        $this->addSql('ALTER TABLE board_member DROP FOREIGN KEY FK_DCFABEDFA76ED395');
        $this->addSql('DROP INDEX IDX_DCFABEDFE7EC5785 ON board_member');
        $this->addSql('ALTER TABLE board_member CHANGE board_id boardboard_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_DCFABEDFDBBA9EF7 ON board_member (boardboard_id)');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3CA372FE');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3F4BD7827');
        $this->addSql('ALTER TABLE card DROP priority, CHANGE description description LONGTEXT NOT NULL, CHANGE assigned_to_id assigned_to_id INT NOT NULL');
        $this->addSql('ALTER TABLE checklist_item DROP FOREIGN KEY FK_99EB20F94ACC9A20');
        $this->addSql('ALTER TABLE `column` DROP FOREIGN KEY FK_7D53877EE7EC5785');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4ACC9A20');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('DROP INDEX IDX_9474526C4ACC9A20 ON comment');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B ON comment');
        $this->addSql('ALTER TABLE comment DROP content, DROP created_at, DROP card_id, DROP author_id');
        $this->addSql('ALTER TABLE label DROP FOREIGN KEY FK_EA750E8E7EC5785');
    }
}
