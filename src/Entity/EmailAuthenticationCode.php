<?php

namespace App\Entity;

use App\Repository\EmailAuthenticationCodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailAuthenticationCodeRepository::class)]
class EmailAuthenticationCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $code;

    #[ORM\Column(type: 'string', length: 180)]
    private $email;

    #[ORM\Column(type: 'datetime_immutable')]
    private $validTo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
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

    public function getValidTo(): ?\DateTimeImmutable
    {
        return $this->validTo;
    }

    public function setValidTo(\DateTimeImmutable $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }
}
