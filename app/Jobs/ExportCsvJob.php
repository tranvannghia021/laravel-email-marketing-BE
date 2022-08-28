<?php

namespace App\Jobs;

use App\Events\SendMessageCsvEvent;
use App\Mail\HelloEmail;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Mail;

class ExportCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shops;
    protected $datas;
    protected $timeZone;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shops, $datas, $timeZone)
    {
        $this->shops = $shops;
        $this->datas = $datas;
        $this->timeZone = $timeZone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerRepository $cusRepo)
    {
        $subjectCsv = [
            'Id',
            'IDShop',
            'FirstName',
            'LastName',
            'Country',
            'Phone',
            'Email',
            'TotalOrder',
            'TotalSpent',
            'Created_customer',
            'Created_at',
            'Updated_at'
        ];
        try {
            $timeCreateCsv = $this->timeZone;
            $nameFile = 'list-customer-' . $this->shops->id . '-' . $timeCreateCsv . '.csv';
            $fopen = fopen(storage_path('app/customer-csv/' . $nameFile), 'w') or die('Permission error');
            if (is_null($this->datas)) {
                $customers = $cusRepo->getAllCus($this->shops->id);
                if (count($customers->toArray()) <= 0) {
                    event(new SendMessageCsvEvent(
                        $this->shops->id,
                        true,
                        'No customer, Can not send mail. '
                    ));
                    return;
                }
            } else {
                $customers = [];

                foreach ($this->datas as $id) {

                    $customers[] = $cusRepo->findByid($id);
                }
            }
            fputcsv($fopen, (array)$subjectCsv);
            foreach ($customers as $cus) {
                fputcsv($fopen, (array)$cus->toArray());
            }
            $result = fclose($fopen);
            if ($result) {
                $nameShop = $this->shops->name;
                Mail::send('mails.hello', compact('nameShop', 'timeCreateCsv'), function ($message) use ($nameFile) {
                    $message->to($this->shops->email)
                        ->subject('Export file from Email-marketing');
                    $message->attach(storage_path('app/customer-csv/' . $nameFile));
                });
                if (count(Mail::failures()) > 0) {
                    throw new Exception();
                }
                event(new SendMessageCsvEvent(
                    $this->shops->id,
                    true,
                    'Sended mail successfully with file csv'
                ));
            } else {
                throw new Exception();
            }
        } catch (\Exception $e) {
            event(new SendMessageCsvEvent(
                $this->shops->id,
                false,
                'Export CSV failed,Try again.'
            ));
        }
    }
}
