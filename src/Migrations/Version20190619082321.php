<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190619082321 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre ADD idApi INT NOT NULL');

        $this->addSql('insert into genre (idApi, nom) values(10759, "Action & Adventure")');
        $this->addSql('insert into genre (idApi, nom) values(16, "Animation")');
        $this->addSql('insert into genre (idApi, nom) values(35, "Comedye")');
        $this->addSql('insert into genre (idApi, nom) values(80, "Crime")');
        $this->addSql('insert into genre (idApi, nom) values(99, "Documentary")');
        $this->addSql('insert into genre (idApi, nom) values(18, "Drama")');
        $this->addSql('insert into genre (idApi, nom) values(10751, "Family")');
        $this->addSql('insert into genre (idApi, nom) values(10762, "Kids")');
        $this->addSql('insert into genre (idApi, nom) values(9648, "Mystery")');
        $this->addSql('insert into genre (idApi, nom) values(10763, "News")');
        $this->addSql('insert into genre (idApi, nom) values(10764, "Reality")');
        $this->addSql('insert into genre (idApi, nom) values(10765, "Sci-Fi & Fantasy")');
        $this->addSql('insert into genre (idApi, nom) values(10766, "Soap")');
        $this->addSql('insert into genre (idApi, nom) values(10767, "Talk")');
        $this->addSql('insert into genre (idApi, nom) values(10768, "War & Politics")');
        $this->addSql('insert into genre (idApi, nom) values(37, "Western")');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre DROP idApi');
    }
}
