<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use function json_encode;
use const JSON_UNESCAPED_SLASHES;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use u2flib_server\Registration;
use u2flib_server\U2F;

/**
 * @author Nils Uliczka
 */
class U2FAuthenticator implements U2FAuthenticatorInterface
{
    /** @var U2F **/
    protected $u2f;

    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();

        if ($request === null) {
            throw new \RuntimeException('Request cannot be null.');
        }

        $scheme = $request->getScheme();
        $host = $request->getHost();
        $port = $request->getPort();
        $intPort = (int) $port;

        $this->u2f = new U2F($scheme . '://' . $host . ((80 !== $intPort && 443 !== $intPort) ? ':' . $port : ''));
    }

    /** @inheritDoc */
    public function generateRequest(TwoFactorInterface $user): string
    {
        $data = json_encode($this->u2f->getAuthenticateData($user->getU2FKeys()->toArray()), JSON_UNESCAPED_SLASHES);

        if ($data === false) {
            throw new \RuntimeException('Invalid JSON');
        }

        return $data;
    }

    /** @inheritDoc */
    public function checkRequest(TwoFactorInterface $user, array $requests, $authData): bool
    {
        $this->u2f->doAuthenticate($requests, $user->getU2FKeys()->toArray(), json_decode($authData));

        return true;
    }

    /** @return mixed[] **/
    public function generateRegistrationRequest(TwoFactorInterface $user): array
    {
        return $this->u2f->getRegisterData($user->getU2FKeys()->toArray());
    }

    public function doRegistration(\stdClass $regRequest, object $registration): Registration
    {
        if (!property_exists($regRequest, 'challenge')) {
            throw new \RuntimeException('Property "challenge" is missing in regRequest.');
        }

        if (!property_exists($regRequest, 'appId')) {
            throw new \RuntimeException('Property "appId" is missing in regRequest.');
        }

        $request = new \u2flib_server\RegisterRequest($regRequest->challenge, $regRequest->appId);
        return $this->u2f->doRegister($request, $registration);
    }
}
