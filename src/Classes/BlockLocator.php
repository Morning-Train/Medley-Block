<?php

namespace MorningMedley\Block\Classes;

use Symfony\Component\Finder\Finder;

class BlockLocator
{
    private Finder $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
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
                $blocks[] = $file->getRealPath();
            }
        }

        return $blocks;
    }
}
