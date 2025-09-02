<?php

namespace MorningMedley\Facades;

use Illuminate\Support\Facades\Facade;
use MorningMedley\Block\Classes\BlockCollection;

/**
 * @method static void init()                              // Initialize the block class
 * @method static void locate()                            // Locate block files in known paths
 * @method static void add(string|array $block)            // Add block(s) must be relative path to block.json file
 * @method static string[] blocks()                        // Get list of relative block.json paths
 * @method static string[] absoluteBlocks()                // Get list of absolute block.json paths
 * @method static void registerBlocksPath(string $path)    // Register a path to look for blocks in
 * @method static string[] getBlocksPaths()                // Get paths
 * @method static string getCachePath()                    // Get path for cache file
 * @method static bool blocksAreCached()                   // Check if cache file exists
 * @method static BlockCollection blockCollection()        // Get the current BlockCollection
 */
class Block extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MorningMedley\Block\Block::class;
    }
}
