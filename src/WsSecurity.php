<?php

namespace WsdlToPhp\WsSecurity;

class WsSecurity
{
    /**
     * @var Security
     */
    protected $security;
    /**
     * @param string $username
     * @param string $password
     * @param string $passwordDigest
     * @param int $addCreated
     * @param int $addExpires
     * @param string $returnSoapHeader
     * @param string $mustunderstand
     * @param string $actor
     */
    protected function __construct($username, $password, $passwordDigest = false, $addCreated = 0, $addExpires = 0, $returnSoapHeader = true, $mustunderstand = false, $actor = null)
    {
        $this
            ->initSecurity($mustunderstand, $actor)
            ->setUsernameToken()
            ->setPassword($password, $passwordDigest, $addCreated)
            ->setNonce()
            ->setCreated($addCreated)
            ->setTimestamp();
    }
    /**
     * @param bool $mustunderstand
     * @param string $actor
     */
    protected function initSecurity($mustunderstand = false, $actor = null)
    {
        $this->security = new Security($mustunderstand, $actor);
        return $this;
    }
    /**
     * @return Security
     */
    public function getSecurity()
    {
        return $this->security;
    }
    /**
     * Create the SoapHeader object to send as SoapHeader in the SOAP request.
     * @param string $username
     * @param string $password
     * @param bool $passwordDigest
     * @param int $addCreated
     * @param int $addExpires
     * @param bool $mustunderstand
     * @param string $actor
     * @return SoapHeader|SoapVar
     */
    public static function createWsSecuritySoapHeader($username, $password, $passwordDigest = false, $addCreated = 0, $addExpires = 0, $returnSoapHeader = true, $mustunderstand = false, $actor = null)
    {
        $self = new WsSecurity($username, $password, $passwordDigest, $addCreated, $addExpires, $returnSoapHeader, $mustunderstand, $actor);
        if ($returnSoapHeader) {
            if (!empty($actor)) {
                return new \SoapHeader(Element::NS_WSSE, 'Security', new \SoapVar($self->getSecurity()->toSend(), XSD_ANYXML), $mustunderstand, $actor);
            } else {
                return new \SoapHeader(Element::NS_WSSE, 'Security', new \SoapVar($self->getSecurity()->toSend(), XSD_ANYXML), $mustunderstand);
            }
        } else {
            return new \SoapVar($self->getSecurity()->toSend(), XSD_ANYXML);
        }
    }
    /**
     * @return WsSecurity
     */
    protected function setUsernameToken()
    {
        $this->security->setUsernameToken(new UsernameToken());
        return this;
    }
    /**
     * @param string $password
     * @param string $passwordDigest
     * @param int $addCreated
     * @return WsSecurity
     */
    protected function setPassword($password, $passwordDigest = false, $addCreated = 0)
    {
        $this->getUsernameToken()->setPassword(new Password($password, $passwordDigest ? Password::TYPE_PASSWORD_DIGEST : Password::TYPE_PASSWORD_TEXT, is_bool($addCreated) ? 0 : ($addCreated > 0 ? $addCreated : 0)));
        return this;
    }
    /**
     * @return WsSecurity
     */
    protected function setNonce()
    {
        $nonceValue = $this->getPassword()->getNonceValue();
        if ($nonceValue instanceof Nonce) {
            $this->getUsernameToken()->setNonce($nonceValue);
        }
        return $this;
    }
    /**
     * @param int $addCreated
     * @return WsSecurity
     */
    protected function setCreated($addCreated)
    {
        $passwordDigest = $this->getPassword()->getTypeValue();
        $timestampValue = $this->getPassword()->getTimestampValue();
        if (($addCreated || $passwordDigest === Password::TYPE_PASSWORD_DIGEST) && $timestampValue > 0) {
            $this->getUsernameToken()->setCreated(new Created($timestampValue));
        }
        return $this;
    }
    /**
     * @param int $addCreated
     * @param int $addExpires
     * @return WsSecurity
     */
    protected function setTimestamp($addCreated = 0, $addExpires = 0)
    {
        $timestampValue = $this->getPassword()->getTimestampValue();
        if ($addCreated && $addExpires && $timestampValue) {
            $timestamp = new Timestamp();
            $timestamp->setCreated(new Created($timestampValue));
            $timestamp->setExpires(new Expires($timestampValue, $addExpires));
            $this->security->setTimestamp($timestamp);
        }
        return $this;
    }
    /**
     * @return UsernameToken
     */
    protected function getUsernameToken()
    {
        return $this->security->getUsernameToken();
    }
    /**
     * @return Password
     */
    protected function getPassword()
    {
        return $this->getUsernameToken()->getPassword();
    }
}
