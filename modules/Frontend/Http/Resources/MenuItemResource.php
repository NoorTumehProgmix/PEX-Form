<?php

/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/juzacms
 * @author     The Anh Dang
 * @link       https://juzaweb.com/cms
 * @license    GNU V2
 */

namespace Juzaweb\Frontend\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'label' => $this->label,
            'link' => $this->box_key == "general_url" ? get_general_link_by_id($this->link)['link'] : $this->link,
            'type' => $this->type,
            'icon' => $this->icon,
            'box_key' => $this->box_key,
            'target' => $this->target,
            'num_order' => $this->num_order,
            'is_active' => $this->isActive(),
        ];
    }
}
