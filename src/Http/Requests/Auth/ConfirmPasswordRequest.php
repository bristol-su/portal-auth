<?php

namespace BristolSU\Auth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmPasswordRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|password'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'password.required' => 'Please enter your password.',
            'password.password' => 'Your password did not match our records.'
        ];
    }

}
