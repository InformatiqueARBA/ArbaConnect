<?php

declare(strict_types=1);

namespace DoctrineMigrations\Customer;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218102956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD nom_chantier VARCHAR(255) DEFAULT NULL, ADD ADR1_chantier VARCHAR(255) DEFAULT NULL, ADD ADR2_chantier VARCHAR(255) DEFAULT NULL, ADD ADR3_chantier VARCHAR(255) DEFAULT NULL, ADD CP_chantier VARCHAR(255) DEFAULT NULL, ADD VIL_chantier VARCHAR(255) DEFAULT NULL, ADD nom_siege_social VARCHAR(255) DEFAULT NULL, ADD ADR1_siege_social VARCHAR(255) DEFAULT NULL, ADD ADR2_siege_social VARCHAR(255) DEFAULT NULL, ADD ADR3_siege_social VARCHAR(255) DEFAULT NULL, ADD CP_siege_social VARCHAR(255) DEFAULT NULL, ADD VIL_siege_social VARCHAR(255) DEFAULT NULL, CHANGE partialDelivery partialDelivery TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP nom_chantier, DROP ADR1_chantier, DROP ADR2_chantier, DROP ADR3_chantier, DROP CP_chantier, DROP VIL_chantier, DROP nom_siege_social, DROP ADR1_siege_social, DROP ADR2_siege_social, DROP ADR3_siege_social, DROP CP_siege_social, DROP VIL_siege_social, CHANGE partialDelivery partialDelivery TINYINT(1) NOT NULL');
    }
}
