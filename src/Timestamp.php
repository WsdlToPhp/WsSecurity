<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

use DOMElement;

class Timestamp extends Element
{
    public const NAME = 'Timestamp';

    protected ?Created $created = null;

    protected ?Expires $expires = null;

    public function __construct(string $namespace = self::NS_WSU)
    {
        parent::__construct(self::NAME, $namespace);
    }

    /**
     * Overrides method in order to add created and expires values if they are set.
     *
     * @param bool $asDomElement returns elements as a DOMElement or as a string
     *
     * @return DOMElement|false|string
     */
    protected function __toSend(bool $asDomElement = false)
    {
        $this->setValue([
            $this->getCreated(),
            $this->getExpires(),
        ]);

        return parent::__toSend($asDomElement);
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

    public function getExpires(): ?Expires
    {
        return $this->expires;
    }

    public function setExpires(Expires $expires): self
    {
        $this->expires = $expires;

        return $this;
    }
}
