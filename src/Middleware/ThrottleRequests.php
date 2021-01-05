<?php


namespace BristolSU\Auth\Middleware;


class ThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{

    protected function resolveRequestSignature($request)
    {
        if ($user = $request->user()) {
            return sha1($user->id());
        }
        return parent::resolveRequestSignature($request);
    }

}
