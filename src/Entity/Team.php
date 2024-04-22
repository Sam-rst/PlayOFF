<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $division = null;

    #[ORM\Column(nullable: true)]
    private ?int $rating = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'teams_history')]
    private Collection $enrolled_players;

    #[ORM\ManyToOne(inversedBy: 'enrolled_teams')]
    private ?Tournament $tournament = null;

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\ManyToMany(targetEntity: Meeting::class, mappedBy: 'enrolled_teams')]
    private Collection $meetings;

    #[ORM\ManyToOne(inversedBy: 'ranking')]
    private ?Meeting $rank_meeting = null;


    public function __construct()
    {
        $this->enrolled_players = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function setDivision(?string $division): static
    {
        $this->division = $division;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

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
     * @return Collection<int, User>
     */
    public function getEnrolledPlayers(): Collection
    {
        return $this->enrolled_players;
    }

    public function addEnrolledPlayer(User $enrolledPlayer): static
    {
        if (!$this->enrolled_players->contains($enrolledPlayer)) {
            $this->enrolled_players->add($enrolledPlayer);
        }

        return $this;
    }

    public function removeEnrolledPlayer(User $enrolledPlayer): static
    {
        $this->enrolled_players->removeElement($enrolledPlayer);

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

    public function __toString()
    {
        return $this->getName();
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
            $meeting->addEnrolledTeam($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): static
    {
        if ($this->meetings->removeElement($meeting)) {
            $meeting->removeEnrolledTeam($this);
        }

        return $this;
    }

    public function getRankMeeting(): ?Meeting
    {
        return $this->rank_meeting;
    }

    public function setRankMeeting(?Meeting $rank_meeting): static
    {
        $this->rank_meeting = $rank_meeting;

        return $this;
    }
}
