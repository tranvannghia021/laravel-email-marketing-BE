<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id_cus_shopify,
            'id_shops'=>$this->id_shops,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'country'=>$this->country,
            'phone'=>$this->phone,
            'email'=>$this->email,
            'total_order'=>$this->total_order,
            'total_spent'=>$this->total_spent,
            'customer_created_at'=>$this->cus_created_at
        ];
    }
}
