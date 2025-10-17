<?php

namespace App\Service\Core;

use Exception;

/**
 * Interface BaseServiceInterface
 *
 * @package App\Services
 */
interface IServiceBase
{
    /**
     * Retrieve all data
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * pluck
     *
     * @param  string $column
     * @param  string $key
     * @return void
     */
    public function pluck($column, $key = null);

    /**
     * Retrieve all data, paginated
     *
     * @param int $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*']);

    /**
     * Retrieve find 1
     * 
     * @param mixed $id
     *
     * @return mixed
     */

    public function find($id);

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function show($id, $columns = ['*']);

    /**
     * Save a new entity
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Update a entity by id
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id);

    /**
     * Delete a entity by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id);

    /**
     * setConnection
     *
     * @param  string $connection
     * @return void
     */
    public function setConnection($connection);

    /**
     * whereCondition
     *
     * @param  string $column
     * @param  string $operator
     * @param  string $value
     * @return mixed
     */
    public function whereCondition($column, $operator = null, $value = null);

    /**
     * casting value
     * @param array [key => type]
     */
    public function setCasting(array $casts);


    public function updateOrCreate(array $attributes, array $values = []);

    public function getConnection();
}
