<?php

namespace R\U2FTwoFactorBundle\Model\U2F;

use Doctrine\Common\Collections\Collection;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorKeyInterface;

interface TwoFactorInterface
{
    public function isU2FAuthEnabled(): bool;

    /** @return Collection<TwoFactorKeyInterface> */
    public function getU2FKeys(): Collection;

    public function addU2FKey(TwoFactorKeyInterface $key): void;

    public function removeU2FKey(TwoFactorKeyInterface $key): void;
}
