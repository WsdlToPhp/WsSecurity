<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

use DOMElement;

class UsernameToken extends Element
{
    const NAME = 'UsernameToken';

    const ATTRIBUTE_ID = 'Id';

    protected ?Username $username = null;

    protected ?Password $password = null;

    protected ?Created $created = null;

    protected ?Nonce $nonce = null;

    public function __construct(?string $id = null, string $namespace = self::NS_WSSE)
    {
        parent::__construct(self::NAME, $namespace, null, empty($id) ? [] : [
            sprintf('%s:%s', parent::NS_WSSU_NAME, self::ATTRIBUTE_ID) => $id,
        ]);
    }

    /**
     * Overrides method in order to add username, password and created values if they are set.
     *
     * @param bool $asDomElement returns elements as a DOMElement or as a string
     *
     * @return DOMElement|string|false
     */
    protected function __toSend(bool $asDomElement = false)
    {
        $this->setValue([
            $this->getUsername(),
            $this->getPassword(),
            $this->getCreated(),
            $this->getNonce(),
        ]);

        return parent::__toSend($asDomElement);
    }

    public function getUsername(): ?Username
    {
        return $this->username;
    }

    public function setUsername(Username $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?Password
    {
        return $this->password;
    }

    public function setPassword(Password $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreated(): ?Created
    {
        return $this->created;
    }

    public function setCreated(Created $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getNonce(): ?Nonce
    {
        return $this->nonce;
    }

    public function setNonce(Nonce $nonce): self
    {
        $this->nonce = $nonce;

        return $this;
    }
}
