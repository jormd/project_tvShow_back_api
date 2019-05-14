<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190512203200 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE friends (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, friend_id INT DEFAULT NULL, INDEX IDX_21EE7069A76ED395 (user_id), INDEX IDX_21EE70696A5458E8 (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE friends ADD CONSTRAINT FK_21EE7069A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friends ADD CONSTRAINT FK_21EE70696A5458E8 FOREIGN KEY (friend_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649727ACA70');
        $this->addSql('DROP INDEX IDX_8D93D649727ACA70 ON user');
        $this->addSql('ALTER TABLE user DROP parent_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE friends');
        $this->addSql('ALTER TABLE user ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649727ACA70 FOREIGN KEY (parent_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649727ACA70 ON user (parent_id)');
    }
}
