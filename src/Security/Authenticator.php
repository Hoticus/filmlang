<?php

namespace App\Security;

use App\Entity\User;
use App\HttpFoundation\JsonApiSuccessfulResponse;
use App\Repository\EmailAuthenticationCodeRepository;
use App\Service\JsonApiErrorResponseCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class Authenticator extends AbstractLoginFormAuthenticator
{
    private array $options = [
        'username_parameter' => 'username',
        'code_parameter' => 'code',
        'authentication_path' => 'app_authentication',
    ];

    public function __construct(
        private HttpUtils $httpUtils,
        private UserProviderInterface $userProvider,
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private JsonApiErrorResponseCreator $jsonApiErrorResponseCreator,
        private RequestRateLimiterInterface $loginRateLimiter,
        private AuthenticationCodeWorker $authenticationCodeWorker,
        private EmailAuthenticationCodeRepository $emailAuthenticationCodeRepository,
    ) {
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, $this->options['authentication_path']);
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST')
            && $this->httpUtils->checkRequestPath($request, $this->options['authentication_path'])
            && 'form' === $request->getContentType();
    }

    public function authenticate(Request $request): Passport
    {
        $request->setRequestFormat('json');

        $credentials = $this->getCredentials($request);

        $userBadge = new UserBadge($credentials['username'], $this->userProvider->loadUserByIdentifier(...));

        if (
            !$this->authenticationCodeWorker->verifyCodeForEmail($credentials['code'], $credentials['username'])
            || !$this->loginRateLimiter->consume($request)->isAccepted()
        ) {
            return new Passport($userBadge, new CustomCredentials(function () {
                return false;
            }, null));
        }

        $this->emailAuthenticationCodeRepository->deleteByEmail($credentials['username']);

        try {
            $userBadge->getUser();
        } catch (AuthenticationException $e) {
            $user = new User();
            $user->setEmail($credentials['username']);
            $this->em->persist($user);
            $this->em->flush();
        }

        $this->loginRateLimiter->reset($request);

        return new SelfValidatingPassport(
            $userBadge,
            [new RememberMeBadge()]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new JsonApiSuccessfulResponse();
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $error = match (true) {
            $exception instanceof TooManyLoginAttemptsAuthenticationException => 'too_many_authentication_attempts',
            $exception instanceof BadCredentialsException => 'bad_credentials',
            default => null
        };
        $statusCode = match (true) {
            $exception instanceof TooManyLoginAttemptsAuthenticationException => Response::HTTP_TOO_MANY_REQUESTS,
            $exception instanceof BadCredentialsException => Response::HTTP_BAD_REQUEST,
            default => null
        };

        $errorMessage = null;

        if ($error !== null) {
            $errorMessage = $this->translator->trans(
                $exception->getMessageKey(),
                $exception->getMessageData(),
                'security',
                $request->getLocale()
            );
        } else {
            // TODO: log error
        }

        return $this->jsonApiErrorResponseCreator->create($exception, $error, $errorMessage, $statusCode);
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [];

        $credentials['username'] = ParameterBagUtils::getParameterBagValue(
            $request->request,
            $this->options['username_parameter']
        );
        $credentials['code'] = ParameterBagUtils::getParameterBagValue(
            $request->request,
            $this->options['code_parameter']
        );

        if (!is_string($credentials['username']) && !$credentials['username'] instanceof \Stringable) {
            throw new BadRequestHttpException(sprintf(
                'The key "%s" must be a string, "%s" given.',
                $this->options['username_parameter'],
                gettype($credentials['username'])
            ));
        }

        $credentials['username'] = trim($credentials['username']);

        if (strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        return $credentials;
    }
}
