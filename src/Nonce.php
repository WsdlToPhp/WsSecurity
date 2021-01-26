<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

class Nonce extends Element
{
    const NAME = 'Nonce';

    const ATTRIBUTE_ENCODING_TYPE = 'EncodingType';

    const NS_ENCODING = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

    public function __construct(string $nonce, string $namespace = self::NS_WSSE)
    {
        parent::__construct(self::NAME, $namespace, self::encodeNonce($nonce), [
            self::ATTRIBUTE_ENCODING_TYPE => self::NS_ENCODING,
        ]);
    }

    public static function encodeNonce(string $nonce): string
    {
        return base64_encode(pack('H*', $nonce));
    }
}
