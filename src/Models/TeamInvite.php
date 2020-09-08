<?php

namespace Mpociot\Teamwork\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Mpociot\Teamwork\Traits\TeamworkTeamInviteTrait;

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
class TeamInvite extends Model
{
    use TeamworkTeamInviteTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('teamwork.team_invites_table');
    }
}
