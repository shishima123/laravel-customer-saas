<?php

namespace App\Repositories;

use App\Models\WebhookUrl;

/**
 * Class WebhookUrlRepository
 *
 * @package App\Repositories
 */
class WebhookUrlRepository extends RepositoryAbstract
{
    /**
     * Function getModel
     *
     * @return  string
     */
    public function getModel()
    {
        return WebhookUrl::class;
    }
}
