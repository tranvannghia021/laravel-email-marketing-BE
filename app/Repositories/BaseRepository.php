<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BaseRepository implements RepositoryInterface
{
    protected $model;
    public function __construct(Model $model)
    {
        $this->model = $model;
    }



    /**
     * getAll
     *
     * @return collection
     */
    public function getAll()
    {
        return $this->model->all();
    }


    /**
     * find
     *
     * @param  mixed $id
     * @return collection
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }


    /**
     * create
     *
     * @param  mixed $datas
     * @return collection|null
     */
    public function create(array $datas)
    {
        try {
            $model = $this->model->create($datas);
        } catch (\Exception $e) {
            return null;
        }
        return $model;
    }



    /**
     * update
     *
     * @param  mixed $id
     * @param  mixed $datas
     * @return collection|null
     */
    public function update($id, array $datas)
    {
        try {
            $model = $this->find($id);
            if ($model) {
                $model = $model->update($datas);
            }
        } catch (\Exception $e) {
            return null;
        }

        return $model;
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
            $model = $this->find($id);
            if ($model) {
                return $model->delete();
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
