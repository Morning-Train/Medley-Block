<?php

namespace Morningtrain\WP\Blocks;

use Illuminate\Support\ServiceProvider;
use Morningtrain\WP\Blocks\Classes\Blocks;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

class BlocksServiceProvider extends ServiceProvider
{
    public function register()
    {
        return true;
    }
}
