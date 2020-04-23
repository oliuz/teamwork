<?php

namespace Mpociot\Teamwork\Tests;

use Exception;
use Mockery as m;
use Mpociot\Teamwork\TeamInvite;
use Mpociot\Teamwork\TeamworkTeam;
use Illuminate\Support\Facades\Config;

/**
 * Class TeamworkTest
 * @package Mpociot\Teamwork\Tests
 */
class TeamworkTest extends TestCase
{
    /**
     * @param null $team
     * @return mixed
     */
    protected function createInvite($team = null)
    {
        $user = $this->createDummyAuthUser();

        if (is_null($team)) {
            $team = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        }

        $invite = $this->app->make(Config::get('teamwork.invite_model'));
        $invite->user_id = $user->getKey();
        $invite->team_id = $team->getKey();
        $invite->type = 'invite';
        $invite->email = 'foo@bar.com';
        $invite->accept_token = md5(uniqid(microtime()));
        $invite->deny_token = md5(uniqid(microtime()));
        $invite->save();

        return $invite;
    }

    /**
     *
     */
    public function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function user()
    {
        $this->assertNull(\Teamwork::user());
        auth()->login($this->user);
        $this->assertEquals($this->user, \Teamwork::user());
    }

    /** @test */
    public function get_invite_from_tokens()
    {
        $invite = $this->createInvite();

        $this->assertEquals($invite->toArray(), \Teamwork::getInviteFromAcceptToken($invite->accept_token)->toArray());
        $this->assertEquals($invite->toArray(), \Teamwork::getInviteFromDenyToken($invite->deny_token)->toArray());
    }

    /** @test */
    public function a_deny_invite()
    {
        $invite = $this->createInvite();
        \Teamwork::denyInvite($invite);
        $this->assertNull(TeamInvite::find($invite->getKey()));
    }

    /** @test */
    public function has_pending_invite_false()
    {
        $this->assertFalse(\Teamwork::hasPendingInvite('foo@bar.com', 1));
    }

    /** @test */
    public function has_pending_invite_true()
    {
        $invite = $this->createInvite();
        $this->assertTrue(\Teamwork::hasPendingInvite($invite->email, $invite->team_id));
    }

    /** @test */
    public function has_pending_invite_from_object()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $invite = $this->createInvite($team);
        $this->assertTrue(\Teamwork::hasPendingInvite($invite->email, $team));
    }

    /** @test */
    public function has_pending_invite_from_array()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $invite = $this->createInvite($team);
        $this->assertTrue(\Teamwork::hasPendingInvite($invite->email, $team->toArray()));
    }

    /** @test
     * @throws Exception
     */
    public function can_not_invite_to_user_without_email()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team Q', 'slug' => 'test-team-q']);
        $this->user->attachTeam($team);
        auth()->login($this->user);

        throw new \Exception('The provided object has no "email" attribute and is not a string.');

        \Teamwork::inviteToTeam($this->user);
    }

    /** @test */
    public function can_accept_invite()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team9', 'slug' => 'test-team9']);
        $invite = $this->createInvite($team);
        auth()->login($this->user);
        \Teamwork::acceptInvite($invite);

        $this->assertCount(1, $this->user->teams);
        $this->assertEquals($team->getKey(), $this->user->current_team_id);

        $this->assertNull(\TeamInvite::find($invite->getKey()));
    }

    /** @test */
    public function can_invite_to_team()
    {
        $user = $this->createDummyAuthUser();

        $email = "asd@fake.com";
        $team = TeamworkTeam::create(['name' => 'Test', 'slug' => 'test']);

        $callback = m::mock('stdClass');
        $callback->shouldReceive('callback')->once()
            ->with(m::type(TeamInvite::class))->andReturn();
        \Teamwork::inviteToTeam($email, $team->getKey(), array($callback, 'callback'));

        $this->assertDatabaseHas(config('teamwork.team_invites_table'), [
            'email' => 'asd@fake.com',
            'user_id' => $user->getKey(),
            'team_id' => $team->getKey()
        ]);
    }

    /** @test */
    public function can_invite_to_team_with_object()
    {
        $user = $this->createDummyAuthUser();

        $email = "asd@fake.com";
        $team = TeamworkTeam::create(['name' => 'Test', 'slug' => 'test']);

        $callback = m::mock('stdClass');
        $callback->shouldReceive('callback')->once()
            ->with(m::type(TeamInvite::class))->andReturn();
        \Teamwork::inviteToTeam($email, $team, array($callback, 'callback'));

        $this->assertDatabaseHas(config('teamwork.team_invites_table'), [
            'email' => 'asd@fake.com',
            'user_id' => $user->getKey(),
            'team_id' => $team->getKey()
        ]);
    }

    /** @test */
    public function can_invite_to_team_with_array()
    {
        $user = $this->createDummyAuthUser();

        $email = "asd@fake.com";
        $team = TeamworkTeam::create(['name' => 'Test', 'slug' => 'test']);

        $callback = m::mock('stdClass');
        $callback->shouldReceive('callback')->once()
            ->with(m::type(TeamInvite::class))->andReturn();
        \Teamwork::inviteToTeam($email, $team->toArray(), array($callback, 'callback'));

        $this->assertDatabaseHas(config('teamwork.team_invites_table'), [
            'email' => 'asd@fake.com',
            'user_id' => $user->getKey(),
            'team_id' => $team->getKey()
        ]);
    }

    /** @test */
    public function can_invite_to_team_with_user()
    {
        $user = $this->createDummyAuthUser();
        $user->email = "asd@fake.com";
        $team = TeamworkTeam::create(['name' => 'Test', 'slug' => 'test']);

        $callback = m::mock('stdClass');
        $callback->shouldReceive('callback')->once()
            ->with(m::type(TeamInvite::class))->andReturn();
        \Teamwork::inviteToTeam($user, $team->toArray(), array($callback, 'callback'));

        $this->assertDatabaseHas(config('teamwork.team_invites_table'), [
            'email' => 'asd@fake.com',
            'user_id' => $user->getKey(),
            'team_id' => $team->getKey()
        ]);
    }

    /** @test */
    public function can_invite_to_team_with_null()
    {
        $user = $this->createDummyAuthUser();

        $email = "asd@fake.com";
        $team = TeamworkTeam::create(['name' => 'test', 'slug' => 'test']);
        $user->attachTeam($team);

        $callback = m::mock('stdClass');
        $callback->shouldReceive('callback')->once()
            ->with(m::type(TeamInvite::class))->andReturn();
        \Teamwork::inviteToTeam($email, null, array($callback, 'callback'));

        $this->assertDatabaseHas(config('teamwork.team_invites_table'), [
            'email' => 'asd@fake.com',
            'team_id' => $team->getKey()
        ]);
    }

    /** @test */
    public function can_invite_to_team_without_callback()
    {
        $user = $this->createDummyAuthUser();

        $email = "asd@fake.com";
        $team = TeamworkTeam::create(['name' => 'test', 'slug' => 'test']);
        $user->attachTeam($team);

        \Teamwork::inviteToTeam($email);

        $this->assertDatabaseHas(config('teamwork.team_invites_table'), [
            'email' => 'asd@fake.com',
            'team_id' => $team->getKey()
        ]);
    }

    /** @test
     * @throws Exception
     */
    public function invite_to_team_fires_event()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $invite = $this->createInvite($team);
        $this->expectsEvents(\Mpociot\Teamwork\Events\UserInvitedToTeam::class);
    }
}

