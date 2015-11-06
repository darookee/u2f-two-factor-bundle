<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Prodiver\U2F;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class U2FAuthenticator
 * @author Nils Uliczka
 */
class U2FAuthenticator implements U2FAuthenticatorInterface
{
    /**
     * @var U2F
     **/
    protected $u2f;

    /**
     * __construct
     * @param RequestStack $requestStack
     * @return void
     **/
    public function __construct(RequestStack $requestStack)
    {
        $scheme = $requestStack->getCurrentRequest()->getScheme();
        $host = $requestStack->getCurrentRequest()->getHost();
        $port = $requestStack->getCurrentRequest()->getPort();
        $this->u2f = new \u2flib_server\U2F($scheme.'://'.$host.((80 !== $port && 443 !== $port)?':'.$port:''));
    }
    /**
     * generateRequest
     * @param AdvancedUserInterface $user
     * @return string
     **/
    public function generateRequest(AdvancedUserInterface $user)
    {
        return $this->u2f->getAuthenticateData($user->getU2FKeys()->toArray());
    }

    /**
     * checkRequest
     * @param AdvancedUserInterface $user
     * @param mixed                 $request
     * @param mixed                 $authData
     * @return void
     **/
    public function checkRequest(AdvancedUserInterface $user, $request, $authData)
    {
        $reg = $this->u2f->doAuthenticate($request, $user->getU2FKeys()->toArray(), $authData);

        return $reg;
    }

    /**
     * generateRegistrationRequest
     * @param AdvancedUserInterface $user
     * @return string
     **/
    public function generateRegistrationRequest(AdvancedUserInterface $user)
    {
        return $this->u2f->getRegisterData($user->getU2FKeys()->toArray());
    }

    /**
     * doRegistration
     * @param string $regRequest
     * @param string $registration
     * @return void
     **/
    public function doRegistration($regRequest, $registration)
    {
        return $this->u2f->doRegister($regRequest, $registration);
    }
}
