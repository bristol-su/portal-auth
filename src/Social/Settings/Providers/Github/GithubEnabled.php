<?php

namespace BristolSU\Auth\Social\Settings\Providers\Github;

use BristolSU\Auth\Social\Settings\Providers\BaseProviderEnabledSetting;

class GithubEnabled extends BaseProviderEnabledSetting
{

    public function driver(): string
    {
        return 'github';
    }
}
