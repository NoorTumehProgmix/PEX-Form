<?php

namespace Progmix\Api\Models;

use Juzaweb\CMS\Models\Model;

class ApiLog extends Model
{
    protected $table = 'awap_api_logs';
    protected $fillable = [
        'api_id',
        'request',
        'response',
        'type',
        'ip',
        'status_code',
        'duration',
        'start',
        'attempt_id',
        'end',
    ];

    public function api(){
        return $this->belongsTo(Api::class);
    }


}
