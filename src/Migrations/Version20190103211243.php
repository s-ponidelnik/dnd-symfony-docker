<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103211243 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE spell ADD name_en VARCHAR(255) NOT NULL, ADD name_ru VARCHAR(255) DEFAULT NULL, ADD description_en VARCHAR(255) DEFAULT NULL, ADD description_ru VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE character_class ADD name_en VARCHAR(255) NOT NULL, ADD name_ru VARCHAR(255) DEFAULT NULL, ADD description_en VARCHAR(255) DEFAULT NULL, ADD description_ru VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE spell_school ADD name_en VARCHAR(255) NOT NULL, ADD name_ru VARCHAR(255) DEFAULT NULL, ADD description_en VARCHAR(255) DEFAULT NULL, ADD description_ru VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE character_class DROP name_en, DROP name_ru, DROP description_en, DROP description_ru');
        $this->addSql('ALTER TABLE spell DROP name_en, DROP name_ru, DROP description_en, DROP description_ru');
        $this->addSql('ALTER TABLE spell_school DROP name_en, DROP name_ru, DROP description_en, DROP description_ru');
    }
}
