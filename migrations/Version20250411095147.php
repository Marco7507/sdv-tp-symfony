<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411095147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE payment (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_42c84955d1e63cd1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955D1E63CD1 ON reservation (insurance_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payment
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C84955D1E63CD1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_42c84955d1e63cd1 ON reservation (insurance_id)
        SQL);
    }
}
