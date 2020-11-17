<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProjectidToNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn("project_id");
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId("project_id")->nullable()->constrained("projects");
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
            $table->dropColumn("project_id");
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId("project_id")->constrained("projects");
        });
    }
}
