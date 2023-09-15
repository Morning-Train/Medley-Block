<?php

namespace src;

use Illuminate\Support\ServiceProvider;
use Morningtrain\WP\Blocks\Classes\Blocks;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

class BlocksServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->when(Blocks::class)
            ->needs(AbstractAdapter::class)
            ->give(PhpFilesAdapter::class);
    }
}
