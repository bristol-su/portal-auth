<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigratePortalAuthSettingValues extends Migration
{

    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        foreach ($this->getKeysToChange() as $original => $new) {
            \BristolSU\Support\Settings\Saved\SavedSettingModel::key($original)->update(['key' => $new]);
        }
    }

    protected function getKeysToChange(): array
    {
        return [
            'authentication.registration_identifier.identifier' => \BristolSU\Auth\Settings\Credentials\IdentifierAttribute::getKey(),
            'authentication.registration_identifier.validation' => \BristolSU\Auth\Settings\Credentials\IdentifierAttributeValidation::getKey(),
            'authentication.password.validation' => \BristolSU\Auth\Settings\Credentials\PasswordValidation::getKey(),
            'authentication.verification.required' => \BristolSU\Auth\Settings\Security\ShouldVerifyEmail::getKey(),
        'authentication.authorization.requiredAlreadyInControl' => \BristolSU\Auth\Settings\Access\ControlUserRegistrationEnabled::getKey(),
        'authentication.authorization.requiredAlreadyInData' => \BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled::getKey(),
        'authentication.messages.notInData' => \BristolSU\Auth\Settings\Messaging\DataUserRegistrationNotAllowedMessage::getKey(),
        'authentication.messages.notInControl' => \BristolSU\Auth\Settings\Messaging\ControlUserRegistrationNotAllowedMessage::getKey(),
        'authentication.messages.alreadyRegistered' => \BristolSU\Auth\Settings\Messaging\AlreadyRegisteredMessage::getKey()
    ];
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        foreach ($this->getKeysToChange() as $original => $new) {
            \BristolSU\Support\Settings\Saved\SavedSettingModel::key($new)->update(['key' => $original]);
        }
    }
}
