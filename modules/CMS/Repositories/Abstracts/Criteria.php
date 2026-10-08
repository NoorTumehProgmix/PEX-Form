<?php


namespace Juzaweb\CMS\Repositories\Abstracts;

abstract class Criteria
{
    public static function make(array $queries): static
    {
        return new static($queries);
    }
}
