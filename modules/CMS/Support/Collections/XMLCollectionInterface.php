<?php


namespace Juzaweb\CMS\Support\Collections;

use Illuminate\Support\Collection;

interface XMLCollectionInterface
{
    public function getCollection($filePath): Collection;
}
