<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method void init()                              // Initialize the block class
 * @method void locate()                            // Locate block files in known paths
 * @method void add(string|array $block)            // Add block(s) must be relative path to block.json file
 * @method void registerBlocksPath(string $path)    // Register a path to look for blocks in
 * @method array getBlocksPaths()                   // Get paths
 * @method string getCachePath()                    // Get path for cache file
 * @method bool blocksAreCached()                   // Check if cache file exists
 */
class Block extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MorningMedley\Block\Block::class;
    }
}
