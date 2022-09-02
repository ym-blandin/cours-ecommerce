<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }


    public function supports(Request $request): ?bool
    {
        // L'authenticator va intervenir si la route est sécurity_login et si l'utilisateur à renvoyer ses informations de login (méthode POST)
        return $request->attributes->get('_route') === 'security_login' && $request->isMethod('POST');
    }


    public function authenticate(Request $request): Passport
    {
        // Puisque $request->request->get('login) retourne null, on passe par $request->request->all()
        $credentials = $request->request->all();

        // $email = $request->request->get('email');
        $email = $credentials['login']['email'];
        // $password = $request->request->get('password');
        $password = $credentials['login']['password'];

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->generator->generate('homepage'));
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->generator->generate('security_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->generator->generate('security_login'));
        /*
            * If you would like this class to control what happens when an anonymous user accesses a
            * protected page (e.g. redirect to /login), uncomment this method and make this class
            * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
            *
            * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
            */
    }
}
