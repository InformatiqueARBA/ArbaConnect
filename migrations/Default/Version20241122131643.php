<?php

declare(strict_types=1);

namespace DoctrineMigrations\Default;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241122131643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE OrderDetail ADD orderNumber VARCHAR(6) NOT NULL, ADD lineNumber VARCHAR(3) NOT NULL, ADD supplierOrderNumber VARCHAR(6) DEFAULT NULL, ADD supplierConfirmation VARCHAR(3) DEFAULT NULL, ADD lineType VARCHAR(6) NOT NULL, ADD orderDate DATE NOT NULL, ADD receptionDate DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE OrderDetail DROP orderNumber, DROP lineNumber, DROP supplierOrderNumber, DROP supplierConfirmation, DROP lineType, DROP orderDate, DROP receptionDate');
    }
}
