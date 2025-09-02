<?php

namespace MorningMedley\Block\Classes;

use Illuminate\Contracts\Container\Container;
use Symfony\Component\Finder\Finder;

class BlockLocator
{

    public function __construct(protected Container $app, protected Finder $finder)
    {

    }

    /**
     * @throws \Exception If $path is not a valid path
     */
    public function locate(string $path): array
    {
        if (! is_dir($path)) {
            throw new \Exception("BlockLocator needs a valid path. \"$path\" supplied is not a path.");
        }
        $blocks = [];
        $this->finder->files()->name('block.json')->in($path);

        if ($this->finder->hasResults()) {
            foreach ($this->finder as $file) {
                $blocks[] = ltrim(str_replace($this->app->basePath(), '', $file->getRealPath()), "/");
            }
        }

        return $blocks;
    }
}
