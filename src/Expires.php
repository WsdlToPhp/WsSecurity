<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

class Expires extends Element
{
    const NAME = 'Expires';

    public function __construct(int $timestamp, int $expiresIn = 3600, string $namespace = self::NS_WSSU)
    {
        $this->setTimestampValue($timestamp + $expiresIn);
        parent::__construct(self::NAME, $namespace, $this->getTimestampValue(true));
    }
}
