<?php


namespace Juzaweb\CMS\Facades;

use Illuminate\Support\Facades\Facade;
use Juzaweb\CMS\Contracts\XssCleanerContract;

/**
 * @method static string clean(string $value)
 *
 * @see \Juzaweb\CMS\Support\XssCleaner
 */
class XssCleaner extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return XssCleanerContract::class;
    }
}
