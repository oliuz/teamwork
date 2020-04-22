<?php

namespace Mpociot\Teamwork\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * This file is part of Teamwork
 *
 * @license MIT
 * @package Teamwork
 */
trait TeamworkTeamInviteTrait
{
    /**
     * Has-One relations with the team model.
     *
     * @return BelongsToMany
     */
    public function team()
    {
        return $this->hasOne(Config::get('teamwork.team_model'), 'id', 'team_id');
    }

    /**
     * Has-One relations with the user model.
     *
     * @return BelongsToMany
     */
    public function user()
    {
        return $this->hasOne(Config::get('teamwork.user_model'), 'email', 'email');
    }

    /**
     * Has-One relations with the user model.
     *
     * @return BelongsToMany
     */
    public function inviter()
    {
        return $this->hasOne(Config::get('teamwork.user_model'), 'id', 'user_id');
//        return $this->hasOne('Mpociot\Teamwork\Tests\Models\User', 'id', 'user_id');
    }
}
