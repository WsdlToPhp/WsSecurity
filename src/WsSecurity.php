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
     * @param bool $passwordDigest
     * @param int $addCreated
     * @param int $addExpires
     * @param bool $mustunderstand
     * @param string $actor
     * @param string $usernameId
     * @param bool $addNonce
     */
    protected function __construct($username, $password, $passwordDigest = false, $addCreated = 0, $addExpires = 0, $mustunderstand = false, $actor = null, $usernameId = null, $addNonce = true)
    {
        $this
            ->initSecurity($mustunderstand, $actor)
            ->setUsernameToken($username, $usernameId)
            ->setPassword($password, $passwordDigest, $addCreated)
            ->setNonce($addNonce)
            ->setCreated($addCreated)
            ->setTimestamp($addCreated, $addExpires);
    }
    /**
     * @param bool $mustunderstand
     * @param string $actor
     * @return WsSecurity
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
     * @param string $usernameId
     * @param bool $addNonce
     * @return \SoapHeader|\SoapVar
     */
    public static function createWsSecuritySoapHeader($username, $password, $passwordDigest = false, $addCreated = 0, $addExpires = 0, $returnSoapHeader = true, $mustunderstand = false, $actor = null, $usernameId = null, $addNonce = true)
    {
        $self = new WsSecurity($username, $password, $passwordDigest, $addCreated, $addExpires, $mustunderstand, $actor, $usernameId, $addNonce);
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
     * @param string $username
     * @param string $usernameId
     * @return WsSecurity
     */
    protected function setUsernameToken($username, $usernameId = null)
    {
        $usernameToken = new UsernameToken($usernameId);
        $usernameToken->setUsername(new Username($username));
        $this->security->setUsernameToken($usernameToken);
        return $this;
    }
    /**
     * @param string $password
     * @param bool $passwordDigest
     * @param int $addCreated
     * @return WsSecurity
     */
    protected function setPassword($password, $passwordDigest = false, $addCreated = 0)
    {
        $this->getUsernameToken()->setPassword(new Password($password, $passwordDigest ? Password::TYPE_PASSWORD_DIGEST : Password::TYPE_PASSWORD_TEXT, is_bool($addCreated) ? 0 : ($addCreated > 0 ? $addCreated : 0)));
        return $this;
    }
    /**
     * @param  $addNonce
     * @return WsSecurity
     */
    protected function setNonce($addNonce)
    {
        if ($addNonce){
            $nonceValue = $this->getPassword()->getNonceValue();
            if (!empty($nonceValue))
            {
                $this->getUsernameToken()->setNonce(new Nonce($nonceValue));
            }
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
