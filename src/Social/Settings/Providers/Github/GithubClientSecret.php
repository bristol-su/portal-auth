<?php

namespace BristolSU\Auth\Social\Settings\Providers\Github;

class GithubClientSecret extends \BristolSU\Auth\Social\Settings\Providers\BaseProviderClientSecretSetting
{

    public function driver(): string
    {
        return 'github';
    }

}
