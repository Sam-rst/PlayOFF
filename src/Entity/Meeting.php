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

    #[ORM\ManyToOne(inversedBy: 'enrolled_meetings')]
    private ?Tournament $tournament = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'meetings_history')]
    private Collection $participating_players;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'meetings')]
    private Collection $enrolled_teams;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\OneToMany(targetEntity: Team::class, mappedBy: 'rank_meeting')]
    private Collection $ranking;

    public function __construct()
    {
        $this->participating_players = new ArrayCollection();
        $this->enrolled_teams = new ArrayCollection();
        $this->ranking = new ArrayCollection();
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

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): static
    {
        $this->tournament = $tournament;

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
            $participatingPlayer->addMeetingsHistory($this);
        }

        return $this;
    }

    public function removeParticipatingPlayer(User $participatingPlayer): static
    {
        if ($this->participating_players->removeElement($participatingPlayer)) {
            $participatingPlayer->removeMeetingsHistory($this);
        }

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

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection<int, Team>
     */
    public function getRanking(): Collection
    {
        return $this->ranking;
    }

    public function addRanking(Team $team): static
    {
        if (null === $this->ranking) {
            $this->ranking = new ArrayCollection();
        }
        if (!$this->ranking->contains($team)) {
            $this->ranking->add($team);
            if ($team->getRankMeeting() !== $this) {
                $team->setRankMeeting($this);
            }
        }
        return $this;
    }

    public function removeRanking(Team $ranking): static
    {
        if ($this->ranking->removeElement($ranking)) {
            // set the owning side to null (unless already changed)
            if ($ranking->getRankMeeting() === $this) {
                $ranking->setRankMeeting(null);
            }
        }

        return $this;
    }

    public function getWinner()
    {
        if (!empty($this->getRanking())) {
            return $this->getRanking()[0];
        }
        return 'Pas encore connu';
    }
}
