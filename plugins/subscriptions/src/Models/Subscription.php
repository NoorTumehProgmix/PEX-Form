<?php

namespace Juzaweb\Subscriptions\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Subscription extends Model
{
    use ResourceModel,SoftDeletes;

    protected $table = 'subscriptions';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'email',
        'lang'
    ];

}
