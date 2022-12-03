<?php declare(strict_types=1);

namespace adventOfCode\lib;


class Dumper {
    private static $stdin;

    public static function dump(...$args): void {
        \var_dump(...$args);
        self::waitForInput();
    }

    private static function waitForInput(): void {
        if (!isset(self::$stdin)) {
            self::$stdin = fopen('php://stdin', 'r');
        }
        \fgets(self::$stdin);
    }
}