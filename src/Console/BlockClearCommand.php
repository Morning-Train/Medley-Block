<?php

namespace MorningMedley\Block\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'block:clear')]
class BlockClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'block:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the block cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->files->delete(\MorningMedley\Facades\Block::getCachePath());

        $this->components->info('Configuration cache cleared successfully.');
    }
}
