<?php


namespace Juzaweb\CMS\Interfaces\Repositories;

interface WithAppendSearch
{
    public function appendCustomSearch($builder, $keyword, $input);
}
