<?php

namespace BristolSU\Auth\Authentication\ControlResolver;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Models\Group;
use BristolSU\ControlDB\Contracts\Models\Role;
use BristolSU\ControlDB\Contracts\Models\User;
use BristolSU\ControlDB\Contracts\Repositories\Group as GroupRepository;
use BristolSU\ControlDB\Contracts\Repositories\Role as RoleRepository;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use BristolSU\Support\Authentication\Contracts\Authentication;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Api Authentication for getting authentication models from the query string
 */
class Api implements Authentication
{

    /**
     * Holds the request object
     *
     * @var Request
     */
    private $request;

    /**
     * Holds the role repository
     *
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * Holds the group repository
     *
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * Holds the user repository
     *
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AuthenticationUserResolver
     */
    private AuthenticationUserResolver $authenticationResolver;
    /**
     * @var AuthenticationUserRepository
     */
    private AuthenticationUserRepository $authenticationUserRepository;

    /**
     * Initialise the API authentication
     *
     * @param Request $request Request object to get parameters from
     * @param RoleRepository $roleRepository Role repository for retrieving roles
     * @param GroupRepository $groupRepository Group repository for retrieving groups
     * @param UserRepository $userRepository User repository for retrieving users
     * @param AuthenticationUserResolver $authenticationResolver
     */
    public function __construct(Request $request,
                                RoleRepository $roleRepository,
                                GroupRepository $groupRepository,
                                UserRepository $userRepository,
                                AuthenticationUserResolver $authenticationResolver,
                                AuthenticationUserRepository $authenticationUserRepository)
    {
        $this->request = $request;
        $this->roleRepository = $roleRepository;
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->authenticationResolver = $authenticationResolver;
        $this->authenticationUserRepository = $authenticationUserRepository;
    }

    /**
     * Get a group from the group_id parameter
     *
     * @return Group|null
     */
    public function getGroup()
    {
        if ($this->hasGroup()) {

            if ($this->hasRole()) {
                return $this->getRole()->group();
            }

            try {
                return $this->groupRepository->getById($this->request->query->get('group_id'));
            } catch (Exception $e) {
            }
        }
        return null;
    }

    /**
     * Get a role from the role_id parameter
     *
     * @return Role|null
     */
    public function getRole()
    {
        if ($this->hasRole()) {
            try {
                return $this->roleRepository->getById($this->request->query->get('role_id'));
            } catch (Exception $e) {
            }
        }
        return null;
    }

    /**
     * Get a user from the user_id parameter
     *
     * @return User|null
     */
    public function getUser()
    {
        if($this->authenticationResolver->hasUser()) {
            try {
                $authenticationUser = $this->authenticationResolver->getUser();
                return $this->userRepository->getById(
                    $authenticationUser->controlId()
                );
            } catch (Exception $e) {}
        }
        return null;
    }

    /**
     * Set the group
     *
     * @param Group $group
     * @return void
     */
    public function setGroup(Group $group)
    {
        $this->request->query->set('group_id', $group->id());
    }

    /**
     * Set the role
     *
     * @param Role $role
     * @return void
     */
    public function setRole(Role $role)
    {
        $this->request->query->set('role_id', $role->id());
    }

    /**
     * Set the user
     *
     * @param User $user
     * @return void
     */
    public function setUser(User $user)
    {
        try {
            $this->authenticationResolver->setUser(
                $this->authenticationUserRepository->getFromControlId($user->id())
            );
        } catch (ModelNotFoundException $e) {}
    }

    public function hasGroup(): bool
    {
        return $this->hasRole() || $this->request !== null && $this->request->query->has('group_id');
    }

    public function hasRole(): bool
    {
        return $this->request !== null && $this->request->query->has('role_id');
    }

    public function hasUser(): bool
    {
        return $this->authenticationResolver->hasUser();
    }
}
