<?php

namespace R\U2FTwoFactorBundle\Model\U2F;

interface TwoFactorInterface
{
    /**
     * @return bool
     **/
    public function isU2FAuthEnabled();

    /**
     * @return array
     **/
    public function getU2FKeys();

    /**
     * @param U2FKey $key
     **/
    public function addU2FKey($key);

    /**
     * @param U2FKey $key
     **/
    public function removeU2FKey($key);
}
