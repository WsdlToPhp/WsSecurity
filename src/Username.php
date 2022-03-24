<?php

declare(strict_types=1);

namespace WsdlToPhp\WsSecurity;

class Username extends Element
{
    public const NAME = 'Username';

    public function __construct(string $username, string $namespace = self::NS_WSSE)
    {
        parent::__construct(self::NAME, $namespace, $username);
    }
}
