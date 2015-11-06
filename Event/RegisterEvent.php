<?php

namespace R\U2FTwoFactorBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class RegisterEvent
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
     * @var Response
     **/
    protected $response;

    /**
     * __construct
     * @param array $registration
     * @param USer  $user
     * @return void
     **/
    public function __construct($registration, $user)
    {
        $this->registration = $registration;
        $this->user = $user;
    }

    /**
     * getRegistration
     *
     * @return mixed
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * getUser
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * setUser
     *
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
     * getResponse
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * setResponse
     *
     * @param mixed $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }
}
