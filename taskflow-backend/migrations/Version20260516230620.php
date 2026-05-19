<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260516230620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_58562B477E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE board_member (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(255) NOT NULL, joined_at DATETIME NOT NULL, boardboard_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_DCFABEDFDBBA9EF7 (boardboard_id), INDEX IDX_DCFABEDFA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, due_date DATE DEFAULT NULL, position INT NOT NULL, created_at DATETIME NOT NULL, board_column_id INT DEFAULT NULL, assigned_to_id INT NOT NULL, INDEX IDX_161498D3CA372FE (board_column_id), INDEX IDX_161498D3F4BD7827 (assigned_to_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE checklist_item (id INT AUTO_INCREMENT NOT NULL, content VARCHAR(255) NOT NULL, is_done TINYINT NOT NULL, position VARCHAR(255) NOT NULL, card_id INT DEFAULT NULL, INDEX IDX_99EB20F94ACC9A20 (card_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `column` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, board_id INT DEFAULT NULL, INDEX IDX_7D53877EE7EC5785 (board_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comment_label (comment_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_A73DC819F8697D13 (comment_id), INDEX IDX_A73DC81933B92F39 (label_id), PRIMARY KEY (comment_id, label_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, board_id INT DEFAULT NULL, INDEX IDX_EA750E8E7EC5785 (board_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B477E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE board_member ADD CONSTRAINT FK_DCFABEDFDBBA9EF7 FOREIGN KEY (boardboard_id) REFERENCES board (id)');
        $this->addSql('ALTER TABLE board_member ADD CONSTRAINT FK_DCFABEDFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3CA372FE FOREIGN KEY (board_column_id) REFERENCES `column` (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE checklist_item ADD CONSTRAINT FK_99EB20F94ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE `column` ADD CONSTRAINT FK_7D53877EE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('ALTER TABLE comment_label ADD CONSTRAINT FK_A73DC819F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment_label ADD CONSTRAINT FK_A73DC81933B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE label ADD CONSTRAINT FK_EA750E8E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board DROP FOREIGN KEY FK_58562B477E3C61F9');
        $this->addSql('ALTER TABLE board_member DROP FOREIGN KEY FK_DCFABEDFDBBA9EF7');
        $this->addSql('ALTER TABLE board_member DROP FOREIGN KEY FK_DCFABEDFA76ED395');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3CA372FE');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3F4BD7827');
        $this->addSql('ALTER TABLE checklist_item DROP FOREIGN KEY FK_99EB20F94ACC9A20');
        $this->addSql('ALTER TABLE `column` DROP FOREIGN KEY FK_7D53877EE7EC5785');
        $this->addSql('ALTER TABLE comment_label DROP FOREIGN KEY FK_A73DC819F8697D13');
        $this->addSql('ALTER TABLE comment_label DROP FOREIGN KEY FK_A73DC81933B92F39');
        $this->addSql('ALTER TABLE label DROP FOREIGN KEY FK_EA750E8E7EC5785');
        $this->addSql('DROP TABLE board');
        $this->addSql('DROP TABLE board_member');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE checklist_item');
        $this->addSql('DROP TABLE `column`');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE comment_label');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
