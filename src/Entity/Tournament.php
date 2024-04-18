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
    private ?int $gender_rule = null;

    #[ORM\Column]
    private ?int $number_players_per_team = null;

    #[ORM\Column(length: 100)]
    private ?string $type_tournament = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    private ?Sport $sport_id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tournaments_history')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    private ?User $organisator_id = null;

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'tournament_id')]
    private Collection $meetings;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->meetings = new ArrayCollection();
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

    public function isPublic(): ?bool
    {
        return $this->is_public;
    }

    public function setPublic(bool $is_public): static
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

    public function getGenderRule(): ?int
    {
        return $this->gender_rule;
    }

    public function setGenderRule(int $gender_rule): static
    {
        $this->gender_rule = $gender_rule;

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

    public function getSportId(): ?Sport
    {
        return $this->sport_id;
    }

    public function setSportId(?Sport $sport_id): static
    {
        $this->sport_id = $sport_id;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTournamentsHistory($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeTournamentsHistory($this);
        }

        return $this;
    }

    public function getOrganisatorId(): ?User
    {
        return $this->organisator_id;
    }

    public function setOrganisatorId(?User $organisator_id): static
    {
        $this->organisator_id = $organisator_id;

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): static
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings->add($meeting);
            $meeting->setTournamentId($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): static
    {
        if ($this->meetings->removeElement($meeting)) {
            // set the owning side to null (unless already changed)
            if ($meeting->getTournamentId() === $this) {
                $meeting->setTournamentId(null);
            }
        }

        return $this;
    }
}
