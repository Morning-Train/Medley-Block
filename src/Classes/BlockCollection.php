<?php

namespace MorningMedley\Block\Classes;

use Illuminate\Contracts\Foundation\Application;

class BlockCollection
{
    /**
     * @var string[]
     */
    private array $blocks = [];

    public function __construct(protected Application $app)
    {

    }

    /**
     * Add one or more files
     *
     * @param  string|string[]  $files
     * @return void
     */
    public function add(string|array $files): void
    {
        $this->blocks = [...$this->blocks, ...(array) $files];
    }

    /**
     * Get current list
     *
     * @return string[]
     */
    public function list(): array
    {
        return $this->blocks;
    }

    /**
     * Empty the current list
     *
     * @return void
     */
    public function clear(): void
    {
        $this->blocks = [];
    }
}
