<?php

namespace MorningMedley\Block;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MorningMedley\Block\Classes\Block;
use MorningMedley\Block\Classes\BlockLocator;
use MorningMedley\Block\Console\BlockMakeCommand;

class BlockServiceProvider extends IlluminateServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/config.php", 'block');
        $this->commands([
            BlockMakeCommand::class,
        ]);
    }

    public function boot(): void
    {
        $paths = (array) $this->app['config']->get('block.compiled', []);

        if (empty($paths)) {
            return;
        }

        if (! $this->app->configurationIsCached()) {
            $blocks = $this->app['config']->get('block.blocks', []);
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

                /** @var BlockLocator $locater */
                $locator = $this->app->make(BlockLocator::class);
                $blocks = [...$locator->locate($path), ...$blocks];
            }
            $this->app['config']->set('block.blocks', $blocks);
        }

        /** @var Block $blockClass */
        $this->app->makeWith(Block::class, ['blocks' => $this->app['config']->get('block.blocks')]);
    }
}
