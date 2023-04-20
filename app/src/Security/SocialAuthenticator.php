<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\LinkedInResourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class SocialAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    public const GOOGLE = 'google';
    public const LINKED_IN = 'linkedin';
    public const FACEBOOK = 'facebook';

    public const SUPPORTED_SOCIAL = [
        self::GOOGLE,
        self::LINKED_IN,
        self::FACEBOOK,
    ];

    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router
    ) {
    }

    public function supports(Request $request): ?bool
    {
        $routeInfo = $this->router->match($request->getPathInfo());

        return 'app_social_check' === $routeInfo['_route'] &&
            in_array($routeInfo['social'] ?? null, self::SUPPORTED_SOCIAL);
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient($request->get('social'));
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser|LinkedInResourceOwner|FacebookUser $socialUser */
                $socialUser = $client->fetchUserFromToken($accessToken);
                /** @var UserRepository $repository */
                $repository = $this->entityManager->getRepository(User::class);
                $existingUser = match ($socialUser::class) {
                    GoogleUser::class => $repository->findOneBy(['googleSubId' => $socialUser->getId()]),
                    LinkedInResourceOwner::class => $repository->findOneBy(['linkedInSubId' => $socialUser->getId()]),
                    FacebookUser::class => $repository->findOneBy(['facebookSubId' => $socialUser->getId()]),
                    default => null,
                };

                if ($existingUser) {
                    return $existingUser;
                }

                $user = $repository->findOneBy(['email' => $socialUser->getEmail()]);
                $user = match ($socialUser::class) {
                    GoogleUser::class => $user ?
                        $user->setGoogleSubId($socialUser->getId()) :
                        User::createGoogleUser($socialUser),
                    LinkedInResourceOwner::class => $user ?
                        $user->setLinkedInSubId($socialUser->getId()) :
                        User::createLinkedInUser($socialUser),
                    FacebookUser::class => $user ?
                        $user->setFacebookSubId($socialUser->getId()) :
                        User::createFacebookUser($socialUser),
                    default => null,
                };

                if ($user) {
                    $repository->save($user)->flush();
                }

                return $user;
            })
        );
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            $this->router->generate('app_home_page')
        );
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->router->generate('app_social_login', ['client' => 'facebook'])
        );
    }
}
