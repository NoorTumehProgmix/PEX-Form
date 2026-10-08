<?php

namespace Progmix\FormBuilder\Models;

use Juzaweb\Backend\Models\Language;
use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class Translation extends Model
{
    use ResourceModel;

    protected $fillable =['value','language_id','label_key'];


    public function language(){
        return $this->belongsTo(Language::class);
    }
}
