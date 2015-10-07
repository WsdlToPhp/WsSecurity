<?php

namespace WsdlToPhp\WsSecurity;

class Security extends Element
{
    /**
     * Element name
     * @var string
     */
    const NAME = 'Security';
    /**
     * Element attribute mustunderstand name
     * @var string
     */
    const ATTRIBUTE_MUST_UNDERSTAND = 'SOAP-ENV:mustunderstand';
    /**
     * Element attribute mustunderstand name
     * @var string
     */
    const ATTRIBUTE_ACTOR = 'SOAP-ENV:actor';
    /**
     * UsernameToken element
     * @var UsernameToken
     */
    protected $usernameToken;
    /**
     * Timestamp element
     * @var Timestamp
     */
    protected $timestamp;
    /**
     * Constructor for Nonce element
     * @param bool $mustunderstand
     * @param string $actor
     * @param string $namespace the namespace
     */
    public function __construct($mustunderstand = false, $actor = null, $namespace = self::NS_WSSE)
    {
        parent::__construct(self::NAME, $namespace);
        /**
         * Sets attributes
         */
        if ($mustunderstand === true) {
            $this->setAttribute(self::ATTRIBUTE_MUST_UNDERSTAND, $mustunderstand);
        }
        if (!empty($actor)) {
            $this->setAttribute(self::ATTRIBUTE_ACTOR, $actor);
        }
    }
    /**
     * @return UsernameToken
     */
    public function getUsernameToken()
    {
        return $this->usernameToken;
    }
    /**
     * @param UsernameToken $usernameToken
     * @return Security
     */
    public function setUsernameToken(UsernameToken $usernameToken)
    {
        $this->usernameToken = $usernameToken;
        return $this;
    }
    /**
     * @return Timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    /**
     * @param Timestamp $timestamp
     * @return Security
     */
    public function setTimestamp(Timestamp $timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }
    /**
     * Overrides methods in order to set the values
     * @param bool $asDomElement returns elements as a DOMElement or as a string
     * @return string
     */
    protected function __toSend($asDomElement = false)
    {
        $value = array();
        if ($this->getUsernameToken()) {
            $value[] = $this->getUsernameToken();
        }
        if ($this->getTimestamp()) {
            $value[] = $this->getTimestamp();
        }
        if (count($value)) {
            $this->setValue($value);
        }
        return parent::__toSend($asDomElement = false);
    }
}
