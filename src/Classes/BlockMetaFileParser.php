<?php

namespace Morningtrain\WP\Blocks\Classes;

class BlockMetaFileParser
{
    protected string $metaFile;
    protected array $metaData = [];

    /**
     * @param  string  $metaFile  Full path for the block.json meta file
     * @throws \Exception If $metaFile does not exist
     */
    public function __construct(string $metaFile)
    {
        if (! file_exists($metaFile)) {
            throw new \Exception("BlockMetaFileParse needs a metafile. File \"$metaFile\" does not exist.");
        }

        $this->metaFile = $metaFile;
        $this->loadFile();
    }

    protected function loadFile(): void
    {
        $this->metaData = \wp_json_file_decode($this->metaFile, ['associative' => true]);
    }

    public function parseFileProperty(string $property): array
    {
        if (! isset($this->metaData[$property])) {
            return [];
        }

        return array_filter(array_map([$this, 'parseFileString'], (array) $this->metaData[$property]));
    }

    public function parseFileString(string $fileString): ?string
    {
        if (! str_starts_with($fileString, 'file:')) {
            return null;
        }

        $realpath = realpath(dirname($this->metaFile) . "/" . str_replace('file:', '', $fileString));
        if ($realpath === false) {
            return null;
        }

        return $realpath;
    }
}
