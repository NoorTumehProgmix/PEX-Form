<?php

namespace Progmix\Restrictions\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Restriction extends Model
{
    use ResourceModel;

    protected $table = 'restrictions';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'ip',
        'notes',
    ];

}
