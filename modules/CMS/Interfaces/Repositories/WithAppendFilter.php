<?php


namespace Juzaweb\CMS\Interfaces\Repositories;

interface WithAppendFilter
{
    public function appendCustomFilter($builder, $input);
}
