<?php

namespace Mpociot\Teamwork\Tests;

use Exception;
use Mockery as m;
use Mpociot\Teamwork\TeamworkTeam;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Mpociot\Teamwork\Tests\Models\User;
use Mpociot\Teamwork\Exceptions\UserNotInTeamException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class UserHasTeamsTraitTest
 * @package Mpociot\Teamwork\Tests
 */
class UserHasTeamsTraitTest extends TestCase
{
    /**
     *
     */
    public function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function new_user_has_no_teams()
    {
        $user = new User();
        $user->name = 'Marcel';
        $user->password = bcrypt('password');
        $user->email = "email@email.com";
        $user->save();

        $this->assertCount(0, $user->teams);
        $this->assertEquals(0, $user->current_team_id);
        $this->assertNull($user->currentTeam);
        $this->assertCount(0, $user->ownedTeams);
        $this->assertCount(0, $user->invites);
    }

    /** @test */
    public function attaching_team_sets_current_team()
    {
        $this->assertNull($this->user->currentTeam);

        $this->user->attachTeam($this->team);

        $this->assertEquals(1, $this->user->currentTeam->getKey());
    }

    /** @test */
    public function can_attach_team_to_user()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team', 'slug' => 'test-team']);

        $this->user->attachTeam($team);

        // Reload relation
        $this->assertCount(1, $this->user->teams);
        $this->assertEquals(TeamworkTeam::find(2)->toArray(), $this->user->currentTeam->toArray());
    }

    /** @test */
    public function can_attach_team_as_array_to_user()
    {
        $team = TeamworkTeam::create(['name' => 'Team2', 'slug' => 'team2']);

        $this->user->attachTeam($team->toArray());

        // Reload relation
        $this->assertCount(1, $this->user->teams);
        $this->assertEquals(TeamworkTeam::find(2)->toArray(), $this->user->currentTeam->toArray());
    }

    /** @test */
    public function can_attach_team_as_id_to_user()
    {
        $user = $this->createDummyAuthUser();

        $team = TeamworkTeam::create(['name' => 'Test-Team', 'slug' => 'test-team']);

        $user->attachTeam($team->getKey());

        // Reload relation
        $this->assertCount(1, $user->teams);
        $this->assertEquals(TeamworkTeam::find(2)->toArray(), $user->currentTeam->toArray());
    }

    /** @test */
    public function can_set_pivot_data_on_attach_team_method()
    {
        $user = $this->createDummyAuthUser();

        Schema::table(config('teamwork.team_user_table'), function ($table) {
            $table->boolean('pivot_set')->default(false);
        });

        $team = TeamworkTeam::create(['name' => 'Test-Team', 'slug' => 'test-team']);
        $pivotData = ['pivot_set' => true];

        $user->attachTeam($team, $pivotData);

        $this->assertDatabaseHas(config('teamwork.team_user_table'), [
            'user_id' => $user->getKey(),
            'team_id' => $team->getKey(),
            'pivot_set' => true
        ]);
    }

    /** @test */
    public function is_team_owner()
    {
        $this->user->attachTeam($this->team->getKey());

        $this->assertFalse($this->user->isTeamOwner());
        $this->assertFalse($this->user->isOwner());

        $user = $this->createDummyAuthUser();
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2', 'owner_id' => $user->getKey()]);
        $user->attachTeam($team2->getKey());

        $this->assertTrue($user->isTeamOwner());
        $this->assertTrue($user->isOwner());
    }

    /** @test */
    public function is_owner_of_team()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team', 'slug' => 'test-team']);
        $this->user->attachTeam($team->getKey());

        $this->assertFalse($this->user->isOwnerOfTeam($team));

        $team = TeamworkTeam::create(['name' => 'Test-Team 4', 'slug' => 'test-team-4', 'owner_id' => $this->user->getKey()]);
        $this->user->attachTeam($team->getKey());

        $this->assertTrue($this->user->isOwnerOfTeam($team));
    }

    /** @test */
    public function get_owned_teams()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team', 'slug' => 'test-team', 'owner_id' => $this->user->getKey()]);
        $this->user->attachTeam($team->getKey());
        $this->assertCount(1, $this->user->ownedTeams);
    }

    /** @test */
    public function can_detach_team()
    {
        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2']);
        $team3 = TeamworkTeam::create(['name' => 'Test-Team 3', 'slug' => 'test-team-3']);

        $this->user->attachTeam($team1);
        $this->user->attachTeam($team2);
        $this->user->attachTeam($team3);

        $this->assertCount(3, $this->user->teams()->get());

        $this->user->detachTeam($team2);
        $this->assertCount(2, $this->user->teams()->get());
    }

    /** @test */
    public function detach_team_resets_current_team()
    {
        $team = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);

        $this->user->attachTeam($team);

        $this->assertEquals($team->getKey(), $this->user->currentTeam->getKey());

        $this->user->detachTeam($team);
        $this->assertNull($this->user->currentTeam);
    }

    /** @test */
    public function attach_team_fires_event()
    {
        Event::fake();

        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $this->user->attachTeam($team1);

        Event::assertDispatched(\Mpociot\Teamwork\Events\UserJoinedTeam::class, function ($e) use ($team1) {
            return $e->getTeamId() === $team1->id && $e->getUser()->id === $this->user->id;
        });
        Event::assertNotDispatched(\Mpociot\Teamwork\Events\UserLeftTeam::class);
    }

    /** @test */
    public function detach_team_fires_event()
    {
        Event::fake();

        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team']);
        $this->user->attachTeam($team1);
        $this->user->detachTeam($team1);

        Event::assertDispatched(\Mpociot\Teamwork\Events\UserLeftTeam::class, function ($e) use ($team1) {
            return $e->getTeamId() === $team1->id && $e->getUser()->id === $this->user->id;
        });
    }

    /** @test */
    public function can_attach_multiple_teams()
    {
        $user = $this->createDummyAuthUser();

        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2']);
        $team3 = TeamworkTeam::create(['name' => 'Test-Team 3', 'slug' => 'test-team-3']);

        $user->attachTeams([
            $team1,
            $team2,
            $team3
        ]);

        $this->assertCount(3, $user->teams()->get());
    }

    /** @test */
    public function can_detach_multiple_teams()
    {
        $user = $this->createDummyAuthUser();

        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2']);
        $team3 = TeamworkTeam::create(['name' => 'Test-Team 3', 'slug' => 'test-team-3']);

        $user->attachTeams([
            $team1,
            $team2,
            $team3
        ]);

        $this->assertCount(3, $user->teams()->get());

        $user->detachTeams([
            $team1,
            $team3
        ]);

        $this->assertCount(1, $user->teams()->get());
    }

    /** @test */
    public function the_current_team_gets_reset_when_detached()
    {
        $user = $this->createDummyAuthUser();

        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2']);
        $team3 = TeamworkTeam::create(['name' => 'Test-Team 3', 'slug' => 'test-team-3']);

        $user->attachTeams([
            $team1,
            $team2,
            $team3
        ]);

        $this->assertEquals($team1->getKey(), $user->currentTeam->getKey());

        $user->detachTeam($team1);

        $this->assertNull($user->currentTeam);
    }

    /** @test */
    public function a_user_can_switch_team()
    {
        $user = $this->createDummyAuthUser();

        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2']);
        $team3 = TeamworkTeam::create(['name' => 'Test-Team 3', 'slug' => 'test-team-3']);

        $this->user->attachTeams([
            $team1,
            $team2,
            $team3
        ]);
        $this->assertEquals($team1->getKey(), $this->user->currentTeam->getKey());
        $user->switchTeam($team2);
        $this->assertEquals($team2->getKey(), $this->user->currentTeam->getKey());
    }

    /** @test */
    public function user_cannot_switch_to_invalid_team()
    {
        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2']);
        $team3 = TeamworkTeam::create(['name' => 'Test-Team 3', 'slug' => 'test-team-3']);

        $this->user->attachTeams([
            $team1,
            $team2
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The user is not in the team Test-Team 3');
        $this->user->switchTeam($team3);
    }

    /** @test */
    public function a_user_cannot_switch_to_not_existing_team()
    {
        $team1 = TeamworkTeam::create(['name' => 'Test-Team 1', 'slug' => 'test-team-1']);
        $team2 = TeamworkTeam::create(['name' => 'Test-Team 2', 'slug' => 'test-team-2']);

        $this->user->attachTeams([
            $team1,
            $team2
        ]);

//        $this->setExpectedException();
        $this->expectException(Exception::class);
        $this->user->switchTeam(3);
    }
}
