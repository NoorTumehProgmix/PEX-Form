<?php

namespace Progmix\ContactUs\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Contact extends Model
{
    use ResourceModel;

    protected $table = 'contact_us';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        "message"
    ];
}
