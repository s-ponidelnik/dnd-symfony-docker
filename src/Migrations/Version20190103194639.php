<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103194639 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE spell (id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, source_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, level INT NOT NULL, casting_time INT NOT NULL, is_ritual TINYINT(1) NOT NULL, concentration TINYINT(1) NOT NULL, range_distance INT NOT NULL, verbal_component TINYINT(1) NOT NULL, somatic_component TINYINT(1) DEFAULT NULL, material_components LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_D03FCD8DC32A47EE (school_id), INDEX IDX_D03FCD8D953C1C61 (source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE spell ADD CONSTRAINT FK_D03FCD8DC32A47EE FOREIGN KEY (school_id) REFERENCES spell_school (id)');
        $this->addSql('ALTER TABLE spell ADD CONSTRAINT FK_D03FCD8D953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('ALTER TABLE spell_school ADD CONSTRAINT FK_413F2DAE953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('CREATE INDEX IDX_413F2DAE953C1C61 ON spell_school (source_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE spell');
        $this->addSql('ALTER TABLE spell_school DROP FOREIGN KEY FK_413F2DAE953C1C61');
        $this->addSql('DROP INDEX IDX_413F2DAE953C1C61 ON spell_school');
    }
}
