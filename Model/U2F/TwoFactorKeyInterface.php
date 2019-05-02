<?php

namespace R\U2FTwoFactorBundle\Model\U2F;

/**
 * Interface KeyInterface.
 *
 * @author Nils Uliczka
 */
interface TwoFactorKeyInterface
{
    /**
     * @return mixed
     */
    public function getKeyHandle();

    /**
     * @param mixed $keyHandle
     *
     * @return $this
     */
    public function setKeyHandle($keyHandle);

    /**
     * @return mixed
     */
    public function getPublicKey();

    /**
     * @param mixed $publicKey
     *
     * @return $this
     */
    public function setPublicKey($publicKey);

    /**
     * @return mixed
     */
    public function getCertificate();

    /**
     * @param mixed $certificate
     *
     * @return $this
     */
    public function setCertificate($certificate);

    /**
     * @return mixed
     */
    public function getCounter();

    /**
     * @param mixed $counter
     *
     * @return $this
     */
    public function setCounter($counter);

    /**
     * @return string
     **/
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     **/
    public function setName($name);
}
