<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Nils Uliczka
 */
interface U2FAuthenticatorInterface
{
    /**
     * @param UserInterface $user
     *
     * @return string
     **/
    public function generateRequest(UserInterface $user);

    /**
     * @param UserInterface $user
     * @param array         $requests
     * @param mixed         $authData
     *
     * @return bool
     **/
    public function checkRequest(UserInterface $user, array $requests, $authData);
}
