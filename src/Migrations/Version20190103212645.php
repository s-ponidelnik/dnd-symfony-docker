<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103212645 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spell ADD identifier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE character_class ADD identifier VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE spell_school CHANGE name identifier VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE character_class DROP identifier');
        $this->addSql('ALTER TABLE spell DROP identifier');
        $this->addSql('ALTER TABLE spell_school CHANGE identifier name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
