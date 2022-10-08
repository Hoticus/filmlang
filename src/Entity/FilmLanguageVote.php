<?php

namespace App\Entity;

use App\Repository\FilmLanguageVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmLanguageVoteRepository::class)]
class FilmLanguageVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Film::class, inversedBy: 'languageVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $film;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'filmLanguageVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $votedUser;

    #[ORM\Column(type: 'smallint')]
    private $vote;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): self
    {
        $this->film = $film;

        return $this;
    }

    public function getVotedUser(): ?User
    {
        return $this->votedUser;
    }

    public function setVotedUser(?User $votedUser): self
    {
        $this->votedUser = $votedUser;

        return $this;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): self
    {
        $this->vote = $vote;

        return $this;
    }
}
