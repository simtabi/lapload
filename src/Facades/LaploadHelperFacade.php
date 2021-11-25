<?php

namespace Simtabi\Lapload\Facades;

use Simtabi\Lapload\Helpers\LaploadHelper;
use Illuminate\Support\Facades\Facade;

class LaploadHelperFacade extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaploadHelper::class;
    }
}