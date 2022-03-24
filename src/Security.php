<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

use DOMElement;

class Security extends Element
{
    const NAME = 'Security';

    const ATTRIBUTE_MUST_UNDERSTAND = ':mustunderstand';

    const ATTRIBUTE_ACTOR = ':actor';

    const ENV_NAMESPACE = 'SOAP-ENV';

    protected ?UsernameToken $usernameToken = null;

    protected ?Timestamp $timestamp = null;

    public function __construct(bool $mustUnderstand = false, ?string $actor = null, string $namespace = self::NS_WSSE, string $envelopeNamespace = self::ENV_NAMESPACE)
    {
        parent::__construct(self::NAME, $namespace);

        if (true === $mustUnderstand) {
            $this->setAttribute($envelopeNamespace.self::ATTRIBUTE_MUST_UNDERSTAND, $mustUnderstand);
        }

        if (!empty($actor)) {
            $this->setAttribute($envelopeNamespace.self::ATTRIBUTE_ACTOR, $actor);
        }
    }

    /**
     * Overrides methods in order to set the values.
     *
     * @param bool $asDomElement returns elements as a DOMElement or as a string
     *
     * @return DOMElement|string|false
     */
    protected function __toSend(bool $asDomElement = false)
    {
        $this->setValue([
            $this->getUsernameToken(),
            $this->getTimestamp(),
        ]);

        return parent::__toSend($asDomElement);
    }

    public function getUsernameToken(): ?UsernameToken
    {
        return $this->usernameToken;
    }

    public function setUsernameToken(UsernameToken $usernameToken): self
    {
        $this->usernameToken = $usernameToken;

        return $this;
    }

    public function getTimestamp(): ?Timestamp
    {
        return $this->timestamp;
    }

    public function setTimestamp(Timestamp $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
