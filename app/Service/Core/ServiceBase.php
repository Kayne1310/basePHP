<?php

namespace App\Service\Core;

use App\Service\Core\IServiceBase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

abstract class ServiceBase implements IServiceBase
{

    protected $repository;
    /**
     * Retrieve all data
     *
     * @param array $columns
     *
     * @return mixed
     */

    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * pluck
     *
     * @param  string $column
     * @param  string $key
     * @return void
     */
    public function pluck($column, $key = null)
    {
        return $this->repository->pluck($column, $key);
    }


    /**
     * Retrieve find 1
     * 
     * @param mixed $id
     *
     * @return mixed
     */

    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Retrieve all data, paginated
     *
     * @param int $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'])
    {
        return $this->repository->paginate($limit, $columns);
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function show($id, $columns = ['*'])
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * Save a new entity
     *
     * @param array $attributes
     *
     * @return mixed $model
     */
    public function create(array $attributes)
    {
        return $this->repository->create($attributes);
    }

    /**
     * Update a entity by id
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        return $this->repository->update($attributes, $id);
    }

    /**
     * Delete a entity by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * setConnection
     *
     * @param  string $connection
     * @return void
     */
    public function setConnection($connection)
    {
        return $this->repository->setConnection($connection);
    }

    /**
     * whereCondition
     *
     * @param  string $column
     * @param  string $operator
     * @param  string $value
     * @return mixed
     */
    public function whereCondition($columnCondition, $operator = null, $value = null, $column = ['*'])
    {
        $where = [array($columnCondition, $operator, $value)];
        return $this->repository->findWhere($where, $column);
    }

    /**
     * casting value
     * @param array [key => type]
     */
    public function setCasting(array $casts)
    {
        $this->repository->setCasting($casts);
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->repository->updateOrCreate($attributes, $values);
    }

    public function getConnection()
    {
        return $this->repository->getConnection();
    }
}
