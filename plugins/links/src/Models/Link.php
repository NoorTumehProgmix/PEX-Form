<?php

namespace Progmix\Links\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Link extends Model
{
    use ResourceModel;

    protected $table = 'links';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'name',
        'slug',
        'link',
    ];
}
