<?php

namespace App\Repositories;

use App\Models\Campaign;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;;

class CampaignRepository extends BaseRepository
{
    protected $campaign;
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        parent::__construct($campaign);
    }


    /**
     * getAllCampaign
     *
     * @param  mixed $id
     * @return collection
     */
    public function getAllCampaign($id)
    {

        return $this->campaign->where('id_shop', $id)->orderBy('id', 'DESC')->get();
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

            $this->campaign->where('id_shop', $id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }

        return true;
    }
}
