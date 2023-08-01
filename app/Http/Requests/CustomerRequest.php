<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'email' => "required|email|max:45|unique:customers,email",
                    'name' => 'nullable|string|max:100',
                    'phone_number' => ['nullable', new PhoneNumber],
                    'company_name' => 'required|max:255|string',
                ];
            case 'PUT':
                $customer = $this->customer;
                if ($this->route()->named(['customers.setup-information-put', 'api.customers.setup-information-put'])) {
                    $customer = auth()->user()->userable;
                }
                return [
                    'email' => "required|email|max:45|unique:customers,email," . $customer->id,
                    'name' => 'nullable|string|max:100',
                    'phone_number' => ['nullable', new PhoneNumber],
                    'company_name' => 'required|string|max:255',
                    'add1' => 'required|string|max:255',
                    'city' => 'nullable|exists:cities,id',
                    'state' => 'nullable|string|max:20',
                    'zipcode' => 'nullable|regex: /^([0-9]{3}-[0-9]{4})$/',
                    'billing_contact_email' => 'nullable|email|max:45',
                ];
            default:
                break;
        }
        return [];
    }

    public function attributes()
    {
        return [
            'email' => strtolower(__('message.user.email')),
            'name' => strtolower(__('message.user.user_name')),
            'phone_number' => strtolower(__('message.user.phone')),
            'company_name' => strtolower(__('message.user.company_name')),
            'add1' => strtolower(__('message.user.address')),
            'city' => strtolower(__('message.user.city')),
            'state' => strtolower(__('message.user.sate_province')),
            'zipcode' => strtolower(__('message.user.zipcode')),
            'billing_contact_email' => strtolower(__('message.user.billing_contact_email')),
        ];
    }
}
