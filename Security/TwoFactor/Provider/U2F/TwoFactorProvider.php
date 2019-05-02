<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Nils Uliczka
 */
class TwoFactorProvider implements TwoFactorProviderInterface
{
    /**
     * @var U2FAuthenticatorInterface
     **/
    protected $authenticator;

    /**
     * @var \Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface
     */
    private $formRenderer;

    /**
     * @var string
     **/
    protected $authCodeParameter;

    private $session;

    public function __construct(U2FAuthenticatorInterface $authenticator, TwoFactorFormRendererInterface $formRenderer, Session $session)
    {
        $this->authenticator = $authenticator;
        $this->formRenderer = $formRenderer;

        $this->session = $session;
    }

    public function prepareAuthentication($user): void
    {
        return null;
    }

    public function beginAuthentication(AuthenticationContextInterface $context): bool
    {
        $user = $context->getUser();

        return $user instanceof TwoFactorInterface && $user->isU2FAuthEnabled();
    }

    /**
     * @param mixed $user
     */
    public function validateAuthenticationCode($user, string $authenticationCode): bool
    {
        if (!($user instanceof TwoFactorInterface)) {
            return false;
        }

        $requests = json_decode($this->session->get('u2f_authentication'));

        return $this->authenticator->checkRequest($user, $requests, $authenticationCode);
    }

    public function getFormRenderer(): TwoFactorFormRendererInterface
    {
        return $this->formRenderer;
    }
}
