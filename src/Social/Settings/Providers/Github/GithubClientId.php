<?php

namespace BristolSU\Auth\Social\Settings\Providers\Github;

class GithubClientId extends \BristolSU\Auth\Social\Settings\Providers\BaseProviderClientIdSetting
{

    public function driver(): string
    {
        return 'github';
    }

}
