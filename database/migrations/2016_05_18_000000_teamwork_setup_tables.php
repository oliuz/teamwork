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
        Schema::table(config('teamwork.users_table'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('teamwork.current_team'))
                ->nullable()
                ->after('id');
        });

        Schema::create(config('teamwork.teams_table'), function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create(config('teamwork.team_user_table'), function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references(config('teamwork.user_foreign_key'))
                ->on(config('teamwork.users_table'))
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on(config('teamwork.teams_table'))
                ->onDelete('cascade');
        });

        Schema::create(config('teamwork.team_invites_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id');
            $table->enum('type', ['invite', 'request']);
            $table->string('email');
            $table->string('accept_token');
            $table->string('deny_token');
            $table->timestamps();
            $table->foreign('team_id')
                ->references('id')
                ->on(config('teamwork.teams_table'))
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('teamwork.users_table'), function (Blueprint $table) {
            $table->dropColumn(config('teamwork.current_team'));
        });

        Schema::table(config('teamwork.team_user_table'), function (Blueprint $table) {
            $table->dropForeign(config('teamwork.team_user_table') . '_user_id_foreign');
            $table->dropForeign(config('teamwork.team_user_table') . '_team_id_foreign');
        });

        Schema::drop(config('teamwork.team_user_table'));
        Schema::drop(config('teamwork.team_invites_table'));
        Schema::drop(config('teamwork.teams_table'));
    }
}
