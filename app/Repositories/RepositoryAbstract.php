<?php

namespace App\Repositories;

/**
 * Abstract Respository
 *
 * @package App\Repositories
 * @author Enablestartup <hello@enablestartup.com>
 */
abstract class RepositoryAbstract implements RepositoryInterface
{

    // initial variable model
    protected $model;

    /**
     * Function construct
     *
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * get model
     * @return string
     */
    abstract public function getModel();

    /**
     * Set model
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    /**
     * Function all (Retrieve all data)
     *
     * @param  array  $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    /**
     * Function paginate (Retrieve all data follow paginate)
     *
     * @param  number  $limit
     * @param  array  $columns
     * @return mixed
     */
    public function paginate($limit = 20, $columns = ['*'])
    {
        return $this->model->paginate($limit, $columns);
    }

    /**
     * Function findById
     *
     * @param number $id
     * @param string[] $columns
     * @return mixed
     */
    public function findById($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find data by field and value
     *
     * @param  $field
     * @param  $value
     * @return mixed
     */
    public function findByField($field, $value)
    {
        return $this->model->where($field, $value)->get();
    }

    /**
     * Find data by field and value
     *
     * @param $array
     * @param bool $first
     * @return mixed
     */
    public function findByAttributes($array, $first = true)
    {
        $result = $this->model->where($array);
        if ($first) {
            return $result->first();
        }
        return $result->get();
    }

    /**
     * Function create (add a new data)
     *
     * @param  array  $attributes  (use request->only)
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Function update (update a current data)
     *
     * @param  array  $attributes  (use request->only)
     * @param  number  $id
     * @return boolean
     */
    public function update(array $attributes, $id)
    {
        $result = $this->model->find($id);
        if ($result) {
            $result->fill($attributes);
            if ($result->save()) {
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param  number  $id
     * @return boolean
     */
    public function delete($id)
    {
        $result = $this->model->find($id);
        if ($result) {
            $result->delete();
            return true;
        }

        return false;
    }

    /**
     * Delete a entity in repository by multiple id
     *
     * @param  array  $ids
     * @return boolean
     */
    public function deleteMultiple($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Order collection by a given column
     *
     * @param  string  $field
     * @param  string  $direction
     * @return $this
     */
    public function orderBy($field, $direction = 'asc')
    {
        return $this->model->orderBy($field, $direction);
    }

    /**
     * Load relations
     *
     * @param  string  $tableName
     * @return $this
     */
    public function with($tableName)
    {
        return $this->model->with($tableName);
    }

    /**
     * return datatable
     *
     * @param array $params
     * @param $sql
     * @param $sort
     * @param $pk
     * @param null $orderCallback
     * @param null $getPagination
     * @return  array
     */
    public function returnDataTable(
        array $params,
        $sql,
        $sort = '',
        $pk = false,
        $orderCallback = null,
        $getPagination = null
    ) {
        $start = (int)$params['start'];
        $length = (int)$params['length'];
        $page = (int)$params['page'];
        if (isset($params['order'])) {
            if ($orderCallback) {
                $sql = $orderCallback($sql, $params);
            } else {
                $order = $params['order'][0];
                $columns = $params['columns'];
                $column = $columns[$order['column']]['name'];
                $dir = $order['dir'];
                $sql->orderBy($column, $dir);
            }
        } else {
            if (!empty($sort)) {
                if ($pk) {
                    $sql->orderBy($sort, 'DESC');
                } else {
                    $sql->orderByRaw("CAST($sort as UNSIGNED) DESC");
                }
            }
        }
        if ($length < 0) {
            $data = $sql->get();
            return [
                'recordsTotal' => $data->count(),
                'data' => $data,
                'start' => $start,
                'recordsFiltered' => $data->count(),
            ];
        } else {
            $data = $sql->paginate($length, ['*'], 'page', $page);

            $response = [
                'recordsTotal' => $data->total(),
                'data' => $data->items(),
                'start' => $start,
                'recordsFiltered' => $data->total(),
            ];
            if ($getPagination) {
                $response['dataPagination'] = $data;
            }
            return $response;
        }
    }

    /** Update or Create an entity in repository
     *
     * @param  array  $attributes
     * @param  array  $values
     *
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }
}
