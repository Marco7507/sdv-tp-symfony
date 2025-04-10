<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411075226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE insurance DROP CONSTRAINT fk_640eaf4cb83297e7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_640eaf4cb83297e7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE insurance DROP reservation_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD insurance_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D1E63CD1 FOREIGN KEY (insurance_id) REFERENCES insurance (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_42C84955D1E63CD1 ON reservation (insurance_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE insurance ADD reservation_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE insurance ADD CONSTRAINT fk_640eaf4cb83297e7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_640eaf4cb83297e7 ON insurance (reservation_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP CONSTRAINT FK_42C84955D1E63CD1
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_42C84955D1E63CD1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP insurance_id
        SQL);
    }
}
