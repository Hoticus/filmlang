<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

class Mailer
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @param string $to
     * @param string $authenticationCode
     * @return void
     *
     * @throws TransportExceptionInterface
     */
    public function sendAuthenticationEmail(string $to, string $authenticationCode): void
    {
        $email = (new TemplatedEmail())
            ->to($to)
            ->subject('Filmlang Authentication')
            ->htmlTemplate('emails/security/authentication.html.twig')
            ->context([
                'code' => $authenticationCode,
            ]);
        $this->sendEmail($email);
    }

    /**
     * @param RawMessage $email
     * @return void
     *
     * @throws TransportExceptionInterface
     */
    protected function sendEmail(RawMessage $email): void
    {
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // TODO: log
            throw $e;
        }
    }
}
