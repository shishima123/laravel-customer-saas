<?php

namespace App\Repositories;

use App\Models\City;

/**
 * Class CityRepository
 *
 * @package App\Repositories
 */
class CityRepository extends RepositoryAbstract
{
    /**
     * Function getModel
     *
     * @return  string
     */
    public function getModel()
    {
        return City::class;
    }
}
