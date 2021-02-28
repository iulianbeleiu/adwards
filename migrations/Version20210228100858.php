<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210228100858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE daily_generated_cost (id INT AUTO_INCREMENT NOT NULL, cost_day_id INT NOT NULL, time TIME NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_B1334433EF40789 (cost_day_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE daily_generated_cost ADD CONSTRAINT FK_B1334433EF40789 FOREIGN KEY (cost_day_id) REFERENCES cost_date (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE daily_generated_cost');
    }
}
