<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use function json_encode;
use const JSON_UNESCAPED_SLASHES;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use u2flib_server\Registration;
use u2flib_server\U2F;

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

    public function __construct(RequestStack $requestStack)
    {
        $scheme = $requestStack->getCurrentRequest()->getScheme();
        $host = $requestStack->getCurrentRequest()->getHost();
        $port = $requestStack->getCurrentRequest()->getPort();
        $intPort = (int) $port;
        $this->u2f = new U2F($scheme.'://'.$host.((80 !== $intPort && 443 !== $intPort)?':'.$port:''));
    }

    /**
     * @return string
     **/
    public function generateRequest(UserInterface $user)
    {
        return json_encode($this->u2f->getAuthenticateData($user->getU2FKeys()->toArray()), JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array $requests
     * @param mixed $authData
     *
     * @return bool
     */
    public function checkRequest(UserInterface $user, array $requests, $authData)
    {
        $reg = $this->u2f->doAuthenticate($requests, $user->getU2FKeys()->toArray(), json_decode($authData));

        if ($reg) {
            return true;
        }

        return false;
    }

    public function generateRegistrationRequest(UserInterface $user) : array
    {
        return $this->u2f->getRegisterData($user->getU2FKeys()->toArray());
    }

    /**
     * @param string $regRequest
     * @param string $registration
     **/
    public function doRegistration($regRequest, $registration) : Registration
    {
        return $this->u2f->doRegister($regRequest, $registration);
    }
}
