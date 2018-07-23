<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class U2FAuthenticatorInterface
 * @author Nils Uliczka
 */
interface U2FAuthenticatorInterface
{
    /**
     * generateRequest
     * @param UserInterface $user
     * @return string
     **/
    public function generateRequest(UserInterface $user);

    /**
     * checkRequest
     * @param UserInterface $user
     * @param array                 $requests
     * @param mixed                 $authData
     * @return boolean
     **/
    public function checkRequest(UserInterface $user, array $requests, $authData);
}
