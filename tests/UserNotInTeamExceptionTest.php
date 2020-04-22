<?php

namespace Mpociot\Teamwork\Tests;

use Mockery as m;

/**
 * Class UserNotInTeamExceptionTest
 * @package Mpociot\Teamwork\Tests
 */
class UserNotInTeamExceptionTest extends TestCase
{

    /**
     *
     */
    public function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function get_team_exception()
    {
        $exception = new \Mpociot\Teamwork\Exceptions\UserNotInTeamException();
        $exception->setTeam( "Test" );
        $this->assertEquals( "Test", $exception->getTeam() );
    }
}
