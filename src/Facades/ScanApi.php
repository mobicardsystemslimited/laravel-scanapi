<?php

namespace MobicardApi\ScanApi\Facades;

use Illuminate\Support\Facades\Facade;

class ScanApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'scanapi';
    }
}
