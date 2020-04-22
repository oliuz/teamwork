<?php

namespace Mpociot\Teamwork\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package Mpociot\Teamwork\Tests\Models
 */
class User extends Authenticatable
{
    use \Mpociot\Teamwork\Traits\UserHasTeams;

    protected $fillable = ['id', 'name', 'email'];
}
