<?php

namespace App\Controller;

use App\HttpFoundation\JsonApiSuccessfulResponse;
use App\Security\AuthenticationCodeWorker;
use App\Service\JsonApiErrorResponseCreator;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_', format: 'json')]
class SecurityController extends AbstractController
{
    #[Route('/api/is-authenticated', name: 'is_authenticated', methods: ['GET'])]
    public function isAuthenticated(): Response
    {
        return new JsonApiSuccessfulResponse($this->getUser() !== null);
    }

    #[Route('/api/authentication', name: 'authentication', methods: ['POST'])]
    public function authentication(): Response
    {
        throw new BadRequestHttpException();
    }

    #[Route('/api/authentication/send-email', name: 'authentication_send_email', methods: 'POST')]
    public function sendVerificationEmail(
        Request $request,
        AuthenticationCodeWorker $authenticationCodeWorker,
        Mailer $mailer,
        RateLimiterFactory $ipAuthenticationCodeEmailLimiter,
        RateLimiterFactory $emailIpAuthenticationCodeEmailLimiter,
        JsonApiErrorResponseCreator $jsonApiErrorResponseCreator,
    ): Response {
        $emailTo = $request->request->get('_email');
        if (!$emailTo) {
            throw new BadRequestHttpException();
        }

        if (
            !$emailIpAuthenticationCodeEmailLimiter->create($emailTo . '-' . $request->getClientIp())
                ->consume()->isAccepted()
            || !$ipAuthenticationCodeEmailLimiter->create($request->getClientIp())->consume()->isAccepted()
        ) {
            $exception = new TooManyRequestsHttpException();
            return $jsonApiErrorResponseCreator->create(
                $exception,
                errorMessage: 'Too many requests, please try again in 1 minute.'
            );
        }

        $code = $authenticationCodeWorker->generateCodeForEmail($emailTo);

        $mailer->sendAuthenticationEmail($emailTo, $code);

        return new JsonApiSuccessfulResponse();
    }
}
