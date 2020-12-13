<?php

namespace App\Http\Requests\Auth\LogIntoAdmin;

use App\Rules\Authentication\GroupIdIsOwned;
use App\Rules\Authentication\RoleIdIsOwned;
use App\Rules\Authentication\UserIdIsOwned;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{

    public function rules()
    {
        return [
            'login_id' => 'required',
            'login_type' => ['required', Rule::in(['user', 'group', 'role'])]
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->sometimes('login_id', new UserIdIsOwned, function($input) {
            return $input->login_type === 'user';
        });
        $validator->sometimes('login_id', new GroupIdIsOwned, function($input) {
            return $input->login_type === 'group';
        });
        $validator->sometimes('login_id', new RoleIdIsOwned, function($input) {
            return $input->login_type === 'role';
        });
    }

    public function attributes()
    {
        return [
            'login_id' => 'user'
        ];
    }

}
