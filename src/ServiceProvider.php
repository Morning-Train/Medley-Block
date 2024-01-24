<?php

namespace MorningMedley\Block;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Block\Classes\BlockRegistrator;
use MorningMedley\Block\Classes\Block;
use MorningMedley\Block\Classes\Cli;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use MorningMedley\Facades\Block as BlockFacade;
use function MorningMedley\Functions\config;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'block');
    }

    public function boot(): void
    {
        $paths = (array) config('block.paths', []);

        if (empty($paths)) {
            return;
        }

        /** @var Block $blockClass */
        $cache = $this->app->make('filecachemanager')->getCache('block');
        $blockClass = $this->app->makeWith(Block::class, [
            'cache' => $cache,
        ]);

        foreach ($paths as $path) {
            // If the path is not a directory then it might be a relative path
            if (! is_dir($path)) {
                // Prepend path with project basepath
                $path = $this->app->basePath($path);
                // If this is still not a dir, then continue instead of crashing
                if (! is_dir($path)) {
                    continue;
                }
            }


            $blockClass->registerBlocksPath($path);
        }
    }
}
