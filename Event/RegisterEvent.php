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
     * @var string
     **/
    protected $keyName;

    /**
     * @var Response
     **/
    protected $response;

    /**
     * __construct
     * @param array  $registration
     * @param User   $user
     * @param string $name
     * @return void
     **/
    public function __construct($registration, $user, $name)
    {
        $this->registration = $registration;
        $this->user = $user;
        $this->keyName = $name;
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

    /**
     * getKeyName
     *
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * setKeyName
     *
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
