<?php


namespace BristolSU\Auth\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'identifier' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function authorize()
    {
        return true;
    }

}
