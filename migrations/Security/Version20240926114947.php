<?php

declare(strict_types=1);

namespace DoctrineMigrations\Security;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926114947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE InventoryArticle (id INT AUTO_INCREMENT NOT NULL, inventoryNumber VARCHAR(6) NOT NULL, warehouse VARCHAR(3) NOT NULL, location VARCHAR(12) DEFAULT NULL, location2 VARCHAR(12) DEFAULT NULL, location3 VARCHAR(12) DEFAULT NULL, articleCode VARCHAR(15) NOT NULL, designation1 VARCHAR(40) NOT NULL, designation2 VARCHAR(40) DEFAULT NULL, lotCode VARCHAR(15) DEFAULT NULL, dimensionType VARCHAR(3) DEFAULT NULL, packaging DOUBLE PRECISION DEFAULT NULL, packagingName VARCHAR(10) DEFAULT NULL, quantityLocation1 DOUBLE PRECISION DEFAULT NULL, quantityLocation2 DOUBLE PRECISION DEFAULT NULL, quantityLocation3 DOUBLE PRECISION DEFAULT NULL, preparationUnit VARCHAR(5) NOT NULL, quantity2Location1 DOUBLE PRECISION DEFAULT NULL, quantity2Location2 DOUBLE PRECISION DEFAULT NULL, quantity2Location3 DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE InventoryArticle');
    }
}
