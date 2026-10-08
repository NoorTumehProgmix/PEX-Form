<?php


namespace Juzaweb\CMS\Support;

use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    public function getNamespace()
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }
    
        return $this->namespace = 'Juzaweb\\Backend\\';
    }
}
