<?php


namespace Juzaweb\API\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Juzaweb\Backend\Models\Menu;

/**
 * @property Menu $resource
 */
class MenuResource extends JsonResource
{
    public function toArray($request): array
    {
        $this->resource->load(
            [
                'items.recursiveChildren' => fn ($q) => $q->cacheFor(
                    config('juzaweb.performance.query_cache.lifetime')
                )
            ]
        );

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'location' => $this->resource->getLocation(),
            'items' => MenuItemCollection::make($this->resource->items),
        ];
    }
}
