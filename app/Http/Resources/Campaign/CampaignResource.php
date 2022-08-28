<?php

namespace App\Http\Resources\Campaign;

use App\Models\Customer;
use App\Models\JobBatch;
use App\Repositories\CustomerRepository;
use App\Repositories\JobBatchRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $jobBatch=(new JobBatchRepository(new JobBatch()))->getJobBatch($this->id);
       
        return[
            'id'=>$this->id,
            'name'=>$this->name,
            'created_at'=>$this->created_at,
            'total'=>$jobBatch->total_jobs,
            'sended'=>$jobBatch->total_jobs - $jobBatch->pending_jobs ,
            'failed'=>$jobBatch->failed_jobs,

        ];
    }
}
