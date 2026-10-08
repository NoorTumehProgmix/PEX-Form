<?php

namespace Juzaweb\API\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuItemCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return $this->collection->map(
            function ($item) {
                $result = [
                    'id' => $item->id,
                    'label' => $item->label,
                    'link' => $item->link,
                    'type' => $item->type,
                    'icon' => $item->icon,
                    'target' => $item->target,
                    'num_order' => $item->num_order,
                ];

                if ($item->recursiveChildren->isNotEmpty()) {
                    $result['children'] = MenuItemCollection::make($item->recursiveChildren);
                }

                return $result;
            }
        )->toArray();
    }
}
