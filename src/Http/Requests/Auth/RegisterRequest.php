<?php


namespace BristolSU\Auth\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'identifier' => 'string',
            'password' => 'required|confirmed|min:6'
        ];
    }

    public function authorize()
    {
        return true;
    }

}
