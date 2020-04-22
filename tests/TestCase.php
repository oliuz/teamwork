<?php

namespace Mpociot\Teamwork\Tests;

use Mpociot\Teamwork\TeamInvite;
use Mpociot\Teamwork\TeamworkTeam;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Application;
use Mpociot\Teamwork\Tests\Models\Task;
use Mpociot\Teamwork\Tests\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class TestCase
 * @package Mpociot\Teamwork\Tests
 */
abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    /** @var User */
    protected $user;

    /** @var TeamInvite */
    protected $invite;

    /** @var TeamworkTeam */
    protected $team;

    /** @var User */
    protected $inviter;

    /** @var Task */
    protected $task;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../tests/migrations');
        $this->setUpDatabase($this->app);
    }

    /**
     * @param Application $app
     * @return array|string[]
     */
    protected function getPackageProviders($app)
    {
        return [\Mpociot\Teamwork\TeamworkServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:'
        ]);
        $app['config']->set('teamwork.user_model', 'User');

        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * @param Application $app
     * @return array|string[]
     */
    protected function getPackageAliases($app)
    {
        return [
            'Teamwork' => \Mpociot\Teamwork\Facades\Teamwork::class
        ];
    }

    /**
     * Set up the database.
     *
     * @param Application $app
     */
    protected function setUpDatabase($app)
    {
        $this->artisan('migrate', [
            '--database' => 'testing',
            '--realpath' => realpath(__DIR__ . '/../tests/migrations'),
        ]);

        $this->user = new User();
        $this->user->name = 'Julia';
        $this->user->email = 'foo@baz.com';
        $this->user->password = bcrypt('password');
        $this->current_team_id = $this->team->getKey();
        $this->user->save();

        $this->inviter = new User();
        $this->inviter->name = 'Marcel';
        $this->inviter->email = 'foo@bar.com';
        $this->inviter->password = bcrypt('password');
        $this->inviter->save();

        $this->team = TeamworkTeam::create([
            'name' => 'Test-Teams',
            'slug' => 'test-teams'
        ]);

        $this->invite = new TeamInvite();
        $this->invite->team_id = $this->team->getKey();
        $this->invite->user_id = $this->inviter->getKey();
        $this->invite->email = $this->user->email;
        $this->invite->type = 'invite';
        $this->invite->accept_token = md5(uniqid(microtime()));
        $this->invite->deny_token = md5(uniqid(microtime()));
        $this->invite->save();

        $this->task = new Task();
        $this->task->team_id = $this->team->getKey();
        $this->task->name = 'Task 1';
        $this->task->save();

    }

    public function createDummyAuthUser(): User
    {
        $user = new User;
        $user->name = "User";
        $user->email = "user@email.com";
        $user->password = bcrypt("password");
        $user->save();

        auth()->login($user);

        return $user;
    }
}
