<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210228194643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_generated_cost DROP FOREIGN KEY FK_B1334433EF40789');
        $this->addSql('DROP TABLE cost_date');
        $this->addSql('DROP INDEX IDX_B1334433EF40789 ON daily_generated_cost');
        $this->addSql('ALTER TABLE daily_generated_cost DROP cost_day_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cost_date (id INT AUTO_INCREMENT NOT NULL, day DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE daily_generated_cost ADD cost_day_id INT NOT NULL');
        $this->addSql('ALTER TABLE daily_generated_cost ADD CONSTRAINT FK_B1334433EF40789 FOREIGN KEY (cost_day_id) REFERENCES cost_date (id)');
        $this->addSql('CREATE INDEX IDX_B1334433EF40789 ON daily_generated_cost (cost_day_id)');
    }
}
