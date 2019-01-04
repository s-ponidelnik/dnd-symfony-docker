<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103211838 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spell CHANGE description_en description_en LONGTEXT DEFAULT NULL, CHANGE description_ru description_ru LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE character_class CHANGE description_en description_en LONGTEXT DEFAULT NULL, CHANGE description_ru description_ru LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE spell_school CHANGE description_en description_en LONGTEXT DEFAULT NULL, CHANGE description_ru description_ru LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE character_class CHANGE description_en description_en VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE description_ru description_ru VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE spell CHANGE description_en description_en VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE description_ru description_ru VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE spell_school CHANGE description_en description_en VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE description_ru description_ru VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
