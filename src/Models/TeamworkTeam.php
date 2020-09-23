<?php

namespace Mpociot\Teamwork\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Mpociot\Teamwork\Traits\TeamworkTeamTrait;

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
class TeamworkTeam extends Model
{
    use TeamworkTeamTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $fillable = ['uuid', 'name', 'owner_id', 'slug'];

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('teamwork.teams_table');
    }

    /**
     *
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string)Uuid::uuid1();
            $model->slug = Str::slug($model->name);
            $model->owner_id = auth()->user()->getKey();
        });

        static::updating(function ($model) {
            $model->uuid = (string)Uuid::uuid1();
            $model->slug = Str::slug($model->name);
        });

        static::deleting(function ($model) {
            $currentTeamId = config('teamwork.current_team');
            $userModel = config('teamwork.user_model');
            $userModel::where($currentTeamId, $model)
                ->update([$currentTeamId => null]);
        });
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return config('teamwork.route_model_binding', 'id');
    }
}
