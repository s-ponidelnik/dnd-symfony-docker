<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103223136 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE spell_source (spell_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_E72B8866479EC90D (spell_id), INDEX IDX_E72B8866953C1C61 (source_id), PRIMARY KEY(spell_id, source_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE spell_source ADD CONSTRAINT FK_E72B8866479EC90D FOREIGN KEY (spell_id) REFERENCES spell (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE spell_source ADD CONSTRAINT FK_E72B8866953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE spell DROP FOREIGN KEY FK_D03FCD8D953C1C61');
        $this->addSql('DROP INDEX IDX_D03FCD8D953C1C61 ON spell');
        $this->addSql('ALTER TABLE spell DROP source_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE spell_source');
        $this->addSql('ALTER TABLE spell ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE spell ADD CONSTRAINT FK_D03FCD8D953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D03FCD8D953C1C61 ON spell (source_id)');
    }
}
