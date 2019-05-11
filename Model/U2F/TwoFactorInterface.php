<?php

namespace R\U2FTwoFactorBundle\Model\U2F;

use Doctrine\Common\Collections\Collection;

interface TwoFactorInterface
{
    public function isU2FAuthEnabled(): bool;

    public function getU2FKeys(): Collection;

    public function addU2FKey(TwoFactorKeyInterface $key);

    public function removeU2FKey(TwoFactorKeyInterface $key);
}
