<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    public const STATUS_INITIALIZATION = 0;
    public const STATUS_ADD_PLAYERS = 1;
    public const STATUS_SELECT_MATCHES = 2;
    public const STATUS_IN_PROGRESS = 3;
    public const STATUS_FINISHED = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $rules = null;

    #[ORM\Column]
    private ?bool $is_public = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\Column]
    private ?int $number_players_per_team = null;

    #[ORM\Column(length: 100)]
    private ?string $type_tournament = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    private ?Sport $sport = null;

    #[ORM\ManyToOne(inversedBy: 'tournaments_organised')]
    private ?User $organisator = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tournaments_participated')]
    private Collection $participating_players;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\OneToMany(targetEntity: Team::class, mappedBy: 'tournament')]
    private Collection $enrolled_teams;

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'tournament')]
    private Collection $enrolled_meetings;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    private ?Gender $gender_rule = null;

    #[ORM\Column(nullable: true)]
    private ?int $status = self::STATUS_INITIALIZATION; 

    public function __construct()
    {
        $this->participating_players = new ArrayCollection();
        $this->enrolled_teams = new ArrayCollection();
        $this->enrolled_meetings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRules(): ?string
    {
        return $this->rules;
    }

    public function setRules(?string $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->is_public;
    }

    public function setIsPublic(bool $is_public): static
    {
        $this->is_public = $is_public;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): static
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getNumberPlayersPerTeam(): ?int
    {
        return $this->number_players_per_team;
    }

    public function setNumberPlayersPerTeam(int $number_players_per_team): static
    {
        $this->number_players_per_team = $number_players_per_team;

        return $this;
    }

    public function getTypeTournament(): ?string
    {
        return $this->type_tournament;
    }

    public function setTypeTournament(string $type_tournament): static
    {
        $this->type_tournament = $type_tournament;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(?Sport $sport): static
    {
        $this->sport = $sport;

        return $this;
    }

    public function getOrganisator(): ?User
    {
        return $this->organisator;
    }

    public function setOrganisator(?User $organisator): static
    {
        $this->organisator = $organisator;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipatingPlayers(): Collection
    {
        return $this->participating_players;
    }

    public function addParticipatingPlayer(User $participatingPlayer): static
    {
        if (!$this->participating_players->contains($participatingPlayer)) {
            $this->participating_players->add($participatingPlayer);
        }

        return $this;
    }

    public function removeParticipatingPlayer(User $participatingPlayer): static
    {
        $this->participating_players->removeElement($participatingPlayer);

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getEnrolledTeams(): Collection
    {
        return $this->enrolled_teams;
    }

    public function addEnrolledTeam(Team $enrolledTeam): static
    {
        if (!$this->enrolled_teams->contains($enrolledTeam)) {
            $this->enrolled_teams->add($enrolledTeam);
            $enrolledTeam->setTournament($this);
        }

        return $this;
    }

    public function removeEnrolledTeam(Team $enrolledTeam): static
    {
        if ($this->enrolled_teams->removeElement($enrolledTeam)) {
            // set the owning side to null (unless already changed)
            if ($enrolledTeam->getTournament() === $this) {
                $enrolledTeam->setTournament(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getEnrolledMeetings(): Collection
    {
        return $this->enrolled_meetings;
    }

    public function addEnrolledMeeting(Meeting $enrolledMeeting): static
    {
        if (!$this->enrolled_meetings->contains($enrolledMeeting)) {
            $this->enrolled_meetings->add($enrolledMeeting);
            $enrolledMeeting->setTournament($this);
        }

        return $this;
    }

    public function removeEnrolledMeeting(Meeting $enrolledMeeting): static
    {
        if ($this->enrolled_meetings->removeElement($enrolledMeeting)) {
            // set the owning side to null (unless already changed)
            if ($enrolledMeeting->getTournament() === $this) {
                $enrolledMeeting->setTournament(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getGenderRule(): ?Gender
    {
        return $this->gender_rule;
    }

    public function setGenderRule(?Gender $gender_rule): static
    {
        $this->gender_rule = $gender_rule;

        return $this;
    }

    public function getNumberPlayers(): ?int
    {
        return count($this->getParticipatingPlayers());
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

        return $this;
    }
}
