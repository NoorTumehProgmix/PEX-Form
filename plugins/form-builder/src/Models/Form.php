<?php

namespace Progmix\FormBuilder\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Form extends Model
{
    use ResourceModel;
    // protected $connection = 'secondary';

    public const TYPES = [
        'FORM_BUILDER' => 'formBuilder',
        'FORM_STATIC' => 'static',
    ];

    protected $fillable = ['name', 'is_database_submittable', 'form_definition', 'validations', 'type', 'side_code', 'destinations', 'submittable'];
}
