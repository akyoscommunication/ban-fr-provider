<?php

namespace Akyos\BANFrProvider;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BANFrProviderBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
