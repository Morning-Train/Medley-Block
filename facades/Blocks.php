<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;
use MorningMedley\Block\Classes\Blocks as BlocksInstance;

/**
 * @method static registerBlocksPath(string $path): BlocksInstance
 * @method static deleteCache(): bool
 *
 * @see \MorningMedley\Block\Classes\Blocks
 */
class Blocks extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'block';
    }
}
