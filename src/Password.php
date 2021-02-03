<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

class Password extends Element
{
    const NAME = 'Password';

    const ATTRIBUTE_TYPE = 'Type';

    const TYPE_PASSWORD_DIGEST = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';

    const TYPE_PASSWORD_TEXT = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';

    protected string $typeValue;

    public function __construct(string $password, string $typeValue = self::TYPE_PASSWORD_TEXT, int $timestampValue = 0, string $namespace = self::NS_WSSE)
    {
        $this
            ->setTypeValue($typeValue)
            ->setTimestampValue($timestampValue ? $timestampValue : time())
            ->setNonceValue((string) mt_rand())
        ;

        parent::__construct(self::NAME, $namespace, $this->convertPassword($password), [
            self::ATTRIBUTE_TYPE => $typeValue,
        ]);
    }

    public function convertPassword(string $password): string
    {
        if (self::TYPE_PASSWORD_DIGEST === $this->getTypeValue()) {
            $password = $this->digestPassword($password);
        }

        return $password;
    }

    /**
     * When generating the password digest, we define values (nonce and timestamp) that can be used in other place.
     */
    public function digestPassword(string $password): string
    {
        $packedNonce = pack('H*', $this->getNonceValue());
        $packedTimestamp = pack('a*', $this->getTimestampValue(true));
        $packedPassword = pack('a*', $password);
        $hash = sha1($packedNonce.$packedTimestamp.$packedPassword);
        $packedHash = pack('H*', $hash);

        return base64_encode($packedHash);
    }

    public function getTypeValue(): string
    {
        return $this->typeValue;
    }

    public function setTypeValue(string $typeValue): self
    {
        $this->typeValue = $typeValue;

        return $this;
    }
}
