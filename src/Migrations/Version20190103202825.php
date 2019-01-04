<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190103202825 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE spell_character_class (spell_id INT NOT NULL, character_class_id INT NOT NULL, INDEX IDX_1734AD52479EC90D (spell_id), INDEX IDX_1734AD52B201E281 (character_class_id), PRIMARY KEY(spell_id, character_class_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE spell_character_class ADD CONSTRAINT FK_1734AD52479EC90D FOREIGN KEY (spell_id) REFERENCES spell (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE spell_character_class ADD CONSTRAINT FK_1734AD52B201E281 FOREIGN KEY (character_class_id) REFERENCES character_class (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE spell_character_class');
    }
}
