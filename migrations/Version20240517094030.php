<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517094030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE corporation (id VARCHAR(20) NOT NULL, name VARCHAR(60) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id VARCHAR(30) NOT NULL, corporation_id VARCHAR(20) NOT NULL, order_status VARCHAR(20) NOT NULL, reference VARCHAR(50) NOT NULL, order_date DATE NOT NULL, delivery_date DATE NOT NULL, type VARCHAR(20) NOT NULL, seller VARCHAR(80) NOT NULL, comment VARCHAR(60) DEFAULT NULL, INDEX IDX_F5299398B2685369 (corporation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_detail (id VARCHAR(40) NOT NULL, command_id VARCHAR(30) NOT NULL, item_number INT NOT NULL, label VARCHAR(100) NOT NULL, quantity DOUBLE PRECISION NOT NULL, ora_quantity DOUBLE PRECISION NOT NULL, unity VARCHAR(20) NOT NULL, oder_delivery_date DATE DEFAULT NULL, comment VARCHAR(500) DEFAULT NULL, INDEX IDX_ED896F4633E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id VARCHAR(20) NOT NULL, corporation_id VARCHAR(20) NOT NULL, profil VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(50) NOT NULL, first_name VARCHAR(40) NOT NULL, last_name VARCHAR(40) NOT NULL, INDEX IDX_8D93D649B2685369 (corporation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398B2685369 FOREIGN KEY (corporation_id) REFERENCES corporation (id)');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F4633E1689A FOREIGN KEY (command_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B2685369 FOREIGN KEY (corporation_id) REFERENCES corporation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398B2685369');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F4633E1689A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B2685369');
        $this->addSql('DROP TABLE corporation');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_detail');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
