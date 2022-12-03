<?php declare(strict_types=1);

namespace adventOfCode\lib;


class InputLoader implements \Iterator {
    private \SplFileObject $file;

    public function __construct(
        string $scriptFileName,
        bool $isSample,
        private  $stripLineEndings = true
    ) {
        $year = \substr($scriptFileName, -11, 4);
        $day = \substr($scriptFileName, -6, 2);
        $sampleFilePrefix = $isSample ? '.sample' : '';
        $fileName = \substr($scriptFileName, 0, -6) . 'inputs/' . $day . $sampleFilePrefix . '.txt';
        if (!$isSample && !file_exists($fileName)) {
            $this->downloadInput((int) $year, (int) $day, $fileName);
        }
        $this->file = new \SplFileObject($fileName);
    }

    private function downloadInput(int $year, int $day, string $fileName): void {
        /* Puzzle inputs differ by user.  Please log in to get your puzzle input.

        $url = "https://adventofcode.com/$year/day/$day/input";
        $fp = \fopen($fileName, 'w');
        $options = [
            CURLOPT_FILE => $fp,
            CURLOPT_URL => $url,
        ];
        $ch = \curl_init();
        \curl_setopt_array($ch, $options);
        \curl_exec($ch);
        \curl_close($ch);
        \fclose($fp);
        */
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