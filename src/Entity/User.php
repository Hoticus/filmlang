<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\OneToMany(mappedBy: 'votedUser', targetEntity: FilmLanguageVote::class, orphanRemoval: true)]
    private $filmLanguageVotes;

    public function __construct()
    {
        $this->filmLanguageVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, FilmLanguageVote>
     */
    public function getFilmLanguageVotes(): Collection
    {
        return $this->filmLanguageVotes;
    }

    public function addFilmLanguageVote(FilmLanguageVote $filmLanguageVote): self
    {
        if (!$this->filmLanguageVotes->contains($filmLanguageVote)) {
            $this->filmLanguageVotes[] = $filmLanguageVote;
            $filmLanguageVote->setVotedUser($this);
        }

        return $this;
    }

    public function removeFilmLanguageVote(FilmLanguageVote $filmLanguageVote): self
    {
        if ($this->filmLanguageVotes->removeElement($filmLanguageVote)) {
            // set the owning side to null (unless already changed)
            if ($filmLanguageVote->getVotedUser() === $this) {
                $filmLanguageVote->setVotedUser(null);
            }
        }

        return $this;
    }
}
