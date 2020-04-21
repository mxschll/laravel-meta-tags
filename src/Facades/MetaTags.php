<?php

namespace mxschll\MetaTags\Facades;

use Illuminate\Support\Facades\Facade;

class MetaTags extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'meta-tags';
    }
}
