<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405123617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0F347EFB ON avis (produit_id)');
        $this->addSql('ALTER TABLE panier ADD produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF2F347EFB ON panier (produit_id)');
        $this->addSql('ALTER TABLE produit ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC273DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC273DA5256D ON produit (image_id)');
        $this->addSql('ALTER TABLE produit_vendus ADD commande_id INT DEFAULT NULL, ADD produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit_vendus ADD CONSTRAINT FK_C55A328182EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE produit_vendus ADD CONSTRAINT FK_C55A3281F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_C55A328182EA2E54 ON produit_vendus (commande_id)');
        $this->addSql('CREATE INDEX IDX_C55A3281F347EFB ON produit_vendus (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0F347EFB');
        $this->addSql('DROP INDEX IDX_8F91ABF0F347EFB ON avis');
        $this->addSql('ALTER TABLE avis DROP produit_id');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2F347EFB');
        $this->addSql('DROP INDEX IDX_24CC0DF2F347EFB ON panier');
        $this->addSql('ALTER TABLE panier DROP produit_id');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC273DA5256D');
        $this->addSql('DROP INDEX IDX_29A5EC273DA5256D ON produit');
        $this->addSql('ALTER TABLE produit DROP image_id');
        $this->addSql('ALTER TABLE produit_vendus DROP FOREIGN KEY FK_C55A328182EA2E54');
        $this->addSql('ALTER TABLE produit_vendus DROP FOREIGN KEY FK_C55A3281F347EFB');
        $this->addSql('DROP INDEX IDX_C55A328182EA2E54 ON produit_vendus');
        $this->addSql('DROP INDEX IDX_C55A3281F347EFB ON produit_vendus');
        $this->addSql('ALTER TABLE produit_vendus DROP commande_id, DROP produit_id');
    }
}
