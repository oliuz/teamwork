<?php

namespace Mpociot\Teamwork\Tests;

use Mockery as m;

/**
 * Class TeamworkTeamInviteTraitTest
 * @package Mpociot\Teamwork\Tests
 */
class TeamworkTeamInviteTraitTest extends TestCase
{
    /**
     *
     */
    public function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function get_teams()
    {
        $this->assertEquals($this->team->getKey(), $this->invite->team->getKey());
    }

    /** @test */
    public function get_user()
    {
        $this->assertEquals($this->user->getKey(), $this->invite->user->getKey());
    }

    /** @test */
    public function get_inviter()
    {
        $this->assertEquals($this->inviter->getKey(), $this->invite->inviter->getKey());
    }
}
