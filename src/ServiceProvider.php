<?php

namespace MorningMedley\Block;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Block\Classes\BlockRegistrator;
use MorningMedley\Block\Classes\Block;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use MorningMedley\Facades\Block as BlockFacade;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        BlockFacade::setFacadeApplication($this->app);
        $this->app->singleton('block',
            fn($container) => new Block($container, new BlockRegistrator(),
                $this->app->make('file.cache', ['namespace' => 'block', 'defaultLifetime' => DAY_IN_SECONDS])));

        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'block');
    }

    public function boot(): void
    {
        foreach ((array) $this->app->make('config')->get('block.paths') as $path) {
            BlockFacade::registerBlocksPath($this->app->basePath($path));
        }
    }
}
