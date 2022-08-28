<?php

namespace App\Jobs;

use App\Events\MessageSendMailCampaignEvent;
use App\Models\JobBatch;
use App\Repositories\CustomerRepository;
use App\Repositories\JobBatchRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailCampaignJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shops;
    protected $campaigns;
    protected $idCustomer;
    protected $variants = [
        '$href_image',
        '$Shop_name',
        '$Customer_Full_name',
        '$Customer_First_name',
        '$Customer_Last_name'
    ];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shops, $campaigns, $idCustomer)
    {
        $this->shops = $shops;
        $this->campaigns = $campaigns;
        $this->idCustomer = $idCustomer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerRepository $cusRepo)
    {
        $bodyMail = $this->campaigns->email_content;
        $subject = $this->campaigns->subject;
        $customer = $cusRepo->findByid($this->idCustomer);
        $arrayName = [
            env('APP_URL') . '/storage/uploads/' . $this->campaigns->thumb,
            $this->shops->name,
            $customer->first_name . ' ' . $customer->last_name,
            $customer->first_name,
            $customer->last_name

        ];

        foreach ($this->variants as $key => $variant) {
            if (strpos($bodyMail, $variant) != false || strpos($bodyMail, $variant) == 0) {
                $bodyMail = str_replace($variant, $arrayName[$key], $bodyMail);
            }

            if (strpos($subject, $variant) != false || strpos($subject, $variant) == 0) {
                $subject = str_replace($variant, $arrayName[$key], $subject);
            }
        }


        try {
            Mail::send('mails.campaign', compact('bodyMail'), function ($message) use ($customer, $subject) {
                $message->from(env('MAIL_FROM_ADDRESS', $this->shops->email))->to($customer->email)
                    ->subject($subject);
            });



            if (count(Mail::failures()) > 0) {
                throw new Exception();
            }
            $this->eventPusher();
        } catch (\Exception $e) {
            $this->eventPusher();
            throw new Exception();
        }
    }


    protected function eventPusher()
    {
        $jobBatchRepo = new JobBatchRepository(new JobBatch());
        $jobBatch = $jobBatchRepo->getJobBatch($this->campaigns->id);
        event(new MessageSendMailCampaignEvent(
            $this->shops->id,
            $this->campaigns->id,
            $jobBatch->total_jobs,
            $jobBatch->total_jobs - $jobBatch->pending_jobs,
            $jobBatch->failed_jobs
        ));
    }
}
