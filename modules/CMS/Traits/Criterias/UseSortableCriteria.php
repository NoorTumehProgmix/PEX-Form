<?php


namespace Juzaweb\CMS\Traits\Criterias;

trait UseSortableCriteria
{
    public function getFieldSortable(): array
    {
        return $this->sortableFields ?? [];
    }

    public function getSortableDefaults(): array
    {
        return $this->sortableDefaults ?? [];
    }
}
