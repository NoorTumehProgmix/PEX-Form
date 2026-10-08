<?php

namespace Progmix\SearchLog\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class SearchLog extends Model
{
    use ResourceModel;

    protected $table = 'search_logs';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'text',
        'lang',
        'ip_address',
        'data'
    ];
}
