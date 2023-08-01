<?php

namespace App\Repositories;

use App\Models\DbConnection;

/**
 * Class DbConnectionRepository
 *
 * @package App\Repositories
 */
class DbConnectionRepository extends RepositoryAbstract
{
    /**
     * Function getModel
     *
     * @return  string
     */
    public function getModel()
    {
        return DbConnection::class;
    }
}
