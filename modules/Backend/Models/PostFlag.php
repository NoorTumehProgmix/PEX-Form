<?php

namespace Juzaweb\Backend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostFlag extends Model
{
    use HasFactory;
    protected $table = 'post_flags';
    protected $fillable = [
        'name',
        'icon',
    ];
}
