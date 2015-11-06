<?php

namespace R\U2FTwoFactorBundle\Model\U2F;

/**
 * Interface KeyInterface
 * @author Nils Uliczka
 */
interface TwoFactorKeyInterface
{
    /**
     * getKeyHandle
     *
     * @return mixed
     */
    public function getKeyHandle();

    /**
     * setKeyHandle
     *
     * @param mixed $keyHandle
     *
     * @return $this
     */
    public function setKeyHandle($keyHandle);

    /**
     * getpublicKey
     *
     * @return mixed
     */
    public function getPublicKey();

    /**
     * setPublicKey
     *
     * @param mixed $publicKey
     *
     * @return $this
     */
    public function setPublicKey($publicKey);

    /**
     * getCertificate
     *
     * @return mixed
     */
    public function getCertificate();

    /**
     * setCertificate
     *
     * @param mixed $certificate
     *
     * @return $this
     */
    public function setCertificate($certificate);

    /**
     * getCounter
     *
     * @return mixed
     */
    public function getCounter();

    /**
     * setCounter
     *
     * @param mixed $counter
     *
     * @return $this
     */
    public function setCounter($counter);

    /**
     * getName
     * @return string
     **/
    public function getName();

    /**
     * setName
     * @param string $name
     * @return $this
     **/
    public function setName($name);
}
