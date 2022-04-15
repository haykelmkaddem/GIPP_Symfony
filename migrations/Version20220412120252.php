<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220412120252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_C53D045FF347EFB ON image (produit_id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC273DA5256D');
        $this->addSql('DROP INDEX IDX_29A5EC273DA5256D ON produit');
        $this->addSql('ALTER TABLE produit DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FF347EFB');
        $this->addSql('DROP INDEX IDX_C53D045FF347EFB ON image');
        $this->addSql('ALTER TABLE image DROP produit_id');
        $this->addSql('ALTER TABLE produit ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC273DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC273DA5256D ON produit (image_id)');
    }
}
