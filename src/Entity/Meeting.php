<?php

namespace App\Entity;

use App\Repository\MeetingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $score = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $win_condition = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'meetings')]
    private Collection $ranking;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'meetings')]
    private Collection $enrolled_teams;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    private ?Tournament $tournament_id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'matchs_history')]
    private Collection $users;

    public function __construct()
    {
        $this->ranking = new ArrayCollection();
        $this->enrolled_teams = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function setEndTime(?\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getScore(): ?array
    {
        return $this->score;
    }

    public function setScore(?array $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getWinCondition(): ?string
    {
        return $this->win_condition;
    }

    public function setWinCondition(?string $win_condition): static
    {
        $this->win_condition = $win_condition;

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

    /**
     * @return Collection<int, Team>
     */
    public function getRanking(): Collection
    {
        return $this->ranking;
    }

    public function addRanking(Team $ranking): static
    {
        if (!$this->ranking->contains($ranking)) {
            $this->ranking->add($ranking);
        }

        return $this;
    }

    public function removeRanking(Team $ranking): static
    {
        $this->ranking->removeElement($ranking);

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
        }

        return $this;
    }

    public function removeEnrolledTeam(Team $enrolledTeam): static
    {
        $this->enrolled_teams->removeElement($enrolledTeam);

        return $this;
    }

    public function getTournamentId(): ?Tournament
    {
        return $this->tournament_id;
    }

    public function setTournamentId(?Tournament $tournament_id): static
    {
        $this->tournament_id = $tournament_id;

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
            $user->addMatchsHistory($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeMatchsHistory($this);
        }

        return $this;
    }
}
