<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

class Created extends Element
{
    const NAME = 'Created';

    public function __construct(int $timestamp, string $namespace = self::NS_WSSU)
    {
        $this->setTimestampValue($timestamp);
        parent::__construct(self::NAME, $namespace, $this->getTimestampValue(true));
    }
}
