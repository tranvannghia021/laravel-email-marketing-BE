<?php

namespace App\Jobs;

use App\Repositories\CampaignRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\JobBatchRepository;
use App\Repositories\ShopRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnInstallWebHookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shops;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shops)
    {

        $this->shops = $shops;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ShopRepository $shopRepo,
        CustomerRepository $cusRepo,
        CampaignRepository $camRepo,
        JobBatchRepository $jobBatRepo
    ) {
        $status = $this->shops->status;
        $idShop = $this->shops->id;
        if ($status == 'install') {
            //update shop 
            $shopRepo->update($idShop, [
                'status' => 'uninstall'
            ]);
            //destroy all customer with id shop
            $cusRepo->destroy($idShop);

            //destroy all campaign with id shop
            $camRepo->destroy($idShop);
            // destroy jobbats
            $jobBatRepo->destroy($idShop);
        }
    }
}
