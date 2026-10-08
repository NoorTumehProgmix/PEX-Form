<?php

namespace Progmix\ForumRegistration\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class ForumRegistration extends Model
{
    use ResourceModel;

    protected $table = 'forum_registrations';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'institution',
        'job_title',
        'email',
        'phone',
        'part_type',
        'sponsor_type',
    ];
}
