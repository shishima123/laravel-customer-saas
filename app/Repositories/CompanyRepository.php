<?php

namespace App\Repositories;

use App\Models\Company;

/**
 * Class CompanyRepository
 *
 * @package App\Repositories
 */
class CompanyRepository extends RepositoryAbstract
{
    /**
     * Function getModel
     *
     * @return  string
     */
    public function getModel()
    {
        return Company::class;
    }
}
