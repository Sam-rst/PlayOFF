<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Meeting;
use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');

        // Create genders
        $genders = [];
        foreach (['Homme', 'Femme', 'Mixte'] as $value) {
            $gender = new Gender();
            $gender->setGender($value);
            $manager->persist($gender);
            $genders[] = $gender;
        }



        // Create sports
        $sports = [];
        foreach (["Football (Soccer)", "Basketball", "Tennis", "Golf", "Baseball", "Volleyball", "Rugby", "Cricket", "Natation", "Athlétisme", "Cyclisme", "Boxe", "Escalade", "Ski", "Surf", "Équitation", "Gymnastique", "Badminton", "Judo", "Taekwondo", "Handball", "Hockey sur glace", "Patinage artistique", "Squash", "Billard", "Plongée sous-marine", "MMA", "Lutte", "Aviron", "Canoë-kayak", "Triathlon", "Polo", "Ultimate Frisbee", "Échecs", "Boulingrin", "Pétanque", "Tir à l'arc", "Voile", "Kitesurf", "Parapente", "Escalade de bloc", "Parkour", "Sumo", "Course automobile", "Course de chevaux", "Course de lévriers", "Marche", "Course à pied", "Plongeon", "Saut en hauteur", "Saut en longueur", "Saut en triple", "Biathlon", "Saut à ski", "Saut à la perche", "Natation synchronisée", "Équitation western", "Équitation classique", "Ski de fond", "Luge", "Skeleton", "Bobsleigh", "Snowboard", "Course en raquettes", "Rallye", "Planche à voile", "Girevoy sport", "Curling", "Paddle tennis", "Padel", "Agility", "Cani-cross", "Flyball", "Disc golf", "Tchoukball", "Agonistique", "Volley-ball de plage", "Baseball féminin", "Basketball en fauteuil roulant", "Crosse", "Crosse féminine", "Crosse en fauteuil roulant", "Golf miniature", "Mini-basketball", "Mini-football", "Mini-golf", "Mini-handball", "Mini-hockey", "Mini-rugby", "Mini-volleyball", "Course en montagne", "Course d'orientation", "Biathlon d'été", "Tennis-ballon"] as $value) {
            $sport = new Sport();
            $sport
                ->setName($value)
                ->setDescription($faker->sentence());
            $manager->persist($sport);
            $sports[] = $sport;
        }



        // Create 10 users
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($faker->password(6))
                ->setUsername($faker->userName())
                ->setGender($faker->randomElement(array_filter($genders, fn ($gender) => $gender->getGender() !== 'Mixte')))
                ->setBirthDate(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($user);
            $users[] = $user;
        }



        // Create tournaments
        $tournaments = [];
        for ($i = 0; $i < 10; $i++) {
            $tournament = new Tournament();
            $sport = $faker->randomElement($sports);
            $type = $faker->randomElement(['Championnat', 'Grand Chelem']);
            $tournament
                ->setName($type . ' de ' . $sport)
                ->setRules($faker->text())
                ->setIsPublic($faker->boolean())
                ->setStartTime(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setEndTime(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setGenderRule($faker->randomElement($genders))
                ->setNumberPlayersPerTeam($faker->numberBetween(1, 15))
                ->setTypeTournament($type)
                ->setSport($sport)
                ->setOrganisator($faker->randomElement($users))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setStatus($faker->numberBetween(1, 4));
            $manager->persist($tournament);
            $tournaments[] = $tournament;
        }



        // Create Teams
        $teams = [];
        foreach ($tournaments as $tournament) {
            for ($i = 0; $i <= $faker->numberBetween(2, 6); $i++) {
                $team = new Team();
                $team
                    ->setName($faker->colorName())
                    ->setDescription($faker->text())
                    ->setDivision($faker->randomElement(['Division ', 'Region ', 'National ']) . $faker->numberBetween(1, 6))
                    ->setRating($faker->numberBetween(1, 10))
                    ->setTournament($tournament)
                    ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()));

                for ($j = 0; $j < $tournament->getNumberPlayersPerTeam(); $j++) {
                    $user = $faker->randomElement($users);
                    $user
                        ->addTeamsHistory($team)
                        ->addTournamentsParticipated($tournament);
                    $manager->persist($user);
                }
                $manager->persist($team);
                $teams[] = $team;
            }
        }



        // Create Meetings
        $meetings = [];
        for ($i = 0; $i < 10; $i++) {
            $meeting = new Meeting();
            $score = [];

            // Links teams and players to meeting
            $teams_ids = array_rand($teams, 2);
            foreach ($teams_ids as $id) {
                $meeting->addEnrolledTeam($teams[$id]);
                $score[] = $faker->numberBetween(0, 7);
                foreach ($teams[$id]->getEnrolledPlayers() as $player) {
                    $meeting->addParticipatingPlayer($player);
                }
            }

            // Faire un faux classement
            $enrolled_teams = $meeting->getEnrolledTeams()->toArray();
            shuffle($enrolled_teams);
            foreach($enrolled_teams as $team) {
                $meeting->addRanking($team);
            }

            $meeting
                ->setName($faker->randomElement(['1er match de poules', '2eme match de poules', '3eme match de poules', '16e de finale', '8e de finale', 'Quart de finale', 'Demie finale', 'Finale']))
                ->setStartTime(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setEndTime(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setScore($score)
                ->setWinCondition($faker->randomElement(['KO', 'WO', 'WIN', 'WIN', 'WIN', '']))
                ->setTournament($faker->randomElement($tournaments))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()));

            $manager->persist($meeting);
            $meetings[] = $meeting;
        }

        foreach(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_GUEST'] as $role) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail(strtolower($role) . '@test.fr')
                ->setUsername($faker->userName())
                ->setPassword($role)
                ->setRoles([$role])
                ->setGender($faker->randomElement(array_filter($genders, fn ($gender) => $gender->getGender() !== 'Mixte')))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}
