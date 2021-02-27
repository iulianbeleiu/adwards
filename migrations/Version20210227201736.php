<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210227201736 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget_daily_adjustment (id INT AUTO_INCREMENT NOT NULL, budget_date_id INT NOT NULL, time TIME NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_4D481287A147C8A (budget_date_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE budget_daily_adjustment ADD CONSTRAINT FK_4D481287A147C8A FOREIGN KEY (budget_date_id) REFERENCES budget_adjustment_date (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE budget_daily_adjustment');
    }
}
