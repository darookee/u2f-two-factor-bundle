<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twig\Environment;

class U2FFormRenderer implements TwoFactorFormRendererInterface
{
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var string
     */
    private $template;

    private $authenticator;
    /**
     * @var TokenInterface|null
     */
    private $token;
    /**
     * @var Session
     */
    private $session;

    public function __construct(Environment $twigRenderer, string $template, U2FAuthenticator $authenticator, TokenStorageInterface $tokenStorage, Session $session)
    {
        $this->template = $template;
        $this->twigEnvironment = $twigRenderer;
        $this->authenticator = $authenticator;
        $this->token = $tokenStorage->getToken();
        $this->session = $session;
    }

    public function renderForm(Request $request, array $templateVars): Response
    {
        $user = $this->token->getUser();

        $authenticationData = $this->authenticator->generateRequest($user);

        $templateVars['authenticationData'] = $authenticationData;

        $this->session->set('u2f_authentication', $authenticationData);

        $content = $this->twigEnvironment->render($this->template, $templateVars);
        $response = new Response();
        $response->setContent($content);

        return $response;
    }
}
