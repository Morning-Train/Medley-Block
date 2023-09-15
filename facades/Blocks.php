<?php

namespace Morningtrain\WP\Facades;

use Illuminate\Support\Facades\Facade;
use Morningtrain\WP\Blocks\Classes\Blocks as BlocksInstance;

/**
 * @method static registerBlocksPath(string $path): BlocksInstance
 * @method static container(): Container
 */
class Blocks extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp-blocks';
    }
}
