<?php

namespace WsdlToPhp\WsSecurity;

class WsSecurity
{
    /**
     * Create the SoapHeader object to send as SoapHeader in the SOAP request.
     * @uses UsernameToken::setCreated()
     * @uses UsernameToken::setUsername()
     * @uses UsernameToken::setPassword()
     * @uses UsernameToken::setNonce()
     * @uses Timestamp::setCreated()
     * @uses Timestamp::setTimestamp()
     * @uses Security::setUsernameToken()
     * @uses Security::setTimestamp()
     * @uses Security::toSend()
     * @uses Element::NS_WSSE
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
        $usernameToken = new UsernameToken();
        $password = new Password($password, $passwordDigest ? Password::TYPE_PASSWORD_DIGEST : Password::TYPE_PASSWORD_TEXT, is_bool($addCreated) ? 0 : ($addCreated > 0 ? $addCreated : 0));
        $timestampValue = $password->getTimestampValue();
        $nonceValue = $password->getNonceValue();
        if ($nonceValue) {
            $usernameToken->setNonce(new Nonce($nonceValue));
        }
        if (($addCreated || $passwordDigest) && $timestampValue) {
            $usernameToken->setCreated(new Created($timestampValue));
        }
        $usernameToken->setUsername(new Username($username));
        $usernameToken->setPassword($password);
        $security = new Security();
        $security->setUsernameToken($usernameToken);
        if ($addCreated && $addExpires && $timestampValue) {
            $timestamp = new Timestamp();
            $timestamp->setCreated(new Created($timestampValue));
            $timestamp->setExpires(new Expires($timestampValue, $addExpires));
            $security->setTimestamp($timestamp);
        }
        if ($returnSoapHeader) {
            if (! empty($actor)) {
                return new \SoapHeader(Element::NS_WSSE, 'Security', new \SoapVar($security->toSend(), XSD_ANYXML), $mustunderstand, $actor);
            } else {
                return new \SoapHeader(Element::NS_WSSE, 'Security', new \SoapVar($security->toSend(), XSD_ANYXML), $mustunderstand);
            }
        } else {
            return new \SoapVar($security->toSend(), XSD_ANYXML);
        }
    }
}
