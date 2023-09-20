<?php

namespace Morningtrain\WP\Blocks;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Morningtrain\WP\Blocks\Classes\BlockRegistrator;
use Morningtrain\WP\Blocks\Classes\Blocks;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Morningtrain\WP\Facades\Blocks as BlocksFacade;

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
        BlocksFacade::registerBlocksPath($this->app->basePath($this->app->get('config')->get('wp-blocks.path')));
    }
}
