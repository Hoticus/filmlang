<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220828134136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Film [id, tmdbId, defaultLanguageVote, languageVotes], FilmLanguageVote [id, film, votedUser, vote]. Add fields to User [filmLanguageVotes]';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE film_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE film_language_vote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE film (id INT NOT NULL, tmdb_id INT NOT NULL, default_language_vote SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE film_language_vote (id INT NOT NULL, film_id INT NOT NULL, voted_user_id INT NOT NULL, vote SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AA5E572A567F5183 ON film_language_vote (film_id)');
        $this->addSql('CREATE INDEX IDX_AA5E572A811DC5CE ON film_language_vote (voted_user_id)');
        $this->addSql('ALTER TABLE film_language_vote ADD CONSTRAINT FK_AA5E572A567F5183 FOREIGN KEY (film_id) REFERENCES film (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE film_language_vote ADD CONSTRAINT FK_AA5E572A811DC5CE FOREIGN KEY (voted_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film_language_vote DROP CONSTRAINT FK_AA5E572A567F5183');
        $this->addSql('DROP SEQUENCE film_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE film_language_vote_id_seq CASCADE');
        $this->addSql('DROP TABLE film');
        $this->addSql('DROP TABLE film_language_vote');
    }
}
