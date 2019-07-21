<?php /** @noinspection SqlResolve */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190721153937 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT id, name, is_admin FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, is_admin BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO groups (id, name, is_admin) SELECT id, name, is_admin FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F06D39705E237E06 ON groups (name)');
        $this->addSql('DROP INDEX IDX_A4C98D39A76ED395');
        $this->addSql('DROP INDEX IDX_A4C98D39FE54D947');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups_user AS SELECT group_id, user_id FROM groups_user');
        $this->addSql('DROP TABLE groups_user');
        $this->addSql('CREATE TABLE groups_user (group_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(group_id, user_id), CONSTRAINT FK_F0F44878FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0F44878A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO groups_user (group_id, user_id) SELECT group_id, user_id FROM __temp__groups_user');
        $this->addSql('DROP TABLE __temp__groups_user');
        $this->addSql('CREATE INDEX IDX_F0F44878FE54D947 ON groups_user (group_id)');
        $this->addSql('CREATE INDEX IDX_F0F44878A76ED395 ON groups_user (user_id)');
        $this->addSql('DROP INDEX rev_9790a06851c948e4a63d30a8d3b619c1_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups_audit AS SELECT id, rev, name, is_admin, revtype FROM groups_audit');
        $this->addSql('DROP TABLE groups_audit');
        $this->addSql('CREATE TABLE groups_audit (id INTEGER NOT NULL, rev INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE BINARY, is_admin BOOLEAN DEFAULT NULL, revtype VARCHAR(4) NOT NULL COLLATE BINARY, PRIMARY KEY(id, rev))');
        $this->addSql('INSERT INTO groups_audit (id, rev, name, is_admin, revtype) SELECT id, rev, name, is_admin, revtype FROM __temp__groups_audit');
        $this->addSql('DROP TABLE __temp__groups_audit');
        $this->addSql('CREATE INDEX rev_616cb5f64725aff45a823efd88dc30bd_idx ON groups_audit (rev)');
        $this->addSql('DROP INDEX UNIQ_8D93D6495E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, name, password, is_admin FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, password VARCHAR(255) NOT NULL COLLATE BINARY, is_admin BOOLEAN NOT NULL, api_token VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, name, password, is_admin) SELECT id, name, password, is_admin FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON user (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497BA2F5EB ON user (api_token)');
        $this->addSql('ALTER TABLE user_audit ADD COLUMN api_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_F06D39705E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT id, name, is_admin FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_admin BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO groups (id, name, is_admin) SELECT id, name, is_admin FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('DROP INDEX rev_616cb5f64725aff45a823efd88dc30bd_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups_audit AS SELECT id, rev, name, is_admin, revtype FROM groups_audit');
        $this->addSql('DROP TABLE groups_audit');
        $this->addSql('CREATE TABLE groups_audit (id INTEGER NOT NULL, rev INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL, is_admin BOOLEAN DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev))');
        $this->addSql('INSERT INTO groups_audit (id, rev, name, is_admin, revtype) SELECT id, rev, name, is_admin, revtype FROM __temp__groups_audit');
        $this->addSql('DROP TABLE __temp__groups_audit');
        $this->addSql('CREATE INDEX rev_9790a06851c948e4a63d30a8d3b619c1_idx ON groups_audit (rev)');
        $this->addSql('DROP INDEX IDX_F0F44878FE54D947');
        $this->addSql('DROP INDEX IDX_F0F44878A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups_user AS SELECT group_id, user_id FROM groups_user');
        $this->addSql('DROP TABLE groups_user');
        $this->addSql('CREATE TABLE groups_user (group_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(group_id, user_id))');
        $this->addSql('INSERT INTO groups_user (group_id, user_id) SELECT group_id, user_id FROM __temp__groups_user');
        $this->addSql('DROP TABLE __temp__groups_user');
        $this->addSql('CREATE INDEX IDX_A4C98D39A76ED395 ON groups_user (user_id)');
        $this->addSql('CREATE INDEX IDX_A4C98D39FE54D947 ON groups_user (group_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D6495E237E06');
        $this->addSql('DROP INDEX UNIQ_8D93D6497BA2F5EB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, name, password, is_admin FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_admin BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO user (id, name, password, is_admin) SELECT id, name, password, is_admin FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON user (name)');
        $this->addSql('DROP INDEX rev_e06395edc291d0719bee26fd39a32e8a_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_audit AS SELECT id, rev, name, password, is_admin, revtype FROM user_audit');
        $this->addSql('DROP TABLE user_audit');
        $this->addSql('CREATE TABLE user_audit (id INTEGER NOT NULL, rev INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, is_admin BOOLEAN DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev))');
        $this->addSql('INSERT INTO user_audit (id, rev, name, password, is_admin, revtype) SELECT id, rev, name, password, is_admin, revtype FROM __temp__user_audit');
        $this->addSql('DROP TABLE __temp__user_audit');
        $this->addSql('CREATE INDEX rev_e06395edc291d0719bee26fd39a32e8a_idx ON user_audit (rev)');
    }
}
