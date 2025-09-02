<?php

namespace MorningMedley\Block\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use MorningMedley\Application\WpContext\WpContextContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'block:cache')]
class BlockCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'block:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster block loading';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config cache command instance.
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
     *
     * @throws \LogicException
     */
    public function handle(): void
    {
        // Clear existing hook file
        $this->callSilent('block:clear');

        $this->laravel->forgetInstance(\MorningMedley\Block\Block::class);
        $block = $this->laravel->make(\MorningMedley\Block\Block::class);
        $block->locate();

        $configPath = $block->getCachePath();

        $success = $this->files->put(
            $configPath, '<?php return ' . var_export($block->blocks(), true) . ';' . PHP_EOL
        );

        if ($success === false) {
            $this->components->error('Failed to write cache file.');
        }

        $this->components->info('Blocks cached successfully.');
    }
}
