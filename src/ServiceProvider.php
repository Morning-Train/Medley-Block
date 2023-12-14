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
            if (! is_dir($path)) {
                $path = $this->app->basePath($path);
            }
            $blockClass->registerBlocksPath($path);
        }
    }
}
