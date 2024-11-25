<?php

namespace App\Repositories;

use App\Models\Address;

/**
 * Class AddressRepository
 *
 * @package App\Repositories
 */
class AddressRepository extends RepositoryAbstract
{
    /**
     * Function getModel
     *
     * @return  string
     */
    public function getModel(): string
    {
        return Address::class;
    }
}
