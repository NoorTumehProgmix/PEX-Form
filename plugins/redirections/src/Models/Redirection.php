<?php

namespace Progmix\Redirections\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Redirection extends Model
{
    use ResourceModel;

    protected $table = 'redirections';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'name',
        'old_link',
        'new_link',
    ];

}
