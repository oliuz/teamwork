<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamworkSetupTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( \Config::get( 'teamwork.users_table' ), function ( Blueprint $table )
        {
            $table->unsignedBigInteger( 'current_team_id' )->nullable();
        } );


        Schema::create( \Config::get( 'teamwork.teams_table' ), function ( Blueprint $table )
        {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name')->unique();
            $table->string( 'slug' )->unique();
            $table->timestamps();
        } );

        Schema::create( \Config::get( 'teamwork.team_user_table' ), function ( Blueprint $table )
        {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->timestamps();

            $table->foreign( 'user_id' )
                ->references( \Config::get( 'teamwork.user_foreign_key' ) )
                ->on( \Config::get( 'teamwork.users_table' ) )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );

            $table->foreign( 'team_id' )
                ->references( 'id' )
                ->on( \Config::get( 'teamwork.teams_table' ) )
                ->onDelete( 'cascade' );
        } );

        Schema::create( \Config::get( 'teamwork.team_invites_table' ), function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->enum('type', ['invite', 'request']);
            $table->string('email');
            $table->string('accept_token');
            $table->string('deny_token');
            $table->timestamps();
            $table->foreign( 'team_id' )
                ->references( 'id' )
                ->on( \Config::get( 'teamwork.teams_table' ) )
                ->onDelete( 'cascade' );
            $table->foreign( 'user_id' )
                ->references( 'id' )->on('users')
                ->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\Config::get( 'teamwork.users_table' ), function(Blueprint $table)
        {
            $table->dropColumn('current_team_id');
        });

        Schema::table(\Config::get('teamwork.team_user_table'), function (Blueprint $table) {
            $table->dropForeign(\Config::get('teamwork.team_user_table').'_user_id_foreign');
            $table->dropForeign(\Config::get('teamwork.team_user_table').'_team_id_foreign');
        });

        Schema::drop(\Config::get('teamwork.team_user_table'));
        Schema::drop(\Config::get('teamwork.team_invites_table'));
        Schema::drop(\Config::get('teamwork.teams_table'));

    }
}
