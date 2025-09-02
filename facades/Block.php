<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;

/**

 */
class Block extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MorningMedley\Block\Block::class;
    }
}
