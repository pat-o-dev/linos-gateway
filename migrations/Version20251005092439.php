<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005092439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP INDEX UNIQ_64C19C1989D9B62, ADD INDEX IDX_64C19C1989D9B62 (slug)');
        $this->addSql('CREATE UNIQUE INDEX uniq_source_source_id ON category (source, source_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_parent_slug_source ON category (parent_id, slug, source)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_source_source_id ON category');
        $this->addSql('DROP INDEX uniq_parent_slug_source ON category');
        $this->addSql('ALTER TABLE category DROP INDEX IDX_64C19C1989D9B62, ADD UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug)');
    }
}
