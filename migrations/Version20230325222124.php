<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325222124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, mail VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, adress VARCHAR(255) DEFAULT NULL, description VARCHAR(1024) DEFAULT NULL, UNIQUE INDEX UNIQ_CCF1F1BAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, contains_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_6DC044C5FA5418CB (contains_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, editor_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(2048) DEFAULT NULL, visible TINYINT(1) NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_5A8A6C8D6995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_group (post_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_FADBC82A4B89032C (post_id), INDEX IDX_FADBC82AFE54D947 (group_id), PRIMARY KEY(post_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE editor ADD CONSTRAINT FK_CCF1F1BAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5FA5418CB FOREIGN KEY (contains_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D6995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id)');
        $this->addSql('ALTER TABLE post_group ADD CONSTRAINT FK_FADBC82A4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_group ADD CONSTRAINT FK_FADBC82AFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_report_route_line ADD start VARCHAR(255) DEFAULT NULL, ADD end VARCHAR(255) DEFAULT NULL, ADD distance DOUBLE PRECISION DEFAULT NULL, ADD travel_mean VARCHAR(255) DEFAULT NULL, DROP trajet, DROP statut, DROP mode_de_transport, DROP nombre_occupant');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE editor DROP FOREIGN KEY FK_CCF1F1BAA76ED395');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5FA5418CB');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D6995AC4C');
        $this->addSql('ALTER TABLE post_group DROP FOREIGN KEY FK_FADBC82A4B89032C');
        $this->addSql('ALTER TABLE post_group DROP FOREIGN KEY FK_FADBC82AFE54D947');
        $this->addSql('DROP TABLE editor');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_group');
        $this->addSql('ALTER TABLE expense_report_route_line ADD trajet VARCHAR(255) DEFAULT NULL, ADD statut VARCHAR(255) DEFAULT NULL, ADD mode_de_transport VARCHAR(255) DEFAULT NULL, ADD nombre_occupant INT DEFAULT NULL, DROP start, DROP end, DROP distance, DROP travel_mean');
    }
}
