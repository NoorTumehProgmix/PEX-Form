<?php


namespace Juzaweb\CMS\Contracts;

use Illuminate\Support\Collection;
use Juzaweb\CMS\Models\Config;
use Juzaweb\CMS\Models\Config as ConfigModel;

interface ConfigContract
{
    public function getConfig(string $key, string|array $default = null): null|string|array;
    
    public function setConfig(string $key, string|array $value = null): ConfigModel;
    
    public function getConfigs(array $keys, string|array $default = null): array;
    
    public function all(): Collection;
}
