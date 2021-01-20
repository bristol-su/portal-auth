<?php


namespace BristolSU\Auth\Social\Settings\Providers\Github;


use BristolSU\Auth\Social\Settings\BaseSocialDriversGroup;

class GithubGroup extends BaseSocialDriversGroup
{

    public function driver(): string
    {
        return 'github';
    }
}
