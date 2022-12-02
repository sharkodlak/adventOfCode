<?php declare(strict_types=1);

namespace adventOfCode\lib;


class InputLoader implements \Iterator {
    private \SplFileObject $file;

    public function __construct(
        string $scriptFileName,
        bool $isSample,
        private  $stripLineEndings = true
    ) {
        $sampleFilePrefix = $isSample ? '.sample' : '';
        $fileName = \substr($scriptFileName, 0, -6) . 'inputs/' . \substr($scriptFileName, -6, 2) . $sampleFilePrefix . '.txt';
        $this->file = new \SplFileObject($fileName);
    }

    public function current(): mixed {
        $line = $this->file->current();
        if ($this->stripLineEndings && \substr($line, -1) === "\n") {
            $line = \substr($line, 0, -1);
        }
        return $line;
    }

    public function key(): mixed {
        return $this->file->key();
    }

    public function next(): void {
        $this->file->next();
    }

    public function rewind(): void {
        $this->file->rewind();
    }

    public function valid(): bool {
        return $this->file->valid();
    }
}