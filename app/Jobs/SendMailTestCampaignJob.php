<?php

namespace App\Jobs;

use App\Events\SendMessageCsvEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class SendMailTestCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $nameFile;
    protected $shops;
    protected $listEmail;
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
    public function __construct($nameFile, $shops, $listEmail)
    {
        $this->nameFile = $nameFile;
        $this->shops = $shops;
        $this->listEmail = $listEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            $bodyMail = Redis::get('email_body');
            $subject = Redis::get('subject');
            $arrayName = [
                env('APP_URL') . '/storage/uploads/' . $this->nameFile,
                $this->shops->name,
                'Customer_Full_name',
                'Customer_First_name',
                'Customer_Last_name'
            ];
            foreach ($this->variants as $key => $variant) {
                if (strpos($bodyMail, $variant) != false || strpos($bodyMail, $variant) == 0) {
                    $bodyMail = str_replace($variant, $arrayName[$key], $bodyMail);
                }

                if (strpos($subject, $variant) != false || strpos($subject, $variant) == 0) {
                    $subject = str_replace($variant, $arrayName[$key], $subject);
                }
            }
            $listEmail = explode(',', trim($this->listEmail, ''));

            foreach ($listEmail as $email) {

                Mail::send('mails.campaign', compact('bodyMail'), function ($message) use ($email, $subject) {
                    $message->from(env('MAIL_FROM_ADDRESS', $this->shops->email))->to($email)
                        ->subject($subject);
                });
            }
            event(new SendMessageCsvEvent(
                $this->shops->id,
                true,
                'Sended mail test successfully'
            ));
            dispatch(new DelKeyRedisJob('subject'));
            dispatch(new DelKeyRedisJob('email_body'));
        } catch (\Exception $e) {
            event(new SendMessageCsvEvent(
                $this->shops->id,
                false,
                'Sended mail test failed'
            ));
        }
    }
}
