<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

use SoapHeader;
use SoapVar;

class WsSecurity
{
    protected Security $security;

    protected function __construct(
        string $username,
        string $password,
        bool $passwordDigest = false,
        int $addCreated = 0,
        int $addExpires = 0,
        bool $mustUnderstand = false,
        ?string $actor = null,
        ?string $usernameId = null,
        bool $addNonce = true,
        string $envelopeNamespace = Security::ENV_NAMESPACE
    ) {
        $this
            ->initSecurity($mustUnderstand, $actor, $envelopeNamespace)
            ->setUsernameToken($username, $usernameId)
            ->setPassword($password, $passwordDigest, $addCreated)
            ->setNonce($addNonce)
            ->setCreated($addCreated)
            ->setTimestamp($addCreated, $addExpires)
        ;
    }

    public function getSecurity(): ?Security
    {
        return $this->security;
    }

    /**
     * @return SoapHeader|SoapVar
     */
    public static function createWsSecuritySoapHeader(
        string $username,
        string $password,
        bool $passwordDigest = false,
        int $addCreated = 0,
        int $addExpires = 0,
        bool $returnSoapHeader = true,
        bool $mustUnderstand = false,
        ?string $actor = null,
        ?string $usernameId = null,
        bool $addNonce = true,
        string $envelopeNamespace = Security::ENV_NAMESPACE
    ) {
        $self = new WsSecurity($username, $password, $passwordDigest, $addCreated, $addExpires, $mustUnderstand, $actor, $usernameId, $addNonce, $envelopeNamespace);
        if ($returnSoapHeader) {
            if (!empty($actor)) {
                return new SoapHeader(Element::NS_WSSE, Security::NAME, new SoapVar($self->getSecurity()->toSend(), XSD_ANYXML), $mustUnderstand, $actor);
            }

            return new SoapHeader(Element::NS_WSSE, Security::NAME, new SoapVar($self->getSecurity()->toSend(), XSD_ANYXML), $mustUnderstand);
        }

        return new SoapVar($self->getSecurity()->toSend(), XSD_ANYXML);
    }

    protected function initSecurity(bool $mustUnderstand = false, ?string $actor = null, string $envelopeNamespace = Security::ENV_NAMESPACE): self
    {
        $this->security = new Security($mustUnderstand, $actor, Security::NS_WSSE, $envelopeNamespace);

        return $this;
    }

    protected function setUsernameToken(string $username, ?string $usernameId = null): self
    {
        $usernameToken = new UsernameToken($usernameId);
        $usernameToken->setUsername(new Username($username));
        $this->security->setUsernameToken($usernameToken);

        return $this;
    }

    protected function setPassword(string $password, bool $passwordDigest = false, int $addCreated = 0): self
    {
        $this->getUsernameToken()->setPassword(new Password($password, $passwordDigest ? Password::TYPE_PASSWORD_DIGEST : Password::TYPE_PASSWORD_TEXT, $addCreated));

        return $this;
    }

    protected function setNonce(bool $addNonce): self
    {
        if ($addNonce) {
            $nonceValue = $this->getPassword()->getNonceValue();
            if (!empty($nonceValue)) {
                $this->getUsernameToken()->setNonce(new Nonce($nonceValue));
            }
        }

        return $this;
    }

    protected function setCreated(int $addCreated): self
    {
        $passwordDigest = $this->getPassword()->getTypeValue();
        $timestampValue = $this->getPassword()->getTimestampValue();
        if (($addCreated || Password::TYPE_PASSWORD_DIGEST === $passwordDigest) && 0 < $timestampValue) {
            $this->getUsernameToken()->setCreated(new Created($timestampValue));
        }

        return $this;
    }

    protected function setTimestamp(int $addCreated = 0, int $addExpires = 0): self
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

    protected function getUsernameToken(): ?UsernameToken
    {
        return $this->security->getUsernameToken();
    }

    protected function getPassword(): ?Password
    {
        return $this->getUsernameToken()->getPassword();
    }
}
