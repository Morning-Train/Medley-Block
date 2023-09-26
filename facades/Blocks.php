<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;
use MorningMedley\Blocks\Classes\Blocks as BlocksInstance;

/**
 * @method static registerBlocksPath(string $path): BlocksInstance
 * @method static deleteCache(): bool
 *
 * @see \MorningMedley\Blocks\Classes\Blocks
 */
class Blocks extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'blocks';
    }
}
