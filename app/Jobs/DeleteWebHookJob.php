<?php

namespace App\Jobs;

use App\Repositories\CustomerRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteWebHookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $datas;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas)
    {
        $this->datas=$datas;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerRepository $cusRepo)
    {
        $cusRepo->delete($this->datas['id']);
    }
}
