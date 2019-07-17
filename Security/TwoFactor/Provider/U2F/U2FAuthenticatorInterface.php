<?php

namespace R\U2FTwoFactorBundle\Security\TwoFactor\Provider\U2F;

use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface;

/**
 * @author Nils Uliczka
 */
interface U2FAuthenticatorInterface
{
    public function generateRequest(TwoFactorInterface $user): string;

    /**
     * @param mixed[] $requests
     * @param mixed $authData
     * @return boolean
     **/
    public function checkRequest(TwoFactorInterface $user, array $requests, $authData): bool;
}
