<?php

namespace Juzaweb\Backend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    protected $table = 'templates';
    protected $fillable = [
        'name',
        'key',
        'post_type',
    ];
}
