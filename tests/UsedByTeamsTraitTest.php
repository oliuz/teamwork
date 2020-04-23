<?php

namespace Mpociot\Teamwork\Tests;

use Exception;
use Mockery as m;
use Mpociot\Teamwork\TeamworkTeam;
use Mpociot\Teamwork\Tests\Models\Task;
use Mpociot\Teamwork\Tests\Models\User;

/**
 * Class UsedByTeamsTraitTest
 * @package Mpociot\Teamwork\Tests
 */
class UsedByTeamsTraitTest extends TestCase
{
    /**
     *
     */
    public function tearDown(): void
    {
        m::close();
    }

    /** @test
     * @throws Exception
     */
    public function a_throws_exception_when_unauthorized()
    {
        throw new \Exception('No authenticated user with selected team present.');

        $task = new Task();
        $task->name = 'Buy milk';
        $task->save();
    }

    /** @test */
    public function gets_a_current_team_tasks()
    {
        $user = $this->createDummyAuthUser();

        $team = TeamworkTeam::create(['name' => 'Team2', 'slug' => 'team2', 'owner_id' => $user->getKey()]);

        $user->attachTeam($team);

        $task = new Task();
        $task->team_id = $user->currentTeam->getKey();
        $task->name = 'Buy milk';
        $task->save();

        $task2 = new Task();
        $task2->team_id = $user->currentTeam->getKey() + 1;
        $task2->name = 'Buy steaks';
        $task2->save();

        $tasks = Task::all();

        $this->assertCount(1, [$tasks]);
        $this->assertEquals($task->id, $tasks->first()->id);
        $this->assertEquals($task->team_id, $tasks->first()->team_id);
        $this->assertEquals($task->name, $tasks->first()->name);
    }

    /** @test */
    public function gets_a_all_tasks()
    {
        $user = $this->createDummyAuthUser();

        $team1 = TeamworkTeam::create(['name' => 'Team1', 'slug' => 'team1', 'owner_id' => $user->getKey()]);
        $team2 = TeamworkTeam::create(['name' => 'Team2', 'slug' => 'team2', 'owner_id' => $user->getKey()]);

        Task::create(['name' => 'Task1', 'team_id' => $team1->getKey()]);
        Task::create(['name' => 'Task1', 'team_id' => $team2->getKey()]);

        $tasks = Task::allTeams()->get();
        $this->assertCount(3, $tasks);
    }

    /** @test */
    public function a_scope_automatically_adds_current_team()
    {
        $user = $this->createDummyAuthUser();

        $team = TeamworkTeam::create(['name' => 'Team1', 'slug' => 'team1', 'owner_id' => $user->getKey()]);

        Task::create(['name' => 'Task 1', 'team_id' => $team->getKey()]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Task 1',
            'team_id' => $team->getKey()
        ]);
    }
}
