<?php


namespace Juzaweb\CMS\Contracts;

use Illuminate\Support\Collection;
use Juzaweb\CMS\Support\Theme;

interface LocalThemeRepositoryContract
{
    public function scan(bool $collection = false): array|Collection;

    public function find(string $name): ?Theme;

    public function all(bool $collection = false): array|Collection;
}
