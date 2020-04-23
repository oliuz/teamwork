<?php

namespace Mpociot\Teamwork\Tests;

use Illuminate\Support\Facades\Config;
use Mockery as m;
use Mpociot\Teamwork\Traits\TeamworkTeamTrait;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TeamworkTeamTraitTest
 */
class TeamworkTeamTraitTest extends TestCase
{

    /**
     *
     */
    public function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function get_invites_the_team()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('teamwork.invite_model')
            ->andReturn('Invite');

        $stub = m::mock('\Mpociot\Teamwork\Tests\TestUserTeamTraitStub[hasMany]');
        $stub->shouldReceive('hasMany')
            ->once()
            ->with('Invite', 'team_id', 'id')
            ->andReturn([]);
        $this->assertEquals([], $stub->invites());
    }

    /** @test */
    public function get_users_for_the_team()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('teamwork.user_model')
            ->andReturn('User');

        Config::shouldReceive('get')
            ->once()
            ->with('teamwork.team_user_table')
            ->andReturn('TeamUser');

        $stub = m::mock('\Mpociot\Teamwork\Tests\TestUserTeamTraitStub[belongsToMany,withTimestamps]');

        $stub->shouldReceive('withTimestamps')
            ->once()
            ->andReturn([]);

        $stub->shouldReceive('belongsToMany')
            ->once()
            ->with('User', 'TeamUser', 'team_id', 'user_id')
            ->andReturnSelf();

        $this->assertEquals([], $stub->users());
    }

    /** @test */
    public function get_owner_a_the_team()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('teamwork.user_model')
            ->andReturn('\Mpociot\Teamwork\Tests\TestUser');

        $stub = m::mock('\Mpociot\Teamwork\Tests\TestUserTeamTraitStub[belongsTo]');
        $stub->shouldReceive('belongsTo')
            ->with('User', 'owner_id', 'user_id')
            ->once()
            ->andReturn([]);
        $this->assertEquals([], $stub->owner());
    }

    /** @test */
    public function has_user()
    {
        $stub = m::mock('\Mpociot\Teamwork\Tests\TestUserTeamTraitStub[users,first]');

        $user = m::mock('\Mpociot\Teamwork\Tests\TestUser[getKey]');
        $user->shouldReceive('getKey')
            ->once()
            ->andReturn('key');

        $stub->shouldReceive('first')
            ->once()
            ->andReturn(true);

        $stub->shouldReceive('where')
            ->once()
            ->with("user_id", "=", "key")
            ->andReturnSelf();

        $stub->shouldReceive('users')
            ->andReturnSelf();

        $this->assertTrue($stub->hasUser($user));
    }

    /** @test */
    public function has_user_returns_false()
    {
        $stub = m::mock('\Mpociot\Teamwork\Tests\TestUserTeamTraitStub[users,first]');

        $user = m::mock('\Mpociot\Teamwork\Tests\TestUser[getKey]');
        $user->shouldReceive('getKey')
            ->once()
            ->andReturn('key');

        $stub->shouldReceive('first')
            ->once()
            ->andReturn(false);

        $stub->shouldReceive('where')
            ->once()
            ->with("user_id", "=", "key")
            ->andReturnSelf();

        $stub->shouldReceive('users')
            ->andReturnSelf();

        $this->assertFalse($stub->hasUser($user));
    }

}

/**
 * Class TestUser
 * @package Mpociot\Teamwork\Tests
 */
class TestUser extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @return string
     */
    public function getKeyName()
    {
        return "user_id";
    }
}

/**
 * Class TestUserTeamTraitStub
 * @package Mpociot\Teamwork\Tests
 */
class TestUserTeamTraitStub extends \Illuminate\Database\Eloquent\Model
{
    use TeamworkTeamTrait;
}
