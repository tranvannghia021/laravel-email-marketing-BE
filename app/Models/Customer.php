<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $fillable = [
        'id_cus_shopify',
        'id_shops',
        'first_name',
        'last_name',
        'country',
        'phone',
        'email',
        'total_order',
        'total_spent',
        'cus_created_at',
        'created_at',
        'updated_at'
    ];

    public function scopeCreateDayStart($query, $startDay)
    {

        return $query->where('cus_created_at', '>=', $this->convertDayTime($startDay));
    }


    public function scopeCreateDayEnd($query, $endDay)
    {

        return $query->where('cus_created_at', '<=', $this->convertDayTime($endDay) . ' 23:59:59');
    }


    public function scopeTotalSpentFrom($query, $from)
    {

        return $query->where('total_spent', '>=', (int)$from);
    }


    public function scopeTotalSpentTo($query, $to)
    {

        return $query->where('total_spent', '<=', (int)$to);
    }


    public function scopeTotalOrderFrom($query, $from)
    {

        return $query->where('total_order', '>=', intval($from));
    }


    public function scopeTotalOrderTo($query, $to)
    {

        return $query->where('total_order', '<=', intval($to));
    }


    public function scopeSort($query, $sort)
    {

        return $query->orderBy('cus_created_at', trim($sort))->orderBy('id_cus_shopify', trim($sort));
    }


    public function scopeShop($query, $idShop)
    {

        return $query->where('id_shops', $idShop);
    }


    public function convertDayTime($day)
    {

        return date('Y-m-d', strtotime((string)$day));
    }
}
