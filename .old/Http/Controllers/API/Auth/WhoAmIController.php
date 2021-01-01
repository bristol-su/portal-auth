<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use BristolSU\Support\User\Contracts\UserAuthentication;

class WhoAmIController extends Controller
{

    public function index(UserAuthentication $authentication)
    {
        $user = $authentication->getUser();
        $controlUser = $user->controlUser();
        $userAsArray = $user->toArray();
        $userAsArray['control'] = $controlUser->toArray();

        return $userAsArray;
    }

}
