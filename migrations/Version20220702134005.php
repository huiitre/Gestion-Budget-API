<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220702134005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fuel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tessence (id INT AUTO_INCREMENT NOT NULL, fuel_id INT NOT NULL, vehicle_id INT NOT NULL, km_travelled DOUBLE PRECISION NOT NULL, price_liter DOUBLE PRECISION NOT NULL, tank DOUBLE PRECISION NOT NULL, INDEX IDX_9ACB8A9997C79677 (fuel_id), INDEX IDX_9ACB8A99545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tessence ADD CONSTRAINT FK_9ACB8A9997C79677 FOREIGN KEY (fuel_id) REFERENCES fuel (id)');
        $this->addSql('ALTER TABLE tessence ADD CONSTRAINT FK_9ACB8A99545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE transaction ADD t_essence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CBFAE8B7 FOREIGN KEY (t_essence_id) REFERENCES tessence (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_723705D1CBFAE8B7 ON transaction (t_essence_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tessence DROP FOREIGN KEY FK_9ACB8A9997C79677');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1CBFAE8B7');
        $this->addSql('ALTER TABLE tessence DROP FOREIGN KEY FK_9ACB8A99545317D1');
        $this->addSql('DROP TABLE fuel');
        $this->addSql('DROP TABLE tessence');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP INDEX UNIQ_723705D1CBFAE8B7 ON transaction');
        $this->addSql('ALTER TABLE transaction DROP t_essence_id');
    }
}
