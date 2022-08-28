<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $cusRepo;
    protected $limitPage = 15;

    public function __construct(CustomerRepository $cusRepo)
    {
        $this->cusRepo = $cusRepo;
    }


    /**
     * getCustomer
     *
     * @param  mixed $request
     * @return response
     */
    public function getCustomer(Request $request)
    {
        $shops = auth()->user();
        $limit = is_null($request->limit) ? $this->limitPage : $request->limit;

        $arayKeySearch = [
            'startDay' => $request->date_from,
            'endDay' => $request->date_to,
            'totalSpentStart' => is_null($request->spent_from) ? null : abs($request->spent_from),
            'totalSpentEnd' => is_null($request->spent_to) ? null : abs($request->spent_to),
            'totalOrderStart' => is_null($request->order_from) ? null :   intval(abs($request->order_from)),
            'totalOrderEnd' => is_null($request->order_to) ? null : intval(abs($request->order_to)),
            'sort' =>  $request->sort

        ];

        $keySearch = is_null($request->q) ? null : $request->q;


        $customers = $this->cusRepo->searchCustomAll($arayKeySearch, $limit, $shops->id, $keySearch);

        return  $this->responseCustomer($customers);
    }


    /**
     * responseCustomer
     *
     * @param  mixed $customers
     * @return response
     */
    public function responseCustomer($customers)
    {
        $datas = $customers->toArray();

        return response()->json(
            [
                'success' => true,
                'message' => 'list customers',
                'data' => CustomerResource::collection($customers),
                'current_page' => $datas['current_page'],
                'prev_page_url' => is_null($datas['prev_page_url']) ? null : $datas['prev_page_url'],
                'next_page_url' => is_null($datas['next_page_url']) ? null : $datas['next_page_url'],

            ]
        );
    }
}
