<?php



namespace Juzaweb\Backend\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxonomyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'taxonomy' => $this->taxonomy,
            'slug' => $this->slug,
            'level' => $this->level,
            'total_post' => $this->total_post,
            'thumbnail' => $this->getThumbnail(),
            'url' => $this->getLink(),
        ];
    }
}
