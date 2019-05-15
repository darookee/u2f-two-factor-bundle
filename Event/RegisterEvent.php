<?php

namespace R\U2FTwoFactorBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface;
use u2flib_server\Registration;

/**
 * @author Nils Uliczka
 */
class RegisterEvent extends Event
{
    /** @var Registration **/
    protected $registration;
    /** @var TwoFactorInterface **/
    protected $user;
    /** @var string **/
    protected $keyName;
    /** @var Response **/
    protected $response;

    public function __construct(Registration $registration, TwoFactorInterface $user, string $name)
    {
        $this->registration = $registration;
        $this->user = $user;
        $this->keyName = $name;
    }

    public function getRegistration(): Registration
    {
        return $this->registration;
    }

    public function getUser(): TwoFactorInterface
    {
        return $this->user;
    }

    public function setUser(TwoFactorInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
    }

    public function setKeyName(string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }
}
