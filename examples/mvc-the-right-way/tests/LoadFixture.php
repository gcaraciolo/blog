<?php

namespace Tests;

use Illuminate\Support\Str;

trait LoadFixture
{
    protected function loadFixture(string $filename): array
    {
        $path = __DIR__ . '/Fixtures/' . $filename;

        if (Str::endsWith($filename, '.json')) {
            return json_decode(file_get_contents($path), true);
        } else if (Str::endsWith($filename, '.php')) {
            return require $path;
        }
    }
}
