<?php

namespace App\Jobs;

use App\Events\SaveCustomerEvent;
use App\Repositories\CustomerRepository;
use App\Services\CustomerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GetAllCusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shop;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerService $cusService, CustomerRepository $cusRepo)
    {
        $number = 0;
        $totalCustomer = $cusService->countCustomer($this->shop)->json('count');
        if ($totalCustomer == 0) {
            event(new  SaveCustomerEvent(
                $this->shop->id,
                $totalCustomer,
                $number
            ));
            return;
        }
        $response = $cusService->getAllCustomer($this->shop);
        while (true) {
            $customers = $response->json('customers');
            foreach ($customers as $customer) {

                $result = $cusRepo->updateOrInsert($customer['id'], [
                    'id_cus_shopify' => $customer['id'],
                    'id_shops' => $this->shop->id,
                    'first_name' => $customer['first_name'],
                    'last_name' => $customer['last_name'],
                    'country' => empty($customer['default_address']['country']) ? null : $customer['default_address']['country'],
                    'phone' => $customer['phone'],
                    'email' => $customer['email'],
                    'total_order' => $customer['orders_count'],
                    'total_spent' => $customer['total_spent'],
                    'cus_created_at' => date("Y-m-d H:i:s", strtotime($customer['created_at'] . ' UTC')),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
                if (is_null($result)) {

                    $totalCustomer--;
                } else {

                    $number++;
                }

                event(new  SaveCustomerEvent(
                    $this->shop->id,
                    $totalCustomer,
                    $number
                ));
            }
            if (strpos($response->header('link'), 'rel="next"') != false) {
                $arrayLink = explode(',', $response->header('link'));
                $response = $cusService->getAllCustomer($this->shop, trim($arrayLink[count($arrayLink) - 1], '<>; rel="next"'));
            } else {
                break;
            }
        }
    }
}
