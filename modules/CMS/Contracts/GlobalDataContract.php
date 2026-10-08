<?php


namespace Juzaweb\CMS\Contracts;

interface GlobalDataContract
{
    public function set($key, $value);

    public function get($key, $default = []);

    public function all(): array;
}
