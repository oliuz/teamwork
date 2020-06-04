<?php

namespace Mpociot\Teamwork\Traits;

use Illuminate\Support\Facades\Config;

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
trait TeamworkTeamInviteTrait
{
    /**
     * Has-One relations with the team model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne(Config::get('teamwork.team_model'), 'id', 'team_id');
    }

    /**
     * Has-One relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(Config::get('teamwork.user_model'), 'email', 'email');
    }

    /**
     * Has-One relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function inviter()
    {
        return $this->hasOne(Config::get('teamwork.user_model'), 'id', 'user_id');
    }
}
