<?php

namespace R\U2FTwoFactorBundle\Model\U2F;

/**
 * Interface: TwoFactorInterface
 *
 */
interface TwoFactorInterface
{

    /**
     * isU2FAuthEnabled
     * @return boolean
     **/
    public function isU2FAuthEnabled();

    /**
     * getU2FKeys
     * @return array
     **/
    public function getU2FKeys();

    /**
     * addU2FKey
     * @param U2FKey $key
     * @return void
     **/
    public function addU2FKey($key);

    /**
     * removeU2FKey
     * @param U2FKey $key
     * @return void
     **/
    public function removeU2FKey($key);
}
