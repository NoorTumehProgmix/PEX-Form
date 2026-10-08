<?php

namespace Juzaweb\Backend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    use HasFactory;
    protected $fillable = ['queue', 'job_id', 'job_type','path','size','status', 'execution_time', 'started_at'];
}
