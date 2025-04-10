<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250410144646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE insurance (id SERIAL NOT NULL, reservation_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_640EAF4CB83297E7 ON insurance (reservation_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation (id SERIAL NOT NULL, reserved_car_id INT NOT NULL, created_by_id INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, total_price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C849557E6D38E9 ON reservation (reserved_car_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955B03A8386 ON reservation (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE insurance ADD CONSTRAINT FK_640EAF4CB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C849557E6D38E9 FOREIGN KEY (reserved_car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE insurance DROP CONSTRAINT FK_640EAF4CB83297E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP CONSTRAINT FK_42C849557E6D38E9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP CONSTRAINT FK_42C84955B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE insurance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation
        SQL);
    }
}
