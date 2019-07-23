<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180103131643 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('sqlite' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_CBE5A331D94388BD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, serie_id, title, description, index_in_serie FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER NOT NULL, serie_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, index_in_serie INTEGER DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_CBE5A331D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO book (id, serie_id, title, description, index_in_serie) SELECT id, serie_id, title, description, index_in_serie FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A331D94388BD ON book (serie_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__job AS SELECT id, tanslation_key FROM job');
        $this->addSql('DROP TABLE job');
        $this->addSql('CREATE TABLE job (id INTEGER NOT NULL, tanslation_key VARCHAR(256) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO job (id, tanslation_key) SELECT id, tanslation_key FROM __temp__job');
        $this->addSql('DROP TABLE __temp__job');
        $this->addSql('DROP INDEX IDX_9A80042B16A2B381');
        $this->addSql('DROP INDEX IDX_9A80042BF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_creation AS SELECT id, book_id, author_id, role FROM project_book_creation');
        $this->addSql('DROP TABLE project_book_creation');
        $this->addSql('CREATE TABLE project_book_creation (id INTEGER NOT NULL, book_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, job_id INTEGER DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_9A80042BBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9A80042B16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9A80042BF675F31B FOREIGN KEY (author_id) REFERENCES author (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project_book_creation (id, book_id, author_id, job_id) SELECT id, book_id, author_id, role FROM __temp__project_book_creation');
        $this->addSql('DROP TABLE __temp__project_book_creation');
        $this->addSql('CREATE INDEX IDX_9A80042B16A2B381 ON project_book_creation (book_id)');
        $this->addSql('CREATE INDEX IDX_9A80042BF675F31B ON project_book_creation (author_id)');
        $this->addSql('CREATE INDEX IDX_9A80042BBE04EA9 ON project_book_creation (job_id)');
        $this->addSql('DROP INDEX IDX_1116D4EA6995AC4C');
        $this->addSql('DROP INDEX IDX_1116D4EA16A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_edition AS SELECT id, editor_id, book_id, publication_date, collection, isbn FROM project_book_edition');
        $this->addSql('DROP TABLE project_book_edition');
        $this->addSql('CREATE TABLE project_book_edition (id INTEGER NOT NULL, editor_id INTEGER DEFAULT NULL, book_id INTEGER DEFAULT NULL, publication_date DATE DEFAULT \'now()\', collection VARCHAR(255) DEFAULT NULL COLLATE BINARY, isbn VARCHAR(255) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_1116D4EA6995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1116D4EA16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project_book_edition (id, editor_id, book_id, publication_date, collection, isbn) SELECT id, editor_id, book_id, publication_date, collection, isbn FROM __temp__project_book_edition');
        $this->addSql('DROP TABLE __temp__project_book_edition');
        $this->addSql('CREATE INDEX IDX_1116D4EA6995AC4C ON project_book_edition (editor_id)');
        $this->addSql('CREATE INDEX IDX_1116D4EA16A2B381 ON project_book_edition (book_id)');
        $this->addSql('DROP INDEX IDX_794381C616A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, book_id, rating, body, username, publication_date FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER NOT NULL, book_id INTEGER DEFAULT NULL, rating INTEGER DEFAULT NULL, body CLOB DEFAULT NULL COLLATE BINARY, username VARCHAR(512) DEFAULT NULL COLLATE BINARY, publication_date DATETIME DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_794381C616A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO review (id, book_id, rating, body, username, publication_date) SELECT id, book_id, rating, body, username, publication_date FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C616A2B381 ON review (book_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('sqlite' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_CBE5A331D94388BD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, serie_id, title, description, index_in_serie FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER NOT NULL, serie_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, index_in_serie INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO book (id, serie_id, title, description, index_in_serie) SELECT id, serie_id, title, description, index_in_serie FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A331D94388BD ON book (serie_id)');
        $this->addSql('ALTER TABLE job ADD COLUMN role INTEGER UNSIGNED DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_9A80042BBE04EA9');
        $this->addSql('DROP INDEX IDX_9A80042B16A2B381');
        $this->addSql('DROP INDEX IDX_9A80042BF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_creation AS SELECT id, job_id, book_id, author_id FROM project_book_creation');
        $this->addSql('DROP TABLE project_book_creation');
        $this->addSql('CREATE TABLE project_book_creation (id INTEGER NOT NULL, book_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, role INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO project_book_creation (id, role, book_id, author_id) SELECT id, job_id, book_id, author_id FROM __temp__project_book_creation');
        $this->addSql('DROP TABLE __temp__project_book_creation');
        $this->addSql('CREATE INDEX IDX_9A80042B16A2B381 ON project_book_creation (book_id)');
        $this->addSql('CREATE INDEX IDX_9A80042BF675F31B ON project_book_creation (author_id)');
        $this->addSql('DROP INDEX IDX_1116D4EA6995AC4C');
        $this->addSql('DROP INDEX IDX_1116D4EA16A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_edition AS SELECT id, editor_id, book_id, publication_date, collection, isbn FROM project_book_edition');
        $this->addSql('DROP TABLE project_book_edition');
        $this->addSql('CREATE TABLE project_book_edition (id INTEGER NOT NULL, editor_id INTEGER DEFAULT NULL, book_id INTEGER DEFAULT NULL, publication_date DATE DEFAULT \'now()\', collection VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO project_book_edition (id, editor_id, book_id, publication_date, collection, isbn) SELECT id, editor_id, book_id, publication_date, collection, isbn FROM __temp__project_book_edition');
        $this->addSql('DROP TABLE __temp__project_book_edition');
        $this->addSql('CREATE INDEX IDX_1116D4EA6995AC4C ON project_book_edition (editor_id)');
        $this->addSql('CREATE INDEX IDX_1116D4EA16A2B381 ON project_book_edition (book_id)');
        $this->addSql('DROP INDEX IDX_794381C616A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, book_id, rating, body, username, publication_date FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER NOT NULL, book_id INTEGER DEFAULT NULL, rating INTEGER DEFAULT NULL, body CLOB DEFAULT NULL, username VARCHAR(512) DEFAULT NULL, publication_date DATETIME DEFAULT \'now()\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO review (id, book_id, rating, body, username, publication_date) SELECT id, book_id, rating, body, username, publication_date FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C616A2B381 ON review (book_id)');
    }
}
