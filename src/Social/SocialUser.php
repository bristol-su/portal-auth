<?php

namespace BristolSU\Auth\Social;

use BristolSU\Auth\User\AuthenticationUser;
use Database\Auth\Factories\SocialUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a user in the database
 */
class SocialUser extends Model
{
    use HasFactory;

    protected $table = 'social_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public function authenticationUser()
    {
        return $this->belongsTo(AuthenticationUser::class);
    }

    public function authenticationUserId(): int
    {
        return (int) $this->authentication_user_id;
    }

    /**
     * Get the ID of the control
     *
     * @return int
     */
    public function id(): int
    {
        return (int)$this->id;
    }

    /**
     * Get the control user attached to this database user
     *
     * @return string
     */
    public function providerId(): string
    {
        return (string)$this->provider_id;
    }

    /**
     * Get the email of the user
     *
     * @return string|null
     */
    public function email(): ?string
    {
        return $this->email;
    }

    /**
     * Get the name of the user
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * Get the provider of the social user
     *
     * @return string
     */
    public function provider(): string
    {
        return (string) $this->provider;
    }

    protected static function newFactory(): SocialUserFactory
    {
        return SocialUserFactory::new();
    }

}
