<?php

namespace Mpociot\Teamwork\Exceptions;

use RuntimeException;

/**
 * This file is part of Teamwork
 *
 * PHP version 7.2
 *
 * @category PHP
 * @package  Teamwork
 * @author   Marcel Pociot <m.pociot@gmail.com>
 * @license  MIT
 * @link     http://github.com/mpociot/teamwork
 */
class UserNotInTeamException extends RuntimeException
{

    /**
     * Name of the affected team
     *
     * @var string
     */
    protected $team;

    /**
     * Set the affected team
     *
     * @param string $team
     * @return $this
     */
    public function setTeam($team)
    {
        $this->team = $team;

        $this->message = "The user is not in the team {$team}";

        return $this;
    }

    /**
     * Get the affected team.
     *
     * @return string
     */
    public function getTeam()
    {
        return $this->team;
    }
}
