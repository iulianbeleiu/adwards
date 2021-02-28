<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210228195434 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_generated_cost ADD budget_date_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE daily_generated_cost ADD CONSTRAINT FK_B13344337A147C8A FOREIGN KEY (budget_date_id) REFERENCES budget_adjustment_date (id)');
        $this->addSql('CREATE INDEX IDX_B13344337A147C8A ON daily_generated_cost (budget_date_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_generated_cost DROP FOREIGN KEY FK_B13344337A147C8A');
        $this->addSql('DROP INDEX IDX_B13344337A147C8A ON daily_generated_cost');
        $this->addSql('ALTER TABLE daily_generated_cost DROP budget_date_id');
    }
}
