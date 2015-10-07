<?php

namespace WsdlToPhp\WsSecurity;

class Timestamp extends Element
{
    /**
     * Element name
     * @var string
     */
    const NAME = 'Timestamp';
    /**
     * Created element
     * @var Created
     */
    protected $created = null;
    /**
     * Created element
     * @var Expires
     */
    protected $expires = null;
    /**
     * Constructor for Timestamp element
     * @param string $namespace the namespace
     */
    public function __construct($namespace = self::NS_WSSU)
    {
        parent::__construct(self::NAME, $namespace);
    }
    /**
     * @return Created
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * @param Created $created
     * @return Timestamp
     */
    public function setCreated(Created $created)
    {
        $this->created = $created;
        return $this;
    }
    /**
     * @return Expires
     */
    public function getExpires()
    {
        return $this->expires;
    }
    /**
     * @param Expires $expires
     * @return Expires
     */
    public function setExpires(Expires $expires)
    {
        $this->expires = $expires;
        return $this;
    }
    /**
     * Overrides method in order to add created and expires values if they are set
     * @param bool $asDomElement returns elements as a DOMElement or as a string
     * @param bool $rootNode OPTIONAL, don't pass the parameter
     * @return string
     */
    protected function __toSend($asDomElement = false, $rootNode = true)
    {
        $value = array();
        if ($this->getCreated() instanceof Created) {
            array_push($value, $this->getCreated());
        }
        if ($this->getExpires() instanceof Expires) {
            array_push($value, $this->getExpires());
        }
        if (count($value) > 0) {
            $this->setValue($value);
        }
        return parent::__toSend($asDomElement, $rootNode);
    }
}
