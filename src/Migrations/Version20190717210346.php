<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190717210346 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_admin BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE groups_user (group_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_A4C98D39FE54D947 ON groups_user (group_id)');
        $this->addSql('CREATE INDEX IDX_A4C98D39A76ED395 ON groups_user (user_id)');
        $this->addSql('CREATE TABLE groups_audit (id INTEGER NOT NULL, rev INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL, is_admin BOOLEAN DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev))');
        $this->addSql('CREATE INDEX rev_9790a06851c948e4a63d30a8d3b619c1_idx ON groups_audit (rev)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_admin BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON user (name)');
        $this->addSql('CREATE TABLE user_audit (id INTEGER NOT NULL, rev INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, is_admin BOOLEAN DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev))');
        $this->addSql('CREATE INDEX rev_e06395edc291d0719bee26fd39a32e8a_idx ON user_audit (rev)');
        $this->addSql('CREATE TABLE revisions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, timestamp DATETIME NOT NULL, username VARCHAR(255) DEFAULT NULL)');
    }

    public function down(Schema $schema) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE groups_user');
        $this->addSql('DROP TABLE groups_audit');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_audit');
        $this->addSql('DROP TABLE revisions');
    }
}
