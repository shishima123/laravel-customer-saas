<?php

namespace Modules\Api\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'company_id' => $this->company_id,
            'trial_ends_at' => $this->trial_ends_at,
            'next_cycle_date' => $this->next_cycle_date,
            'stripe_id' => $this->stripe_id,
            'user_number' => $this->user_number,
            'billing_contact_email' => $this->billing_contact_email,
            'is_premium' => $this->is_premium,
            'on_grace_period' => $this->on_grace_period,
            'next_cycle_date_format' => $this->next_cycle_date_format
        ];
    }
}
