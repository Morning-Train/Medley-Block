<?php

namespace Morningtrain\WP\Facades;

use Illuminate\Support\Facades\Facade;
use Morningtrain\WP\Blocks\Classes\Blocks as BlocksInstance;

/**
 * @method static registerBlocksPath(string $path): BlocksInstance
 * @method static deleteCache(): bool
 *
 * @see \Morningtrain\WP\Blocks\Classes\Blocks
 */
class Blocks extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp-blocks';
    }
}