<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Prodiver\U2F;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Class U2FAuthenticatorInterface
 * @author Nils Uliczka
 */
interface U2FAuthenticatorInterface
{
    /**
     * generateRequest
     * @param AdvancedUserInterface $user
     * @return string
     **/
    public function generateRequest(AdvancedUserInterface $user);

    /**
     * checkRequest
     * @param AdvancedUserInterface $user
     * @param mixed                 $request
     * @param mixed                 $authData
     * @return boolean
     **/
    public function checkRequest(AdvancedUserInterface $user, $request, $authData);
}
