<?php

namespace WsdlToPhp\WsSecurity;

/**
 * Base class to represent any element that must be included for a WS-Security header.
 * Each element must be named with the actual targeted element tag name.
 * The namespace is also mandatory.
 * Finally the attributes are optional.
 */
class Element
{
    /**
     * Namespace for WSSE elements
     * @var string
     */
    const NS_WSSE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    /**
     * Namespace name for WSSE elements
     * @var string
     */
    const NS_WSSE_NAME = 'wsse';
    /**
     * Namespace for WSSU elements
     * @var string
     */
    const NS_WSSU = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
    /**
     * Namespace name for WSSU elements
     * @var string
     */
    const NS_WSSU_NAME = 'wssu';
    /**
     * Name of the element used as the WS-Security tag
     * @var string
     */
    protected $name = '';
    /**
     * Value of the element.
     * It can either be a string value or a Element object
     * @var string|Element
     */
    protected $value = '';
    /**
     * Array of attributes that must contains the element
     * @var array
     */
    protected $attributes = array();
    /**
     * The namespace the element belongs to
     * @var string
     */
    protected $namespace = '';
    /**
     * Nonce used to generate digest password
     * @var string
     */
    protected $nonceValue = null;
    /**
     * Timestamp used to generate digest password
     * @var int
     */
    protected $timestampValue = null;
    /**
     * Current \DOMDocument used to generate XML content
     * @var \DOMDocument
     */
    protected static $dom = null;
    /**
     * Generic constructor
     * @param string $name
     * @param string $namespace
     * @param mixed $value
     * @param array $attributes
     */
    public function __construct($name, $namespace, $value = null, array $attributes = array())
    {
        $this
            ->setName($name)
            ->setNamespace($namespace)
            ->setValue($value)
            ->setAttributes($attributes);
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     * @return Element
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    /**
     * @param array $attributes
     * @return Element
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
    /**
     * @param string $name
     * @param mixed $value
     * @return Element
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    /**
     * @return bool true|false
     */
    public function hasAttributes()
    {
        return count($this->attributes) > 0;
    }
    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
    /**
     * @return string
     */
    private function getNamespacePrefix()
    {
        switch ($this->getNamespace()) {
            case self::NS_WSSE:
                return self::NS_WSSE_NAME;
            case self::NS_WSSU:
                return self::NS_WSSU_NAME;
        }
    }
    /**
     * @param string $namespace
     * @return Element
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }
    /**
     * @return string|Element
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param string|Element
     * @return Element
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    /**
     * @return string
     */
    public function getNonceValue()
    {
        return $this->nonceValue;
    }
    /**
     * @param string $nonceValue
     * @return Element
     */
    public function setNonceValue($nonceValue)
    {
        $this->nonceValue = $nonceValue;
        return $this;
    }
    /**
     * @return int|string
     */
    public function getTimestampValue($formated = false)
    {
        return ($formated && $this->timestampValue > 0) ? gmdate('Y-m-d\TH:i:s\Z', $this->timestampValue) : $this->timestampValue;
    }
    /**
     * @param int $timestampValue
     * @return Element
     */
    public function setTimestampValue($timestampValue)
    {
        $this->timestampValue = $timestampValue;
        return $this;
    }
    /**
     * @return \DOMDocument
     */
    private static function getDom()
    {
        return self::$dom;
    }
    /**
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    private static function setDom(\DOMDocument $dom)
    {
        self::$dom = $dom;
    }
    /**
     * Method called to generate the string XML request to be sent among the SOAP Header
     * @param bool $asDomElement returns elements as a \DOMElement or as a string
     * @return string|\DOMElement
     */
    protected function __toSend($asDomElement = false)
    {
        /**
         * Create element tag
         */
        $element = self::getDom()->createElement($this->getNamespacedName());
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', sprintf('xmlns:%s', $this->getNamespacePrefix()), $this->getNamespace());
        /**
         * Define element value
         * Add attributes if there are any
         */
        $this
            ->appendValueToElementToSend($this->getValue(), $element)
            ->appendAttributesToElementToSend($element);
        /**
         * Returns element content
         */
        if ($asDomElement) {
            return $element;
        } else {
            return self::getDom()->saveXML($element);
        }
    }
    /**
     * Handle adding value to element according to the value type
     * @param mixed $value
     * @param \DOMElement $element
     * @return Element
     */
    protected function appendValueToElementToSend($value, \DOMElement $element)
    {
        if ($value instanceof Element) {
            $this->appendElementToElementToSend($value, $element);
        } elseif (is_array($value)) {
            $this->appendValuesToElementToSend($value, $element);
        } else {
            $element->appendChild(self::getDom()->createTextNode($value));
        }
        return $this;
    }
    /**
     * @param Element $element
     * @param \DOMElement $element
     */
    protected function appendElementToElementToSend(Element $value, \DOMElement $element)
    {
        $toSend = $value->__toSend(true, false);
        if ($toSend instanceof \DOMElement) {
            $element->appendChild($toSend);
        }
    }
    /**
     * @param array $values
     * @param \DOMElement $element
     */
    protected function appendValuesToElementToSend(array $values, \DOMElement $element)
    {
        foreach ($values as $value) {
            $this->appendValueToElementToSend($value, $element);
        }
    }
    /**
     * @param \DOMElement $element
     * @return Element
     */
    protected function appendAttributesToElementToSend(\DOMElement $element)
    {
        if ($this->hasAttributes()) {
            foreach ($this->getAttributes() as $attributeName=>$attributeValue) {
                $element->setAttribute($attributeName, $attributeValue);
            }
        }
        return $this;
    }
    /**
     * Returns the name with its namespace
     * @return string
     */
    protected function getNamespacedName()
    {
        return sprintf('%s:%s', $this->getNamespacePrefix(), $this->getName());
    }
    /**
     * Returns the element to send as WS-Security header
     * @return string
     */
    public function toSend()
    {
        self::setDom(new \DOMDocument('1.0', 'UTF-8'));
        return $this->__toSend();
    }
}
