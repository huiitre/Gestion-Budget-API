<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711190606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fuel ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE fuel ADD CONSTRAINT FK_31BD6FE9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_31BD6FE9A76ED395 ON fuel (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fuel DROP FOREIGN KEY FK_31BD6FE9A76ED395');
        $this->addSql('DROP INDEX IDX_31BD6FE9A76ED395 ON fuel');
        $this->addSql('ALTER TABLE fuel DROP user_id');
    }
}
