<?php

namespace Progmix\Api\Models;

use Juzaweb\CMS\Models\Model;

class Api extends Model
{
    protected $table = 'awap_apis';
    protected $fillable = [
        'name',
        'edge_name',
        'slug',
        'version',
        'method',
        'headers',
        'origin_url',
        'edge_url',
        'params',
        'query',
        'body',
        'api_architecture',
        'message',
        'description',
        'status',
        'maintenance',
        'timeout',
        'timeout_message',
        'response_pairs',
    ];

    public function apiLog()
    {
        return $this->hasMany(ApiLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
