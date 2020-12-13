<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\Logic\Contracts\Audience\AudienceMemberFactory;
use BristolSU\Support\User\Contracts\UserAuthentication;
use Illuminate\Http\Request;

class LogIntoParticipantController extends Controller
{

    public function show(Request $request, Activity $activity, AudienceMemberFactory $factory, UserAuthentication $userAuthentication)
    {
        $user = $userAuthentication->getUser()->controlUser();
        $audienceMember = $factory->fromUser($user);
        $audienceMember->filterForLogic($activity->forLogic);

        $canBeUser = ($activity->activity_for === 'user' && $audienceMember->canBeUser());
        $groups = ($activity->activity_for !== 'role' ? $audienceMember->groups() : collect());

        if(!$canBeUser && $groups->isEmpty() && $audienceMember->roles()->isEmpty()) {
            return view('errors.no_activity_access')->with([
                'admin' => false,
                'activity' => $activity
            ]);
        }

        return view('auth.login.resource')->with([
            'admin' => false,
            'user' => $user,
            'canBeUser' => $canBeUser,
            'groups' => $groups,
            'roles' => $audienceMember->roles(),
            'activity' => $activity,
            'redirectTo' => $request->input('redirect')
        ]);
    }

}
