<?php

namespace MorningMedley\Block\Console;

use MorningMedley\Application\WpContext\WpContextContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:block')]
class BlockMakeCommand extends \Illuminate\Console\Command{
    protected $name  = 'make:block';
    protected $description = 'Create a new block';

    public function __construct(private WpContextContract $wpContext)
    {

        parent::__construct();
    }

    public function handle()
    {
        // domain - Text domain and "namespace"
        // safename - The block name for the "namespace" lowercase and not special chars
        // name - Block name for displaying
        // category - Should have a nice default "widgets"
        // description -
        // viewportwidth - int. Should have a default 1200
        // CSS block class should be calculated and made available for .scss files

        $name = $this->argument('name');
        $safename = \sanitize_key($name);
        $domain = $this->option('domain');
        $description = $this->option('description');
        $viewportWidth = $this->option('viewportwidth');

        \WP_CLI::line('Name is: '.$name);
        \WP_CLI::line('Safename is: '.$safename);
        \WP_CLI::line('Domain is: '.$domain);
        \WP_CLI::line('Description is: '.$description);
        \WP_CLI::line('Viewportwidth is: '.$viewportWidth);
        \WP_CLI::success(' I got called, yay');
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Specify the name for your block']
        ];
    }

    protected function getOptions()
    {
        return [
            ['domain','d', InputOption::VALUE_OPTIONAL, 'Specify the text- and blockdomain', $this->wpContext->textDomain],
            ['category','c', InputOption::VALUE_OPTIONAL, 'Specify the blockcategory', 'widgets'],
            ['description','m', InputOption::VALUE_OPTIONAL, 'Specify the description', ''],
            ['viewportwidth','w', InputOption::VALUE_OPTIONAL, 'Specify viewport width for examle view', 1200],
        ];
    }
}
