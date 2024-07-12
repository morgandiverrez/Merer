<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326203350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE editor CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5FA5418CB');
        $this->addSql('DROP INDEX IDX_6DC044C5FA5418CB ON `group`');
        $this->addSql('ALTER TABLE `group` CHANGE contains_id include_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5EA0ECADF FOREIGN KEY (include_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_6DC044C5EA0ECADF ON `group` (include_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE editor CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5EA0ECADF');
        $this->addSql('DROP INDEX IDX_6DC044C5EA0ECADF ON `group`');
        $this->addSql('ALTER TABLE `group` CHANGE include_id contains_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5FA5418CB FOREIGN KEY (contains_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_6DC044C5FA5418CB ON `group` (contains_id)');
    }
}
