<?php

namespace MorningMedley\Block;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Block\Classes\BlockLocator;
use MorningMedley\Block\Console\BlockCacheCommand;
use MorningMedley\Block\Console\BlockClearCommand;
use MorningMedley\Block\Console\BlockMakeCommand;
use MorningMedley\Facades\Block as BlockFacade;

class BlockServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'block');

        BlockFacade::setFacadeApplication($this->app);
        $this->app->singleton(Block::class);
    }

    public function boot(): void
    {
        /** @var Block $blockClass */
        $this->app->make(Block::class)->boot();

        if ($this->app->runningInConsole()) {
            $this->optimizes(
                optimize: 'block:cache',
                clear: 'block:clear',
            );
        }

        $this->commands([
            BlockMakeCommand::class,
            BlockClearCommand::class,
            BlockCacheCommand::class,
        ]);
    }
}
