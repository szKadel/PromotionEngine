<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230725171218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF756BF700BD');
        $this->addSql('CREATE TABLE vacation_status (id INT AUTO_INCREMENT NOT NULL, status_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE vacation_request_status');
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF756BF700BD');
        $this->addSql('ALTER TABLE vacation ADD CONSTRAINT FK_E3DADF756BF700BD FOREIGN KEY (status_id) REFERENCES vacation_status (id)');
        $this->addSql('ALTER TABLE vacation_type CHANGE limit_in_days limit_in_days INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF756BF700BD');
        $this->addSql('CREATE TABLE vacation_request_status (id INT AUTO_INCREMENT NOT NULL, status_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE vacation_status');
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF756BF700BD');
        $this->addSql('ALTER TABLE vacation ADD CONSTRAINT FK_E3DADF756BF700BD FOREIGN KEY (status_id) REFERENCES vacation_request_status (id)');
        $this->addSql('ALTER TABLE vacation_type CHANGE limit_in_days limit_in_days VARCHAR(255) NOT NULL');
    }
}
