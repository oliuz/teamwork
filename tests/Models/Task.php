<?php

namespace Mpociot\Teamwork\Tests\Models;

use Mpociot\Teamwork\Traits\UsedByTeams;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Task
 * @package Mpociot\Teamwork\Tests\Models
 */
class Task extends Model
{
    use UsedByTeams;

    /**
     * @var string
     */
    protected $table = 'tasks';

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'team_id'];
}
