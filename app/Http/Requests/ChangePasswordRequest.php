<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => ['sometimes', 'required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    return $fail(__('validation.current_password'));
                }
            }],
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|min:8|same:password'
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => strtolower(__('message.auth.current_password')),
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
