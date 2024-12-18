<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218150249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE grades (id INT AUTO_INCREMENT NOT NULL, student_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, value INT NOT NULL, INDEX IDX_3AE36110CB944F1A (student_id), INDEX IDX_3AE3611023EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subjects (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_AB25991741807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1483A5E9D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110CB944F1A FOREIGN KEY (student_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE3611023EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)');
        $this->addSql('ALTER TABLE subjects ADD CONSTRAINT FK_AB25991741807E1D FOREIGN KEY (teacher_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110CB944F1A');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE3611023EDC87');
        $this->addSql('ALTER TABLE subjects DROP FOREIGN KEY FK_AB25991741807E1D');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9D60322AC');
        $this->addSql('DROP TABLE grades');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE subjects');
        $this->addSql('DROP TABLE users');
    }
}
