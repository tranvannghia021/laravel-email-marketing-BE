<?php

namespace App\Jobs;

use App\Repositories\CustomerRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWebHookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $datas;
    protected $shops;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas, $shops)
    {
        $this->datas = $datas;
        $this->shops = $shops;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerRepository $cusRepo)
    {
    
        if (!is_null($cusRepo->findByid($this->datas['id']))) {
            $cusRepo->updateCus($this->datas['id'], [
                'first_name' => $this->datas['first_name'],
                'last_name' => $this->datas['last_name'],
                'country' => empty($this->datas['default_address']['country']) ? null : $this->datas['default_address']['country'],
                'phone' => $this->datas['phone'],
                'email' => $this->datas['email'],
                'total_order' => $this->datas['orders_count'],
                'total_spent' => $this->datas['total_spent'],
            ]);
        }
    }
}
