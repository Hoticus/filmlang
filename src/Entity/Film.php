<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $tmdbId;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $defaultLanguageVote;

    #[ORM\OneToMany(mappedBy: 'film', targetEntity: FilmLanguageVote::class, orphanRemoval: true)]
    private $languageVotes;

    public function __construct()
    {
        $this->languageVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(int $tmdbId): self
    {
        $this->tmdbId = $tmdbId;

        return $this;
    }

    public function getDefaultLanguageVote(): ?int
    {
        return $this->defaultLanguageVote;
    }

    public function setDefaultLanguageVote(?int $defaultLanguageVote): self
    {
        $this->defaultLanguageVote = $defaultLanguageVote;

        return $this;
    }

    /**
     * @return Collection<int, FilmLanguageVote>
     */
    public function getLanguageVotes(): Collection
    {
        return $this->languageVotes;
    }

    public function addLanguageVote(FilmLanguageVote $languageVote): self
    {
        if (!$this->languageVotes->contains($languageVote)) {
            $this->languageVotes[] = $languageVote;
            $languageVote->setFilm($this);
        }

        return $this;
    }

    public function removeLanguageVote(FilmLanguageVote $languageVote): self
    {
        if ($this->languageVotes->removeElement($languageVote)) {
            // set the owning side to null (unless already changed)
            if ($languageVote->getFilm() === $this) {
                $languageVote->setFilm(null);
            }
        }

        return $this;
    }

    public function getLanguage(): int|null
    {
        $defaultLanguage = $this->getDefaultLanguageVote();
        $languageVotes = $this->getLanguageVotes();

        $votesCount = $languageVotes->count() + ($defaultLanguage !== null ? 5 : 0);

        if ($votesCount < 5) {
            return null;
        }

        $votes = ($defaultLanguage !== null ? $defaultLanguage : 0) * 5;
        foreach ($languageVotes as $languageVote) {
            $votes += $languageVote->getVote();
        }

        return round($votes / $votesCount);
    }
}
