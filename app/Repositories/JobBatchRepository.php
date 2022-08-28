<?php

namespace App\Repositories;

use App\Models\JobBatch;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class JobBatchRepository extends BaseRepository
{
    protected $jobBatch;
    public function __construct(JobBatch $jobBatch)
    {
        $this->jobBatch = $jobBatch;
        parent::__construct($jobBatch);
    }

    /**
     * getJobBatch
     *
     * @param  mixed $id
     * @return collection
     */
    public function getJobBatch($id)
    {

        return $this->jobBatch->where('name', $id)->first();
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
            $this->jobBatch->where('name', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
        return true;
    }
}
