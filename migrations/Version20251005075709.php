<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005075709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sync_job ADD availabled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE source source VARCHAR(64) NOT NULL');
        $this->addSql('CREATE INDEX IDX_4596994B8CDE5729 ON sync_job (type)');
        $this->addSql('CREATE INDEX IDX_4596994B232D562B ON sync_job (object_id)');
        $this->addSql('CREATE INDEX IDX_4596994B5F8A7F73 ON sync_job (source)');
        $this->addSql('CREATE INDEX IDX_4596994B9DC696CE ON sync_job (tries)');
        $this->addSql('CREATE INDEX IDX_4596994B62A6DC27 ON sync_job (priority)');
        $this->addSql('CREATE INDEX IDX_4596994BC73F5950 ON sync_job (availabled_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_4596994B8CDE5729 ON sync_job');
        $this->addSql('DROP INDEX IDX_4596994B232D562B ON sync_job');
        $this->addSql('DROP INDEX IDX_4596994B5F8A7F73 ON sync_job');
        $this->addSql('DROP INDEX IDX_4596994B9DC696CE ON sync_job');
        $this->addSql('DROP INDEX IDX_4596994B62A6DC27 ON sync_job');
        $this->addSql('DROP INDEX IDX_4596994BC73F5950 ON sync_job');
        $this->addSql('ALTER TABLE sync_job DROP availabled_at, CHANGE source source VARCHAR(64) DEFAULT NULL');
    }
}
