<?php

namespace App\Security;

use App\Entity\EmailAuthenticationCode;
use App\Repository\EmailAuthenticationCodeRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AuthenticationCodeWorker
{
    public const CODE_LENGTH = 6;
    public const VALID_TIME = '15 minutes';

    private PasswordHasherInterface $hasher;

    public function __construct(
        private EmailAuthenticationCodeRepository $emailAuthenticationCodeRepository,
        PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
        $this->hasher = $passwordHasherFactory->getPasswordHasher(EmailAuthenticationCode::class);
    }

    public function generateCodeForEmail(string $email): string
    {
        $code = '';

        for ($i = 1; $i <= self::CODE_LENGTH; $i++) {
            $code .= random_int(0, 9);
        }

        $this->emailAuthenticationCodeRepository->deleteByEmail($email);

        $emailAuthenticationCode = new EmailAuthenticationCode();
        $validTo = (new \DateTimeImmutable())->modify('+' . self::VALID_TIME);
        $emailAuthenticationCode->setCode($this->hasher->hash($code))->setEmail($email)->setValidTo($validTo);
        $this->emailAuthenticationCodeRepository->add($emailAuthenticationCode, true);

        return $code;
    }

    public function verifyCodeForEmail(string $code, string $email): bool
    {
        $emailAuthenticationCode = $this->emailAuthenticationCodeRepository->findLastValidByEmail($email);

        if ($emailAuthenticationCode === null) {
            return false;
        }

        return $this->hasher->verify($emailAuthenticationCode->getCode(), $code);
    }
}
