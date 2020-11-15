<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColorToStringInProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('color');
            $table->dropColumn('color_id');
        });

        Schema::dropIfExists('colors');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('colors')) {
            Schema::create('colors', function (Blueprint $table) {
                $table->id();
                $table->string("name");
                $table->string("color_hex");
            });
        }
        if (Schema::hasColumn('projects', 'color')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }

        if (!Schema::hasColumn('projects', 'color_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId("color_id")->constrained("colors");
            });
        }

    }
}
