<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class TwoFactorProvider
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

    /**
     * __construct
     *
     * @param U2FAuthenticatorInterface $authenticator
     * @param \Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface $formRenderer
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(U2FAuthenticatorInterface $authenticator, TwoFactorFormRendererInterface $formRenderer, Session $session)
    {
        $this->authenticator = $authenticator;
        $this->formRenderer = $formRenderer;

        $this->session = $session;
    }

    /**
     * beginAuthentication
     * @param AuthenticationContextInterface $context
     * @return boolean
     **/
    public function beginAuthentication(AuthenticationContextInterface $context): bool
    {
        $user = $context->getUser();

        return ($user instanceof TwoFactorInterface && $user->isU2FAuthEnabled());
    }

    /**
     * @param mixed $user
     * @param string $authenticationCode
     *
     * @return bool
     */
    public function validateAuthenticationCode($user, string $authenticationCode): bool
    {
        if (!($user instanceof TwoFactorInterface)) {
            return false;
        }

        $request = json_decode($this->session->get('u2f_authentication'));

        return $this->authenticator->checkRequest($user, $request, $authenticationCode);
    }

    public function getFormRenderer(): TwoFactorFormRendererInterface
    {
        return $this->formRenderer;
    }
}
