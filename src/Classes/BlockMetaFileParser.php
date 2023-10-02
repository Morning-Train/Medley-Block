<?php

namespace MorningMedley\Block\Classes;

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
        $this->metaFile = $metaFile;
        $this->loadFile();
    }

    public function loadFile(): void
    {
        if (! file_exists($this->metaFile)) {
            throw new \Exception("BlockMetaFileParse needs a metafile. File \"{$this->metaFile}\" does not exist.");
        }

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

        $realpath = $this->resolveRealpath($fileString);
        if ($realpath === false) {
            return null;
        }

        return $realpath;
    }

    public function resolveRealpath(string $fileString): false|string
    {
        return realpath(dirname($this->metaFile) . "/" . str_replace('file:', '', $fileString));
    }
}
