<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

use DOMDocument;
use DOMElement;

/**
 * Base class to represent any element that must be included for a WS-Security header.
 * Each element must be named with the actual targeted element tag name.
 * The namespace is also mandatory.
 * Finally the attributes are optional.
 */
class Element
{
    const NS_WSSE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    const NS_WSSE_NAME = 'wsse';

    const NS_WSSU = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

    const NS_WSSU_NAME = 'wssu';

    protected string $name = '';

    /**
     * Value of the element.
     * It can either be a string value or a Element object.
     *
     * @var Element|string
     */
    protected $value = '';

    /**
     * Array of attributes that must contains the element.
     *
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    /**
     * The namespace the element belongs to.
     */
    protected string $namespace = '';

    /**
     * Nonce used to generate digest password.
     */
    protected string $nonceValue;

    /**
     * Timestamp used to generate digest password.
     */
    protected int $timestampValue;

    /**
     * Current DOMDocument used to generate XML content.
     */
    protected static ?DOMDocument $dom = null;

    /**
     * @param mixed                $value
     * @param array<string, mixed> $attributes
     */
    public function __construct(string $name, string $namespace, $value = null, array $attributes = [])
    {
        $this
            ->setName($name)
            ->setNamespace($namespace)
            ->setValue($value)
            ->setAttributes($attributes)
        ;
    }

    /**
     * Method called to generate the string XML request to be sent among the SOAP Header.
     *
     * @param bool $asDomElement returns elements as a \DOMElement or as a string
     *
     * @return DOMElement|string|false
     */
    protected function __toSend(bool $asDomElement = false)
    {
        // Create element tag.
        $element = self::getDom()->createElement($this->getNamespacedName());
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', sprintf('xmlns:%s', $this->getNamespacePrefix()), $this->getNamespace());

        // Define element value, add attributes if there are any
        $this
            ->appendValueToElementToSend($this->getValue(), $element)
            ->appendAttributesToElementToSend($element)
        ;

        return $asDomElement ? $element : self::getDom()->saveXML($element);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return Element
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function hasAttributes(): bool
    {
        return 0 < count($this->attributes);
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @return Element|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return Element
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getNonceValue(): string
    {
        return $this->nonceValue;
    }

    public function setNonceValue(string $nonceValue): self
    {
        $this->nonceValue = $nonceValue;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getTimestampValue(bool $formatted = false)
    {
        return ($formatted && $this->timestampValue > 0) ? gmdate('Y-m-d\TH:i:s\Z', $this->timestampValue) : $this->timestampValue;
    }

    public function setTimestampValue(int $timestampValue): self
    {
        $this->timestampValue = $timestampValue;

        return $this;
    }

    /**
     * Returns the element to send as WS-Security header.
     *
     * @return DOMElement|string|false
     */
    public function toSend()
    {
        self::setDom(new DOMDocument('1.0', 'UTF-8'));

        return $this->__toSend();
    }

    /**
     * Handle adding value to element according to the value type.
     *
     * @param mixed $value
     *
     * @return Element
     */
    protected function appendValueToElementToSend($value, DOMElement $element): self
    {
        if ($value instanceof Element) {
            $this->appendElementToElementToSend($value, $element);
        } elseif (is_array($value)) {
            $this->appendValuesToElementToSend($value, $element);
        } elseif (!empty($value)) {
            $element->appendChild(self::getDom()->createTextNode($value));
        }

        return $this;
    }

    protected function appendElementToElementToSend(Element $value, DOMElement $element): void
    {
        $toSend = $value->__toSend(true);
        if ($toSend instanceof DOMElement) {
            $element->appendChild($toSend);
        }
    }

    /**
     * @param array<mixed> $values
     */
    protected function appendValuesToElementToSend(array $values, DOMElement $element): void
    {
        foreach ($values as $value) {
            $this->appendValueToElementToSend($value, $element);
        }
    }

    protected function appendAttributesToElementToSend(DOMElement $element): self
    {
        if (!$this->hasAttributes()) {
            return $this;
        }

        foreach ($this->getAttributes() as $attributeName => $attributeValue) {
            $matches = [];
            if (0 === preg_match(sprintf('/(%s|%s):/', self::NS_WSSU_NAME, self::NS_WSSE_NAME), $attributeName, $matches)) {
                $element->setAttribute($attributeName, (string) $attributeValue);
            } else {
                $element->setAttributeNS(self::NS_WSSE_NAME === $matches[1] ? self::NS_WSSE : self::NS_WSSU, $attributeName, $attributeValue);
            }
        }

        return $this;
    }

    /**
     * Returns the name with its namespace.
     */
    protected function getNamespacedName(): string
    {
        return sprintf('%s:%s', $this->getNamespacePrefix(), $this->getName());
    }

    private function getNamespacePrefix(): string
    {
        $namespacePrefix = '';

        switch ($this->getNamespace()) {
            case self::NS_WSSE:
                $namespacePrefix = self::NS_WSSE_NAME;

                break;

            case self::NS_WSSU:
                $namespacePrefix = self::NS_WSSU_NAME;

                break;
        }

        return $namespacePrefix;
    }

    private static function getDom(): ?DOMDocument
    {
        return self::$dom;
    }

    private static function setDom(DOMDocument $dom): void
    {
        self::$dom = $dom;
    }
}
