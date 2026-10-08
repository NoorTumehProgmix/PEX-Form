<?php


namespace Juzaweb\CMS\Facades;

use Juzaweb\CMS\Contracts\MacroableModelContract;
use Illuminate\Support\Facades\Facade;

class MacroableModel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MacroableModelContract::class;
    }
}
