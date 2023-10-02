<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;
use MorningMedley\Block\Classes\Block as BlocksInstance;

/**
 * @method static registerBlocksPath(string $path): BlocksInstance
 * @method static deleteCache(): bool
 *
 * @see \MorningMedley\Block\Classes\Block
 */
class Block extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'block';
    }
}
