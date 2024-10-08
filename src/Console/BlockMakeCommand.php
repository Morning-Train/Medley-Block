<?php

namespace MorningMedley\Block\Console;

use Illuminate\Filesystem\Filesystem;
use MorningMedley\Application\WpContext\WpContextContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:block')]
class BlockMakeCommand extends \Illuminate\Console\Command
{
    protected $name = 'make:block';
    protected $description = 'Create a new block';

    public function __construct(protected WpContextContract $wpContext, protected Filesystem $filesystem)
    {
        parent::__construct();

    }

    public function handle()
    {
        $name = $this->argument('name');                   // The display name of the block
        $safeName = \sanitize_key($name);                       // The safe name used in "namespace"
        $domain = $this->option('domain');                 // The block domain. Usually same as context's text domain
        $description = $this->option('description');       // The block description
        $viewportWidth = $this->option('viewportwidth');   // Width for example rendering
        $category = $this->option('category');             // Block category
        $blockCssClass = "wp-block-{$domain}-{$safeName}";      // The CSS class that WordPress will assign
        $blockType = $this->option('type');                // The type of block. static, dynamic or hybrid

        $replaces = [
            '{{ name }}' => $name,
            '{{ safename }}' => $safeName,
            '{{ domain }}' => $domain,
            '{{ description }}' => $description,
            '{{ viewportwidth }}' => $viewportWidth,
            '{{ cssclass }}' => $blockCssClass,
            '{{ category }}' => $category,
            '{{ blockmetaextradeps }}' => '',
        ];

        $stubs = [
            '/stubs/block/blockmeta.stub' => 'block.json',
            '/stubs/block/deprecated.stub' => 'deprecated.js',
            '/stubs/block/edit.stub' => 'edit.js',
            '/stubs/block/editorstyle.stub' => 'editor.scss',
            '/stubs/block/mainstyle.stub' => 'style.scss',
            '/stubs/block/icon.stub' => 'icon.js',
            '/stubs/block/index.stub' => 'index.js',
            '/stubs/block/save.stub' => 'save.js',
            '/stubs/block/variations.stub' => 'variations.js',
        ];

        $blockPath = $this->getBlockPath($safeName);

        if (! is_dir($blockPath)) {
            if (mkdir($blockPath, 0777, true)) {
                \WP_CLI::line(\WP_CLI::colorize("Created block directory: %g{$blockPath}%n"));
            } else {
                \WP_CLI::error(\WP_CLI::colorize("Could not create missing block directory: %r{$blockPath}%n"));

                return;
            }
        }

        if (in_array($blockType, ['dynamic', 'hybrid'])) {
            $stub = '/stubs/block/view.stub';
            $fileName = $safeName . ".blade.php";
            $stubPath = $this->resolveStubPath($stub);
            $dir = \trailingslashit($this->laravel['config']->get('view.paths')[0]) . "blocks/" . $safeName . "/";
            mkdir($dir, 0777, true);
            $file = $dir . $fileName;
            $this->createStubFile($stubPath, $file, $replaces);
            $replaces['{{ blockmetaextradeps }}'] .= '"renderView": "blocks.'.$safeName.'.'.$safeName.'",';
        }

        if($blockType === 'dynamic'){
            $stubs['/stubs/block/dynamic-index.stub'] = $stubs['/stubs/block/index.stub'];
            unset($stubs['/stubs/block/index.stub']);
            unset($stubs['/stubs/block/save.stub']);
        }

        foreach ($stubs as $stub => $fileName) {
            $stubPath = $this->resolveStubPath($stub);
            $file = \trailingslashit($blockPath) . $fileName;
            $this->createStubFile($stubPath, $file, $replaces);
        }

        \WP_CLI::success('Created block: ' . $name);
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Specify the name for your block'],
        ];
    }

    protected function getOptions()
    {
        return [
            [
                'domain',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Specify the text- and blockdomain',
                $this->wpContext->textDomain,
            ],
            ['category', 'c', InputOption::VALUE_OPTIONAL, 'Specify the blockcategory', 'widgets'],
            ['description', 'm', InputOption::VALUE_OPTIONAL, 'Specify the description', ''],
            ['viewportwidth', 'w', InputOption::VALUE_OPTIONAL, 'Specify viewport width for examle view', 1200],
            ['type', 't', InputOption::VALUE_OPTIONAL, 'Specify the block type. static, dynamic, hybrid', 'static'],
        ];
    }

    protected function getBlockPath(string $path): string
    {
        $blocks = $this->laravel['config']['block.paths'][0] ?? resource_path("blocks");

        return $blocks . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function createStubFile(string $stubPath, string $toPath, array $replaces)
    {
        if ($this->copyAndReplaceStub($stubPath, $toPath, $replaces)) {
            \WP_CLI::line(\WP_CLI::colorize("Created block file: %g{$toPath}%n"));
        } else {
            \WP_CLI::error(\WP_CLI::colorize("Could not create block file: %r{$toPath}%n"));
        }
    }

    protected function copyAndReplaceStub(string $stub, string $toPath, array $replaces)
    {
        $content = file_get_contents($stub);
        $content = str_replace(array_keys($replaces), array_values($replaces), $content);

        $status = file_put_contents($toPath, $content);

        return (is_numeric($status) && $status > 0) && $status !== false;
    }
}
