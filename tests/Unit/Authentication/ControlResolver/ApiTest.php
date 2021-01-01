<?php

namespace BristolSU\Auth\Tests\Unit\Authentication\ControlResolver;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Authentication\ControlResolver\Api as ApiControlResolver;
use BristolSU\Auth\Tests\helpers\SessionAuthenticationResolver;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Repositories\Group as GroupRepository;
use BristolSU\ControlDB\Contracts\Repositories\Role as RoleRepository;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use BristolSU\ControlDB\Models\Group;
use BristolSU\ControlDB\Models\Role;
use BristolSU\ControlDB\Models\User;
use BristolSU\Auth\Tests\TestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;

class ApiTest extends TestCase
{

    /** @test */
    public function get_group_returns_null_if_not_logged_into_a_group_or_role()
    {
        $authentication = resolve(ApiControlResolver::class);
        $this->assertNull($authentication->getGroup());
    }

    /** @test */
    public function get_group_returns_a_group_if_given_in_url()
    {
        $group = $this->newGroup();
        $groupRepository = $this->prophesize(GroupRepository::class);
        $groupRepository->getById($group->id())->shouldBeCalled()->willReturn($group);

        $query = $this->prophesize(ParameterBag::class);
        $query->has('role_id')->shouldBeCalled()->willReturn(false);
        $query->has('group_id')->shouldBeCalled()->willReturn(true);
        $query->get('group_id')->shouldBeCalled()->willReturn($group->id());

        $request = $this->prophesize(Request::class)->reveal();
        $request->query = $query->reveal();

        $authentication = resolve(ApiControlResolver::class, [
            'request' => $request,
            'groupRepository' => $groupRepository->reveal()
        ]);

        $this->assertInstanceOf(Group::class, $authentication->getGroup());
        $this->assertModelEquals($group, $authentication->getGroup());
        $this->assertEquals(1, $authentication->getGroup()->id);
    }

    /** @test */
    public function get_group_returns_a_group_given_by_a_role_if_role_given(){
        $group = $this->newGroup();
        $role = $this->newRole(['group_id' => $group->id()]);
        $roleRepository = $this->prophesize(RoleRepository::class);
        $roleRepository->getById($role->id())->shouldBeCalled()->willReturn($role);

        $query = $this->prophesize(ParameterBag::class);
        $query->has('role_id')->shouldBeCalled()->willReturn(true);
        $query->get('role_id')->shouldBeCalled()->willReturn($role->id());

        $request = $this->prophesize(Request::class)->reveal();
        $request->query = $query->reveal();

        $authentication = resolve(ApiControlResolver::class, [
            'request' => $request,
            'roleRepository' => $roleRepository->reveal()
        ]);
        $this->assertInstanceOf(Group::class, $authentication->getGroup());
        $this->assertEquals($group->id(), $authentication->getGroup()->id());
    }

    /** @test */
    public function get_role_returns_null_if_not_logged_into_role()
    {
        $authentication = resolve(ApiControlResolver::class);
        $this->assertNull($authentication->getRole());
    }

    /** @test */
    public function get_role_returns_a_role_if_given_in_query()
    {

        $role = $this->newRole();
        $roleRepository = $this->prophesize(RoleRepository::class);
        $roleRepository->getById($role->id())->shouldBeCalled()->willReturn($role);

        $query = $this->prophesize(ParameterBag::class);
        $query->has('role_id')->shouldBeCalled()->willReturn(true);
        $query->get('role_id')->shouldBeCalled()->willReturn($role->id());

        $request = $this->prophesize(Request::class)->reveal();
        $request->query = $query->reveal();

        $authentication = resolve(ApiControlResolver::class, [
            'request' => $request,
            'roleRepository' => $roleRepository->reveal()
        ]);
        $this->assertInstanceOf(Role::class, $authentication->getRole());
        $this->assertModelEquals($role, $authentication->getRole());
    }

    /** @test */
    public function get_user_returns_null_if_not_logged_into_a_user()
    {
        $authResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authResolver->hasUser()->willReturn(false);

        $authentication = resolve(ApiControlResolver::class, [
            'authenticationResolver' => $authResolver->reveal()
        ]);
        $this->assertNull($authentication->getUser());
    }

    /** @test */
    public function get_user_returns_a_user_if_given_in_resolver()
    {
        $user = $this->newUser();
        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->getById($user->id())->shouldBeCalled()->willReturn($user);

        $authUser = AuthenticationUser::factory()->create(['control_id' => $user->id()]);
        $authResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authResolver->hasUser()->willReturn(true);
        $authResolver->getUser()->willReturn($authUser);

        $authentication = resolve(ApiControlResolver::class, [
            'userRepository' => $userRepository->reveal(),
            'authenticationResolver' => $authResolver->reveal()
        ]);

        $resolvedUser = $authentication->getUser();
        $this->assertInstanceOf(User::class, $resolvedUser);
        $this->assertModelEquals($resolvedUser, $authentication->getUser());
    }

    /** @test */
    public function has_user_returns_false_if_a_user_is_not_logged_in(){
        $authUser = AuthenticationUser::factory()->create();
        $authResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authResolver->hasUser()->willReturn(false);

        $authentication = resolve(ApiControlResolver::class, [
            'authenticationResolver' => $authResolver->reveal()
        ]);

        $this->assertFalse($authentication->hasUser());
    }

    /** @test */
    public function has_user_returns_true_if_a_user_is_logged_in(){
        $authUser = AuthenticationUser::factory()->create();
        $authResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authResolver->hasUser()->willReturn(true);

        $authentication = resolve(ApiControlResolver::class, [
            'authenticationResolver' => $authResolver->reveal()
        ]);

        $this->assertTrue($authentication->hasUser());
    }

    /** @test */
    public function set_user_sets_the_user()
    {
        $user = $this->newUser();
        $authenticationUser = AuthenticationUser::factory()->create(['control_id' => $user->id()]);
        $authenticationUserRepository = $this->prophesize(AuthenticationUserRepository::class);
        $authenticationUserRepository->getFromControlId($user->id())->shouldBeCalled()->willReturn($authenticationUser);

        $authentication = resolve(ApiControlResolver::class, [
            'authenticationUserRepository' => $authenticationUserRepository->reveal(),
            'authenticationResolver' => new SessionAuthenticationResolver()
        ]);

        $this->assertNull($authentication->getUser());
        $authentication->setUser($user);
        $this->assertInstanceOf(User::class, $authentication->getUser());
        $this->assertModelEquals($user, $authentication->getUser());
    }

    /** @test */
    public function set_group_sets_the_group()
    {
        $group = $this->newGroup();
        $groupRepository = $this->prophesize(GroupRepository::class);
        $groupRepository->getById($group->id())->shouldBeCalled()->willReturn($group);

        $authentication = resolve(ApiControlResolver::class, [
            'groupRepository' => $groupRepository->reveal()
        ]);

        $authentication->setGroup($group);
        $this->assertInstanceOf(Group::class, $authentication->getGroup());
        $this->assertEquals($group, $authentication->getGroup());
    }

    /** @test */
    public function set_role_sets_the_role()
    {
        $role = $this->newRole();
        $roleRepository = $this->prophesize(RoleRepository::class);
        $roleRepository->getById($role->id())->shouldBeCalled()->willReturn($role);
        $this->app->instance(RoleRepository::class, $roleRepository->reveal());

        $authentication = resolve(ApiControlResolver::class, [
            'roleRepository' => $roleRepository->reveal()
        ]);

        $authentication->setRole($role);
        $this->assertInstanceOf(Role::class, $authentication->getRole());
        $this->assertEquals($role, $authentication->getRole());
    }

    /** @test */
    public function getGroup_returns_null_if_exception_thrown_in_repository(){
        $query = $this->prophesize(ParameterBag::class);
        $query->has('role_id')->shouldBeCalled()->willReturn(false);
        $query->has('group_id')->shouldBeCalled()->willReturn(true);
        $query->get('group_id')->shouldBeCalled()->willReturn(1);

        $request = $this->prophesize(Request::class)->reveal();
        $request->query = $query->reveal();

        $groupRepository = $this->prophesize(GroupRepository::class);
        $groupRepository->getById(1)->shouldBeCalled()->willThrow(new \Exception());

        $authentication = resolve(ApiControlResolver::class, [
            'request' => $request,
            'groupRepository' => $groupRepository->reveal(),
        ]);

        $this->assertNull($authentication->getGroup());
    }

    /** @test */
    public function getRole_returns_null_if_exception_thrown_in_repository(){
        $query = $this->prophesize(ParameterBag::class);
        $query->has('role_id')->shouldBeCalled()->willReturn(true);
        $query->get('role_id')->shouldBeCalled()->willReturn(1);

        $request = $this->prophesize(Request::class)->reveal();
        $request->query = $query->reveal();

        $roleRepository = $this->prophesize(RoleRepository::class);
        $roleRepository->getById(1)->shouldBeCalled()->willThrow(new \Exception());

        $authentication = resolve(ApiControlResolver::class, [
            'request' => $request,
            'roleRepository' => $roleRepository->reveal(),
        ]);
        $this->assertNull($authentication->getRole());
    }

    /** @test */
    public function getUser_returns_null_if_exception_thrown_in_control_repository(){
        $user = $this->newUser();

        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->getById($user->id())->shouldBeCalled()->willThrow(new \Exception());

        $authenticationUser = AuthenticationUser::factory()->create(['control_id' => $user->id()]);

        $authentication = resolve(ApiControlResolver::class, [
            'userRepository' => $userRepository->reveal(),
            'authenticationResolver' => new SessionAuthenticationResolver($authenticationUser)
        ]);

        $this->assertNull($authentication->getUser());
    }

    /** @test */
    public function getUser_returns_null_if_exception_thrown_in_authentication_repository(){
        $user = $this->newUser();

        $authenticationResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authenticationResolver->hasUser()->willReturn(true);
        $authenticationResolver->getUser()->willThrow(new \Exception());

        $authentication = resolve(ApiControlResolver::class, [
            'authenticationResolver' => $authenticationResolver->reveal()
        ]);

        $this->assertNull($authentication->getUser());
    }

    /** @test */
    public function setUser_handles_a_repository_from_the_repository(){
        $user = $this->newUser();

        $authenticationUserRepository = $this->prophesize(AuthenticationUserRepository::class);
        $authenticationUserRepository->getFromControlId($user->id())->willThrow(new ModelNotFoundException());

        $authentication = resolve(ApiControlResolver::class, [
            'authenticationUserRepository' => $authenticationUserRepository->reveal()
        ]);

        $this->assertNull($authentication->setUser($user));
    }
}
