<?php

namespace Progmix\Donations\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Donation extends Model
{
    use ResourceModel;

    protected $table = 'donations';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [
        'name',
        'email',
        'phone',
        'payment_method',
        'payment_status',
        'payment_id',
        'payment_amount',
        'payment_currency',
        'recurring',
        'recurring_amount',
        'recurring_interval',
        'note',
    ];
}
