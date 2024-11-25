<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Enums\Role;

/**
 * Class CustomerRepository
 *
 * @package App\Repositories
 */
class CustomerRepository extends RepositoryAbstract
{
    /**
     * Function getModel
     *
     * @return  string
     */
    public function getModel(): string
    {
        return Customer::class;
    }

    public function list($params): array
    {
        $sql = $this->model->select('customers.*')
            ->with(['user', 'company', 'subscriptionRelationLast'])
            ->join('users', function ($q) {
                $q->on('customers.id', 'users.userable_id')
                    ->where('users.userable_type', 'App\Models\Customer')
                    ->where('users.role', Role::USER);
            });

        $search = $params['search']['value'] ?? '';
        if ($search) {
            $sql->leftJoin('companies', function ($q) {
                $q->on('companies.id', 'customers.company_id');
            })
                ->where(function ($q) use ($search) {
                    $q->orWhere('customers.email', 'like', '%' . $search . '%')
                        ->orWhere('customers.name', 'like', '%' . $search . '%')
                        ->orWhere('customers.phone_number', 'like', '%' . $search . '%')
                        ->orWhere('customers.user_number', 'like', '%' . $search . '%')
                        ->orWhere('companies.name', 'like', '%' . $search . '%');
                });
        }

        return $this->returnDataTable($params, $sql, 'users.id');
    }
}
