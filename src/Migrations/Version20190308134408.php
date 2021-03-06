<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190308134408
 * @package DoctrineMigrations
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190308134408 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rent_release (id INT AUTO_INCREMENT NOT NULL, rent_release_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(19) NOT NULL, INDEX IDX_7F6B786D1BC8D612 (rent_release_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rent_release ADD CONSTRAINT FK_7F6B786D1BC8D612 FOREIGN KEY (rent_release_id) REFERENCES lessee (id)');
        $this->addSql('ALTER TABLE rent_release ADD date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE rent_release ADD property_name VARCHAR(255) NOT NULL, ADD lessee_name VARCHAR(511) NOT NULL');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rent_release');
    }
}
