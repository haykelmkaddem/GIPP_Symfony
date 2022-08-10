<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220703193537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD visibilite TINYINT(1) NOT NULL, ADD nom_ar VARCHAR(255) NOT NULL, ADD nom_it VARCHAR(255) NOT NULL, ADD nom_en VARCHAR(255) NOT NULL, ADD description_it VARCHAR(5000) NOT NULL, ADD description_ar VARCHAR(5000) NOT NULL, ADD description_en VARCHAR(5000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP visibilite, DROP nom_ar, DROP nom_it, DROP nom_en, DROP description_it, DROP description_ar, DROP description_en');
    }
}
