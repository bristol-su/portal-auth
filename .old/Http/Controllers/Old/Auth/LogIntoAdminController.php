<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LogIntoAdmin\LoginRequest;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\Logic\Contracts\Audience\AudienceMemberFactory;
use BristolSU\Support\User\Contracts\UserAuthentication;
use Illuminate\Http\Request;

class LogIntoAdminController extends Controller
{

    public function show(Request $request, Activity $activity, AudienceMemberFactory $factory, UserRepository $userRepository, UserAuthentication $userAuthentication)
    {
        $user = $userRepository->getById($userAuthentication->getUser()->control_id);
        $audienceMember = $factory->fromUser($user);
        $audienceMember->filterForLogic($activity->adminLogic);

        $canBeUser = ($activity->activity_for === 'user' && $audienceMember->canBeUser());
        $groups = ($activity->activity_for !== 'role' ? $audienceMember->groups() : collect());

        if(!$canBeUser && $groups->isEmpty() && $audienceMember->roles()->isEmpty()) {
            return view('errors.no_activity_access')->with([
                'admin' => true,
                'activity' => $activity
            ]);
        }

        return view('auth.login.resource')->with([
            'admin' => true,
            'user' => $user,
            'canBeUser' => $canBeUser,
            'groups' => $groups,
            'roles' => $audienceMember->roles(),
            'activity' => $activity,
            'redirectTo' => $request->input('redirect')
        ]);
    }

}
