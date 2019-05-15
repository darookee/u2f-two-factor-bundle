<?php

namespace R\U2FTwoFactorBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Nils Uliczka
 */
class RegisterEvent extends Event
{
    /**
     * @var array
     **/
    protected $registration;

    /**
     * @var User
     **/
    protected $user;

    /**
     * @var string
     **/
    protected $keyName;

    /**
     * @var Response
     **/
    protected $response;

    /**
     * @param array  $registration
     * @param User   $user
     * @param string $name
     **/
    public function __construct($registration, $user, $name)
    {
        $this->registration = $registration;
        $this->user = $user;
        $this->keyName = $name;
    }

    /**
     * @return mixed
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * @param mixed $keyName
     *
     * @return $this
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;

        return $this;
    }
}
