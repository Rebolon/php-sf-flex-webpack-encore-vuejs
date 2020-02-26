<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226131747 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_CBE5A331D94388BD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, serie_id, title, description, index_in_serie FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, serie_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, index_in_serie INTEGER DEFAULT NULL, CONSTRAINT FK_CBE5A331D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO book (id, serie_id, title, description, index_in_serie) SELECT id, serie_id, title, description, index_in_serie FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A331D94388BD ON book (serie_id)');
        $this->addSql('DROP INDEX IDX_F2F4CE1516A2B381');
        $this->addSql('DROP INDEX IDX_F2F4CE15BAD26311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book_tag AS SELECT book_id, tag_id FROM book_tag');
        $this->addSql('DROP TABLE book_tag');
        $this->addSql('CREATE TABLE book_tag (book_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(book_id, tag_id), CONSTRAINT FK_F2F4CE1516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F2F4CE15BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO book_tag (book_id, tag_id) SELECT book_id, tag_id FROM __temp__book_tag');
        $this->addSql('DROP TABLE __temp__book_tag');
        $this->addSql('CREATE INDEX IDX_F2F4CE1516A2B381 ON book_tag (book_id)');
        $this->addSql('CREATE INDEX IDX_F2F4CE15BAD26311 ON book_tag (tag_id)');
        $this->addSql('DROP INDEX IDX_C5D30D0316A2B381');
        $this->addSql('DROP INDEX IDX_C5D30D0311CE312B');
        $this->addSql('DROP INDEX IDX_C5D30D03434E717A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__loan AS SELECT id, book_id, borrower_id, loaner_id, start_loan, end_loan FROM loan');
        $this->addSql('DROP TABLE loan');
        $this->addSql('CREATE TABLE loan (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_id INTEGER DEFAULT NULL, borrower_id INTEGER DEFAULT NULL, loaner_id INTEGER DEFAULT NULL, start_loan DATETIME NOT NULL, end_loan DATETIME DEFAULT NULL, CONSTRAINT FK_C5D30D0316A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C5D30D0311CE312B FOREIGN KEY (borrower_id) REFERENCES reader (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C5D30D03434E717A FOREIGN KEY (loaner_id) REFERENCES reader (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO loan (id, book_id, borrower_id, loaner_id, start_loan, end_loan) SELECT id, book_id, borrower_id, loaner_id, start_loan, end_loan FROM __temp__loan');
        $this->addSql('DROP TABLE __temp__loan');
        $this->addSql('CREATE INDEX IDX_C5D30D0316A2B381 ON loan (book_id)');
        $this->addSql('CREATE INDEX IDX_C5D30D0311CE312B ON loan (borrower_id)');
        $this->addSql('CREATE INDEX IDX_C5D30D03434E717A ON loan (loaner_id)');
        $this->addSql('DROP INDEX IDX_9A80042BBE04EA9');
        $this->addSql('DROP INDEX IDX_9A80042B16A2B381');
        $this->addSql('DROP INDEX IDX_9A80042BF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_creation AS SELECT id, job_id, book_id, author_id FROM project_book_creation');
        $this->addSql('DROP TABLE project_book_creation');
        $this->addSql('CREATE TABLE project_book_creation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, job_id INTEGER DEFAULT NULL, book_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL, CONSTRAINT FK_9A80042BBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9A80042B16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9A80042BF675F31B FOREIGN KEY (author_id) REFERENCES author (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project_book_creation (id, job_id, book_id, author_id) SELECT id, job_id, book_id, author_id FROM __temp__project_book_creation');
        $this->addSql('DROP TABLE __temp__project_book_creation');
        $this->addSql('CREATE INDEX IDX_9A80042BBE04EA9 ON project_book_creation (job_id)');
        $this->addSql('CREATE INDEX IDX_9A80042B16A2B381 ON project_book_creation (book_id)');
        $this->addSql('CREATE INDEX IDX_9A80042BF675F31B ON project_book_creation (author_id)');
        $this->addSql('DROP INDEX IDX_1116D4EA6995AC4C');
        $this->addSql('DROP INDEX IDX_1116D4EA16A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_edition AS SELECT id, editor_id, book_id, publication_date, collection, isbn FROM project_book_edition');
        $this->addSql('DROP TABLE project_book_edition');
        $this->addSql('CREATE TABLE project_book_edition (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editor_id INTEGER DEFAULT NULL, book_id INTEGER DEFAULT NULL, publication_date DATE DEFAULT NULL, collection VARCHAR(255) DEFAULT NULL COLLATE BINARY, isbn VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_1116D4EA6995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1116D4EA16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project_book_edition (id, editor_id, book_id, publication_date, collection, isbn) SELECT id, editor_id, book_id, publication_date, collection, isbn FROM __temp__project_book_edition');
        $this->addSql('DROP TABLE __temp__project_book_edition');
        $this->addSql('CREATE INDEX IDX_1116D4EA6995AC4C ON project_book_edition (editor_id)');
        $this->addSql('CREATE INDEX IDX_1116D4EA16A2B381 ON project_book_edition (book_id)');
        $this->addSql('DROP INDEX IDX_2A3845F31717D737');
        $this->addSql('DROP INDEX IDX_2A3845F316A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reader_book AS SELECT reader_id, book_id FROM reader_book');
        $this->addSql('DROP TABLE reader_book');
        $this->addSql('CREATE TABLE reader_book (reader_id INTEGER NOT NULL, book_id INTEGER NOT NULL, PRIMARY KEY(reader_id, book_id), CONSTRAINT FK_2A3845F31717D737 FOREIGN KEY (reader_id) REFERENCES reader (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2A3845F316A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reader_book (reader_id, book_id) SELECT reader_id, book_id FROM __temp__reader_book');
        $this->addSql('DROP TABLE __temp__reader_book');
        $this->addSql('CREATE INDEX IDX_2A3845F31717D737 ON reader_book (reader_id)');
        $this->addSql('CREATE INDEX IDX_2A3845F316A2B381 ON reader_book (book_id)');
        $this->addSql('DROP INDEX IDX_794381C616A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, book_id, rating, body, username, publication_date FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_id INTEGER DEFAULT NULL, rating INTEGER NOT NULL, body CLOB DEFAULT NULL COLLATE BINARY, username VARCHAR(512) DEFAULT NULL COLLATE BINARY, publication_date DATETIME NOT NULL, CONSTRAINT FK_794381C616A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO review (id, book_id, rating, body, username, publication_date) SELECT id, book_id, rating, body, username, publication_date FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C616A2B381 ON review (book_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_CBE5A331D94388BD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, serie_id, title, description, index_in_serie FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, serie_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, index_in_serie INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO book (id, serie_id, title, description, index_in_serie) SELECT id, serie_id, title, description, index_in_serie FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A331D94388BD ON book (serie_id)');
        $this->addSql('DROP INDEX IDX_F2F4CE1516A2B381');
        $this->addSql('DROP INDEX IDX_F2F4CE15BAD26311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book_tag AS SELECT book_id, tag_id FROM book_tag');
        $this->addSql('DROP TABLE book_tag');
        $this->addSql('CREATE TABLE book_tag (book_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(book_id, tag_id))');
        $this->addSql('INSERT INTO book_tag (book_id, tag_id) SELECT book_id, tag_id FROM __temp__book_tag');
        $this->addSql('DROP TABLE __temp__book_tag');
        $this->addSql('CREATE INDEX IDX_F2F4CE1516A2B381 ON book_tag (book_id)');
        $this->addSql('CREATE INDEX IDX_F2F4CE15BAD26311 ON book_tag (tag_id)');
        $this->addSql('DROP INDEX IDX_C5D30D0316A2B381');
        $this->addSql('DROP INDEX IDX_C5D30D0311CE312B');
        $this->addSql('DROP INDEX IDX_C5D30D03434E717A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__loan AS SELECT id, book_id, borrower_id, loaner_id, start_loan, end_loan FROM loan');
        $this->addSql('DROP TABLE loan');
        $this->addSql('CREATE TABLE loan (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_id INTEGER DEFAULT NULL, borrower_id INTEGER DEFAULT NULL, loaner_id INTEGER DEFAULT NULL, start_loan DATETIME NOT NULL, end_loan DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO loan (id, book_id, borrower_id, loaner_id, start_loan, end_loan) SELECT id, book_id, borrower_id, loaner_id, start_loan, end_loan FROM __temp__loan');
        $this->addSql('DROP TABLE __temp__loan');
        $this->addSql('CREATE INDEX IDX_C5D30D0316A2B381 ON loan (book_id)');
        $this->addSql('CREATE INDEX IDX_C5D30D0311CE312B ON loan (borrower_id)');
        $this->addSql('CREATE INDEX IDX_C5D30D03434E717A ON loan (loaner_id)');
        $this->addSql('DROP INDEX IDX_9A80042BBE04EA9');
        $this->addSql('DROP INDEX IDX_9A80042B16A2B381');
        $this->addSql('DROP INDEX IDX_9A80042BF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_creation AS SELECT id, job_id, book_id, author_id FROM project_book_creation');
        $this->addSql('DROP TABLE project_book_creation');
        $this->addSql('CREATE TABLE project_book_creation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, job_id INTEGER DEFAULT NULL, book_id INTEGER DEFAULT NULL, author_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO project_book_creation (id, job_id, book_id, author_id) SELECT id, job_id, book_id, author_id FROM __temp__project_book_creation');
        $this->addSql('DROP TABLE __temp__project_book_creation');
        $this->addSql('CREATE INDEX IDX_9A80042BBE04EA9 ON project_book_creation (job_id)');
        $this->addSql('CREATE INDEX IDX_9A80042B16A2B381 ON project_book_creation (book_id)');
        $this->addSql('CREATE INDEX IDX_9A80042BF675F31B ON project_book_creation (author_id)');
        $this->addSql('DROP INDEX IDX_1116D4EA6995AC4C');
        $this->addSql('DROP INDEX IDX_1116D4EA16A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_book_edition AS SELECT id, editor_id, book_id, publication_date, collection, isbn FROM project_book_edition');
        $this->addSql('DROP TABLE project_book_edition');
        $this->addSql('CREATE TABLE project_book_edition (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editor_id INTEGER DEFAULT NULL, book_id INTEGER DEFAULT NULL, publication_date DATE DEFAULT NULL, collection VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO project_book_edition (id, editor_id, book_id, publication_date, collection, isbn) SELECT id, editor_id, book_id, publication_date, collection, isbn FROM __temp__project_book_edition');
        $this->addSql('DROP TABLE __temp__project_book_edition');
        $this->addSql('CREATE INDEX IDX_1116D4EA6995AC4C ON project_book_edition (editor_id)');
        $this->addSql('CREATE INDEX IDX_1116D4EA16A2B381 ON project_book_edition (book_id)');
        $this->addSql('DROP INDEX IDX_2A3845F31717D737');
        $this->addSql('DROP INDEX IDX_2A3845F316A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reader_book AS SELECT reader_id, book_id FROM reader_book');
        $this->addSql('DROP TABLE reader_book');
        $this->addSql('CREATE TABLE reader_book (reader_id INTEGER NOT NULL, book_id INTEGER NOT NULL, PRIMARY KEY(reader_id, book_id))');
        $this->addSql('INSERT INTO reader_book (reader_id, book_id) SELECT reader_id, book_id FROM __temp__reader_book');
        $this->addSql('DROP TABLE __temp__reader_book');
        $this->addSql('CREATE INDEX IDX_2A3845F31717D737 ON reader_book (reader_id)');
        $this->addSql('CREATE INDEX IDX_2A3845F316A2B381 ON reader_book (book_id)');
        $this->addSql('DROP INDEX IDX_794381C616A2B381');
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, book_id, rating, body, username, publication_date FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_id INTEGER DEFAULT NULL, rating INTEGER NOT NULL, body CLOB DEFAULT NULL, username VARCHAR(512) DEFAULT NULL, publication_date DATETIME NOT NULL)');
        $this->addSql('INSERT INTO review (id, book_id, rating, body, username, publication_date) SELECT id, book_id, rating, body, username, publication_date FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C616A2B381 ON review (book_id)');
    }
}
