<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\ExportCsvJob;
use App\Jobs\GetAllCusJob;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    protected $cusRepo;
    public function __construct(CustomerRepository $cusRepo)
    {
        $this->cusRepo = $cusRepo;
    }


    /**
     * exportCSV
     *
     * @param  mixed $request
     * @return response
     */
    public function exportCSV(Request $request)
    {

        $shops = auth()->user();
        dispatch(new ExportCsvJob($shops, $request->customers, $request->timezone));
        return response()->json([
            'success' => true,
            'message' => 'Export customers is running!',

        ]);
    }


    /**
     * manualSync
     *
     * @param  mixed $request
     * @return response
     */
    public function manualSync(Request $request)
    {

        $shops = auth()->user();
        if ($shops) {
            dispatch(new GetAllCusJob($shops));

            return response()->json([
                'success' => true,
                'message' => 'Manual Sync is running!'
            ]);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Manual Sync failded'
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }
    }
}
