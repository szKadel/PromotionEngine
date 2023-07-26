<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230724183059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vacation ADD type_id INT DEFAULT NULL, ADD status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vacation ADD CONSTRAINT FK_E3DADF75C54C8C93 FOREIGN KEY (type_id) REFERENCES vacation_type (id)');
        $this->addSql('ALTER TABLE vacation ADD CONSTRAINT FK_E3DADF756BF700BD FOREIGN KEY (status_id) REFERENCES vacation_request_status (id)');
        $this->addSql('CREATE INDEX IDX_E3DADF75C54C8C93 ON vacation (type_id)');
        $this->addSql('CREATE INDEX IDX_E3DADF756BF700BD ON vacation (status_id)');
        $this->addSql('ALTER TABLE vacation_type ADD limit_in_days VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF75C54C8C93');
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF756BF700BD');
        $this->addSql('DROP INDEX IDX_E3DADF75C54C8C93 ON vacation');
        $this->addSql('DROP INDEX IDX_E3DADF756BF700BD ON vacation');
        $this->addSql('ALTER TABLE vacation DROP type_id, DROP status_id');
        $this->addSql('ALTER TABLE vacation_type DROP limit_in_days');
    }
}
