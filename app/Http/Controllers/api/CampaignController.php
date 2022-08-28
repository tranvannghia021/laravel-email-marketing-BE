<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSendMailCampaignEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignRequest;
use App\Http\Requests\Campaign\CampaignTestRequest;
use App\Http\Resources\Campaign\CampaignResource;
use App\Jobs\SendMailCampaignJob;
use App\Jobs\SendMailTestCampaignJob;
use App\Repositories\CampaignRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\JobBatchRepository;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Throwable;

class CampaignController extends Controller
{
    protected $campaRepo;
    protected $cusRepo;
    protected $jobBatch;

    public function __construct(
        CampaignRepository $campaRepo,
        CustomerRepository $cusRepo,
        JobBatchRepository $jobBatch
    ) {
        $this->campaRepo = $campaRepo;
        $this->cusRepo = $cusRepo;
        $this->jobBatch = $jobBatch;
    }


    /**
     * getAllCampaign
     *
     * @param  mixed $request
     * @return response
     */
    public function getAllCampaign(Request $request)
    {
        $shops = auth()->user();
        $campaigns = $this->campaRepo->getAllCampaign($shops->id);


        return response()->json([
            'success' => true,
            'message' => 'List campaign',
            'data' => CampaignResource::collection($campaigns)
        ]);
    }


    /**
     * store
     *
     * @param  mixed $request
     * @return response
     */
    public function store(CampaignRequest $request)
    {
        $shops = auth()->user();
        $listCutomers = json_decode($request->list_customers);
        if (count($listCutomers) <= 0) {

            return response()->json([
                'success' => false,
                'message' => 'Customer list must be non-empty'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $isCheckFile = $this->isCheckFile($request);
        if ($isCheckFile) {
            $nameFile = $request->file('image')->getClientOriginalName();
            $campaigns = $this->campaRepo->create([
                'id_shop' => $shops->id,
                'name' => $request->campaign_name,
                'thumb' => $nameFile,
                'subject' => $request->subject,
                'email_content' => $request->email_body,
            ]);
            if ($campaigns) {
                $request->file('image')->storeAs('public/uploads', $nameFile);
                $arrayJob = [];

                foreach ($listCutomers as $idCus) {
                    $arrayJob[] =  new SendMailCampaignJob($shops, $campaigns, $idCus);
                }
                Bus::batch($arrayJob)->then(function (Batch $batch) {
                })->finally(function (Batch $batch) use ($campaigns, $shops) {
                    $jobBatch = $this->jobBatch->getJobBatch($campaigns->id);
                    if ($jobBatch) {
                        event(new MessageSendMailCampaignEvent(
                            $shops->id,
                            $campaigns->id,
                            $jobBatch->total_jobs,
                            $jobBatch->total_jobs - $jobBatch->pending_jobs,
                            $jobBatch->failed_jobs
                        ));
                    }
                })->allowFailures()->name($campaigns->id)->onConnection('redis')->dispatch();


                return response()->json([
                    'success' => true,
                    'message' => 'Save campaign successfully',
                ]);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Save campaign failed',
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }


    /**
     * sendTestMail
     *
     * @param  mixed $request
     * @return response
     */
    public function sendTestMail(CampaignTestRequest $request)
    {

        $shops = auth()->user();

        $isCheckFile = $this->isCheckFile($request);
        if ($isCheckFile) {
            $nameFile = $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/uploads', $nameFile);
            Redis::set('subject', $request->subject);
            Redis::set('email_body', $request->email_body);
            dispatch(new SendMailTestCampaignJob($nameFile, $shops, $request->list_email));

            return response()->json([
                'success' => true,
                'message' => 'Test campaign running',
            ]);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Test campaign failed,wrong image format',
            ], Response::HTTP_BAD_REQUEST);
        }
    }




    /**
     * isCheckFile
     *
     * @param  mixed $request
     * @return bool
     */
    public function isCheckFile($request)
    {

        $fileExtension = ['png', 'jpg', 'jpeg', 'gif'];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $fileItem) {
                $ext = $fileItem->getClientOriginalExtension();
                if (!in_array($ext, $fileExtension)) {
                    return false;
                }
                $size = $fileItem->getSize();
                if ($size > 5000000) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
