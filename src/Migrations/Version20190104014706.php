<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190104014706 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spell ADD area_type INT DEFAULT NULL, ADD area_size INT DEFAULT NULL, ADD range_type INT DEFAULT NULL, ADD area_size_type INT DEFAULT NULL, CHANGE range_distance range_distance INT DEFAULT NULL, CHANGE somatic_component somatic_component TINYINT(1) NOT NULL, CHANGE casting_time_type casting_time_type INT DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spell DROP area_type, DROP area_size, DROP range_type, DROP area_size_type, CHANGE range_distance range_distance INT NOT NULL, CHANGE somatic_component somatic_component TINYINT(1) DEFAULT NULL, CHANGE casting_time_type casting_time_type INT NOT NULL, CHANGE duration duration INT NOT NULL');
    }
}
