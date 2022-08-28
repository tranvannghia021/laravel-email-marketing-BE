<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $table='campaigns';
    protected $fillable=[
        'id_shop',
        'name',
        'thumb',
        'subject',
        'email_content',
        'created_at',
        'updated_at'
    ];
}
