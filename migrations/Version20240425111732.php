<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425233544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gender (id INT AUTO_INCREMENT NOT NULL, gender VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting (id INT AUTO_INCREMENT NOT NULL, tournament_id INT DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME DEFAULT NULL, score LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', win_condition VARCHAR(3) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F515E13933D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting_team (meeting_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_28115ECD67433D9C (meeting_id), INDEX IDX_28115ECD296CD8AE (team_id), PRIMARY KEY(meeting_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, tournament_id INT DEFAULT NULL, rank_meeting_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, division VARCHAR(100) DEFAULT NULL, rating INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C4E0A61F33D1A3E7 (tournament_id), INDEX IDX_C4E0A61FAA816465 (rank_meeting_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament (id INT AUTO_INCREMENT NOT NULL, sport_id INT DEFAULT NULL, organisator_id INT DEFAULT NULL, gender_rule_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, rules LONGTEXT DEFAULT NULL, is_public TINYINT(1) NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, number_players_per_team INT NOT NULL, type_tournament VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status INT DEFAULT NULL, INDEX IDX_BD5FB8D9AC78BCF8 (sport_id), INDEX IDX_BD5FB8D9FFDD4EC8 (organisator_id), INDEX IDX_BD5FB8D986A676ED (gender_rule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_user (tournament_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BA1E647733D1A3E7 (tournament_id), INDEX IDX_BA1E6477A76ED395 (user_id), PRIMARY KEY(tournament_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, gender_id INT DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, username VARCHAR(100) NOT NULL, birth_date DATE DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8D93D649708A0E0 (gender_id), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username, email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_meeting (user_id INT NOT NULL, meeting_id INT NOT NULL, INDEX IDX_AD18FF33A76ED395 (user_id), INDEX IDX_AD18FF3367433D9C (meeting_id), PRIMARY KEY(user_id, meeting_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E13933D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE meeting_team ADD CONSTRAINT FK_28115ECD67433D9C FOREIGN KEY (meeting_id) REFERENCES meeting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meeting_team ADD CONSTRAINT FK_28115ECD296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FAA816465 FOREIGN KEY (rank_meeting_id) REFERENCES meeting (id)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9FFDD4EC8 FOREIGN KEY (organisator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D986A676ED FOREIGN KEY (gender_rule_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649708A0E0 FOREIGN KEY (gender_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE user_meeting ADD CONSTRAINT FK_AD18FF33A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_meeting ADD CONSTRAINT FK_AD18FF3367433D9C FOREIGN KEY (meeting_id) REFERENCES meeting (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E13933D1A3E7');
        $this->addSql('ALTER TABLE meeting_team DROP FOREIGN KEY FK_28115ECD67433D9C');
        $this->addSql('ALTER TABLE meeting_team DROP FOREIGN KEY FK_28115ECD296CD8AE');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F33D1A3E7');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FAA816465');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232A76ED395');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9AC78BCF8');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9FFDD4EC8');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D986A676ED');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E647733D1A3E7');
        $this->addSql('ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649708A0E0');
        $this->addSql('ALTER TABLE user_meeting DROP FOREIGN KEY FK_AD18FF33A76ED395');
        $this->addSql('ALTER TABLE user_meeting DROP FOREIGN KEY FK_AD18FF3367433D9C');
        $this->addSql('DROP TABLE gender');
        $this->addSql('DROP TABLE meeting');
        $this->addSql('DROP TABLE meeting_team');
        $this->addSql('DROP TABLE sport');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE tournament_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_meeting');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
