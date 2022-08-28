<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobBatch extends Model
{
    use HasFactory;
    protected $table='job_batches';
    protected $fillable=[
       'name',
       'total_jobs',
       'pending_jobs',
       'failed_jobs',
       'failed_job_ids',
       'option',
       'cancelled_at',
       'created_at',
       'finished_at'
    ];
}
