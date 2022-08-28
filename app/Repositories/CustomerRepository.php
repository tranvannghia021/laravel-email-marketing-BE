<?php

namespace App\Repositories;

use App\Models\Customer;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CustomerRepository extends BaseRepository
{
    protected $customer;
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        parent::__construct($customer);
    }


    /**
     * getAllCus
     *
     * @param  mixed $id
     * @return collection
     */
    public function getAllCus($id)
    {

        return $this->customer->where('id_shops', $id)->get();
    }




    /**
     * searchCustomAll
     *
     * @param  mixed $datas
     * @param  mixed $limit
     * @param  mixed $id
     * @param  mixed $keySearch
     * @return collection
     */
    public function searchCustomAll($datas,  $limit, $id, $keySearch)
    {
        $query = $this->customer->query();
        $query = $query->shop($id);
        if (!is_null($keySearch)) {
            $query->whereLike([
                DB::raw("CONCAT(`first_name`, ' ', `last_name`)"),
                'first_name',
                'last_name',
                'phone',
                'email',
                'total_order',
                'total_spent',

            ], $keySearch);
        }

        if (!is_null($datas['startDay'])) {

            $query->createDayStart($datas['startDay']);
        }


        if (!is_null($datas['endDay'])) {
            $query->createDayEnd($datas['endDay']);
        }

        if (!is_null($datas['totalSpentStart'])) {
            $query->totalSpentFrom($datas['totalSpentStart']);
        }

        if (!is_null($datas['totalSpentEnd'])) {
            $query->totalSpentTo($datas['totalSpentEnd']);
        }

        if (!is_null($datas['totalOrderStart'])) {
            $query->totalOrderFrom($datas['totalOrderStart']);
        }

        if (!is_null($datas['totalOrderEnd'])) {
            $query->totalOrderTo($datas['totalOrderEnd']);
        }

        if (!is_null($datas['sort']) && $datas['sort'] == 'asc') {
            $query->sort($datas['sort']);
        }

        if (!is_null($datas['sort']) && $datas['sort'] == 'desc') {
            $query->sort($datas['sort']);
        }

        return $query->simplePaginate($limit);
    }


    /**
     * findByid
     *
     * @param  mixed $id
     * @return collection
     */
    public function findByid($id)
    {

        return $this->customer->where('id_cus_shopify', $id)->first();
    }



    /**
     * updateOrInsert
     *
     * @param  mixed $id
     * @param  mixed $datas
     * @return collection|null
     */
    public function updateOrInsert($id, $datas)
    {
        try {
            $model = $this->findByid($id);
            if (is_null($model)) {
                $model = $this->customer->create($datas);
            } else {

                $model = $this->updateCus($id, $datas);
            }
        } catch (\Exception $e) {

            return null;
        }

        return $model;
    }


    /**
     * updateCus
     *
     * @param  mixed $id
     * @param  mixed $datas
     * @return collection|null
     */
    public function updateCus($id, array $datas)
    {
        try {

            $customer = $this->customer->where('id_cus_shopify', $id)->update($datas);
        } catch (\Exception $e) {
            return null;
        }
        return $customer;
    }


    /**
     * delete
     *
     * @param  mixed $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $result = $this->customer->where('id_cus_shopify', $id)->delete();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }


    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->customer->where('id_shops', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
        return true;
    }
}
