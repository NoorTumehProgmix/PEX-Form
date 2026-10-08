<?php

namespace Juzaweb\Backend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'status',
        'description',
        'setting_category_id'
    ];
}
