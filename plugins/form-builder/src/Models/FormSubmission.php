<?php

namespace Progmix\FormBuilder\Models;

use Progmix\FormBuilder\Models\Form;;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class FormSubmission extends Model
{
    use ResourceModel;
    protected $table = 'form_submissions';

    protected $fillable = ['form_data', 'form_id','meta_data'];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
