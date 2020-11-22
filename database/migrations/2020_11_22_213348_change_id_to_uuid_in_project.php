<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIdToUuidInProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->uuid('project_id');
            $table->foreign("project_id")->references('id')->on("projects");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn("id");
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->id();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId("project_id")->constrained("projects");
        });
    }
}
