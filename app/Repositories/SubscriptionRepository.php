<?php

namespace App\Repositories;

use App\Models\Subscription;

/**
 * Class SubscriptionRepository
 *
 * @package App\Repositories
 */
class SubscriptionRepository extends RepositoryAbstract
{
    /**
     * Function getModel
     *
     * @return  string
     */
    public function getModel()
    {
        return Subscription::class;
    }
}
