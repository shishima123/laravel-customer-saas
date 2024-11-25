<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirstChangePasswordRequest extends FormRequest
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
        return [
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
        ];
    }

    public function attributes()
    {
        return [
            'password' => strtolower(__('message.auth.new_password')),
            'password_confirmation' => strtolower(__('message.auth.confirm_password')),
        ];
    }

    public function messages()
    {
        return [
            'password_confirmation.same' =>
                __('validation.confirmed', ['attribute' => strtolower(__('message.auth.password'))])
        ];
    }
}
