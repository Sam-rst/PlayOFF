<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username', 'email'])]
#[UniqueEntity(fields: ['username', 'email'], message: 'Il y a déjà un compte avec ce pseudo')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[ORM\Column(length: 100)]
    private ?string $username = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birth_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Tournament>
     */
    #[ORM\OneToMany(targetEntity: Tournament::class, mappedBy: 'organisator')]
    private Collection $tournaments_organised;

    /**
     * @var Collection<int, Tournament>
     */
    #[ORM\ManyToMany(targetEntity: Tournament::class, mappedBy: 'participating_players')]
    private Collection $tournaments_participated;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'enrolled_players')]
    private Collection $teams_history;

    /**
     * @var Collection<int, Meeting>
     */
    #[ORM\ManyToMany(targetEntity: Meeting::class, inversedBy: 'participating_players')]
    private Collection $meetings_history;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Gender $gender = null;

    public function __construct()
    {
        $this->tournaments_organised = new ArrayCollection();
        $this->tournaments_participated = new ArrayCollection();
        $this->teams_history = new ArrayCollection();
        $this->meetings_history = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if (empty($roles)) {
            $roles[] = 'ROLE_GUEST';
        }

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->firstname . ' ' . $this->lastname ;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(?\DateTimeInterface $birth_date): static
    {
        $this->birth_date = $birth_date;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Tournament>
     */
    public function getTournamentsOrganised(): Collection
    {
        return $this->tournaments_organised;
    }

    public function addTournamentsOrganised(Tournament $tournamentsOrganised): static
    {
        if (!$this->tournaments_organised->contains($tournamentsOrganised)) {
            $this->tournaments_organised->add($tournamentsOrganised);
            $tournamentsOrganised->setOrganisator($this);
        }

        return $this;
    }

    public function removeTournamentsOrganised(Tournament $tournamentsOrganised): static
    {
        if ($this->tournaments_organised->removeElement($tournamentsOrganised)) {
            // set the owning side to null (unless already changed)
            if ($tournamentsOrganised->getOrganisator() === $this) {
                $tournamentsOrganised->setOrganisator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tournament>
     */
    public function getTournamentsParticipated(): Collection
    {
        return $this->tournaments_participated;
    }

    public function addTournamentsParticipated(Tournament $tournamentsParticipated): static
    {
        if (!$this->tournaments_participated->contains($tournamentsParticipated)) {
            $this->tournaments_participated->add($tournamentsParticipated);
            $tournamentsParticipated->addParticipatingPlayer($this);
        }

        return $this;
    }

    public function removeTournamentsParticipated(Tournament $tournamentsParticipated): static
    {
        if ($this->tournaments_participated->removeElement($tournamentsParticipated)) {
            $tournamentsParticipated->removeParticipatingPlayer($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeamsHistory(): Collection
    {
        return $this->teams_history;
    }

    public function addTeamsHistory(Team $teamsHistory): static
    {
        if (!$this->teams_history->contains($teamsHistory)) {
            $this->teams_history->add($teamsHistory);
            $teamsHistory->addEnrolledPlayer($this);
        }

        return $this;
    }

    public function removeTeamsHistory(Team $teamsHistory): static
    {
        if ($this->teams_history->removeElement($teamsHistory)) {
            $teamsHistory->removeEnrolledPlayer($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetingsHistory(): Collection
    {
        return $this->meetings_history;
    }

    public function addMeetingsHistory(Meeting $meetingsHistory): static
    {
        if (!$this->meetings_history->contains($meetingsHistory)) {
            $this->meetings_history->add($meetingsHistory);
        }

        return $this;
    }

    public function removeMeetingsHistory(Meeting $meetingsHistory): static
    {
        $this->meetings_history->removeElement($meetingsHistory);

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getNumberTournamentsParticipated()
    {
        return count($this->getTournamentsParticipated());
    }
}
