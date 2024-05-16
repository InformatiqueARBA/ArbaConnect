<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516131239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE corporation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, corporation_id INT NOT NULL, order_number_id VARCHAR(30) NOT NULL, order_status VARCHAR(20) NOT NULL, reference VARCHAR(50) NOT NULL, order_date DATE NOT NULL, delivery_date DATE NOT NULL, type VARCHAR(20) NOT NULL, seller VARCHAR(80) NOT NULL, comment VARCHAR(60) DEFAULT NULL, INDEX IDX_F5299398B2685369 (corporation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_detail (id INT AUTO_INCREMENT NOT NULL, command_id INT NOT NULL, order_detail_id VARCHAR(40) NOT NULL, item_number INT NOT NULL, label VARCHAR(100) NOT NULL, quantity DOUBLE PRECISION NOT NULL, ora_quantity DOUBLE PRECISION NOT NULL, unity VARCHAR(20) NOT NULL, oder_delivery_date DATE DEFAULT NULL, comment VARCHAR(500) DEFAULT NULL, INDEX IDX_ED896F4633E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_log (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, corporation_id INT NOT NULL, login VARCHAR(20) NOT NULL, profil VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(50) NOT NULL, first_name VARCHAR(40) NOT NULL, last_name VARCHAR(40) NOT NULL, INDEX IDX_8D93D649B2685369 (corporation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
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
        $this->addSql('DROP TABLE order_log');
        $this->addSql('DROP TABLE user');
    }
}
