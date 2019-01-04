<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103193404 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

//        $this->addSql('ALTER TABLE spell_school ADD source_id INT NOT NULL');
        $this->addSql('ALTER TABLE spell_school ADD CONSTRAINT FK_413F2DAE953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('CREATE INDEX IDX_413F2DAE953C1C61 ON spell_school (source_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spell_school DROP FOREIGN KEY FK_413F2DAE953C1C61');
        $this->addSql('DROP INDEX IDX_413F2DAE953C1C61 ON spell_school');
        $this->addSql('ALTER TABLE spell_school DROP source_id');
    }
}
