<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTaskColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('priority', ['0', '1', '2', '3', '4', '5'])->default('0')->after('description');
            $table->dropColumn('priority_id');
        });

        Schema::dropIfExists('priorities');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('priorities')) {
            Schema::create('priorities', function (Blueprint $table) {
                $table->id();
                $table->string("name");
            });
        }
        if (Schema::hasColumn('tasks', 'priority')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }

        if (!Schema::hasColumn('tasks', 'priority_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId("priority_id")->constrained("priorities");
            });
        }
    }
}
