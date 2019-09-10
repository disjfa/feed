<?php

namespace App\Security\Authenticator;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class FacebookAuthenticator.
 */
class FacebookAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * FacebookAuthenticator constructor.
     *
     * @param ClientRegistry         $clientRegistry
     * @param RouterInterface        $router
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ClientRegistry $clientRegistry, RouterInterface $router, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'connect_facebook_check' === $request->attributes->get('_route');
    }

    /**
     * @param Request $request
     *
     * @return AccessToken|mixed
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()->fetchUserFromToken($credentials);
        $existingUser = $this->userRepository->findOneBy([
            'facebookId' => $facebookUser->getId(),
        ]);

        /** @var User $existingUser */
        if ($existingUser instanceof User) {
            $existingUser->setFacebookAccessToken($credentials);

            return $existingUser;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'email' => $facebookUser->getEmail(),
        ]);

        if (null === $user) {
            $user = new User();
            $user->setName($facebookUser->getName());
            $user->setEmail($facebookUser->getEmail());
        }

        $user->setFacebookId($facebookUser->getId());
        $user->setFacebookAccessToken($credentials);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @return OAuth2Client|FacebookClient
     */
    private function getFacebookClient()
    {
        return $this->clientRegistry->getClient('facebook_main');
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return RedirectResponse|Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate('home_index');

        return new RedirectResponse($targetUrl);
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response|Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

//    /**
//     * @param mixed $credentials
//     * @param UserProviderInterface $userProvider
//     * @return UserInterface|null
//     */
//    public function getUser($credentials, UserProviderInterface $userProvider)
//    {
//        return $userProvider->loadUserByUsername($this->getFacebookClient()->fetchUserFromToken($credentials)->getId());
//    }
}
