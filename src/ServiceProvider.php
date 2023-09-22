<?php

namespace MorningMedley\Blocks;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Blocks\Classes\BlockRegistrator;
use MorningMedley\Blocks\Classes\Blocks;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use MorningMedley\Facades\Blocks as BlocksFacade;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        BlocksFacade::setFacadeApplication($this->app);

        $this->app->singleton('wp-blocks',
            fn($container) => new Blocks($container, new BlockRegistrator(),
                new PhpFilesAdapter('wp-blocks', DAY_IN_SECONDS, __DIR__ . "/_php_cache")));

        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'wp-blocks');
    }

    public function boot(): void
    {
        foreach ((array) $this->app->get('config')->get('wp-blocks.path') as $path) {
            BlocksFacade::registerBlocksPath($this->app->basePath($path));
        }
    }
}
